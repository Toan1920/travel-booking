<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class DepartureDateController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ DANH SÁCH & FORM TẠO LỊCH
    // ==========================================
    public function index()
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();
        
        $filterTourId = filter_input(INPUT_GET, 'tour_id', FILTER_VALIDATE_INT) ?: 0;
        
        // 1. Lấy danh sách Tour (Để đổ vào Select box)
        $stmtTours = $db->query("SELECT id, title FROM tours ORDER BY title ASC");
        $tours = $stmtTours ? $stmtTours->fetchAll() : [];

        // 2. Chỉ lấy danh sách ngày của Tour được chọn
        $dates = [];
        if ($filterTourId > 0) {
            $sql = "SELECT * FROM departure_dates WHERE tour_id = ? ORDER BY departure_date ASC";
            $stmt = $db->query($sql, [$filterTourId]);
            $dates = $stmt ? $stmt->fetchAll() : [];
        }

        return $this->view('admin/departure-dates', [
            'pageTitle' => 'Quản lý Lịch khởi hành - Admin Panel',
            'dates' => $dates,
            'tours' => $tours,
            'filterTourId' => $filterTourId
        ]);
    }

   // ==========================================
    // 2. XỬ LÝ THÊM LỊCH MỚI CÓ GIÁ TÙY CHỈNH (POST)
    // ==========================================
    public function store()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/departure-dates');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        // 1. Lấy dữ liệu (Chỉ dùng trim() cho ngày tháng để giữ nguyên định dạng YYYY-MM-DD)
        $tour_id = filter_input(INPUT_POST, 'tour_id', FILTER_VALIDATE_INT);
        $departure_date = trim($_POST['departure_date'] ?? '');
        $available_slots = filter_input(INPUT_POST, 'available_slots', FILTER_VALIDATE_INT) ?: 20;
        
        // 2. Xử lý giá tiền (Loại bỏ dấu phẩy/khoảng trắng nếu người dùng gõ sai định dạng)
        $priceAdultRaw = str_replace([',', ' '], '', trim($_POST['price_adult'] ?? ''));
        $priceChildRaw = str_replace([',', ' '], '', trim($_POST['price_child'] ?? ''));
        
      $price_adult = ($priceAdultRaw !== '') ? (float)$priceAdultRaw : 0;
$price_child = ($priceChildRaw !== '') ? (float)$priceChildRaw : 0;

        if (!$tour_id || empty($departure_date)) {
            $_SESSION['error'] = 'Vui lòng chọn Tour và nhập ngày khởi hành!';
            redirect(SITE_URL . '/admin/departure-dates' . ($tour_id ? "?tour_id=$tour_id" : ''));
            return; // Đảm bảo dừng thực thi
        }

        try {
            $db = Database::getInstance();
            // Tạm thời lấy kết nối PDO gốc để có thể "bắt" được lỗi chi tiết từ MySQL
            $pdo = $db->getConnection(); 

            // Kiểm tra trùng ngày
            $stmtCheck = $pdo->prepare("SELECT id FROM departure_dates WHERE tour_id = ? AND departure_date = ?");
            $stmtCheck->execute([$tour_id, $departure_date]);
            
            if ($stmtCheck->rowCount() > 0) {
                $_SESSION['error'] = 'Tour này đã có lịch khởi hành vào ngày ' . date('d/m/Y', strtotime($departure_date)) . '!';
            } else {
                $sql = "INSERT INTO departure_dates (tour_id, departure_date, available_slots, price_adult, price_child, status) 
                        VALUES (?, ?, ?, ?, ?, 'available')";
                
                $stmtInsert = $pdo->prepare($sql);
                if ($stmtInsert->execute([$tour_id, $departure_date, $available_slots, $price_adult, $price_child])) {
                    $_SESSION['success'] = 'Thêm lịch khởi hành thành công!';
                } else {
                    $_SESSION['error'] = 'Lỗi hệ thống khi thêm ngày.';
                }
            }
        } catch (\PDOException $e) {
            // NÂNG CẤP: Nếu Database lỗi, nó sẽ in thẳng nguyên nhân ra màn hình cho cụ thấy!
            $_SESSION['error'] = 'Lỗi Database: ' . $e->getMessage();
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Lỗi chung: ' . $e->getMessage();
        }

        redirect(SITE_URL . '/admin/departure-dates' . ($tour_id ? "?tour_id=$tour_id" : ''));
    }

    // ==========================================
    // 3. XỬ LÝ XÓA LỊCH (POST)
    // ==========================================
    public function destroy()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/departure-dates');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
        if (!$id) redirect(SITE_URL . '/admin/departure-dates');

        $db = Database::getInstance();

        $stmtCheck = $db->query("SELECT COUNT(*) as total FROM bookings WHERE departure_date_id = ?", [$id]);
        $hasBooking = $stmtCheck ? $stmtCheck->fetch()['total'] : 0;

        if ($hasBooking > 0) {
            $_SESSION['error'] = 'Không thể xóa! Đã có khách hàng đặt tour vào ngày này.';
        } else {
            if ($db->execute("DELETE FROM departure_dates WHERE id = ?", [$id])) {
                $_SESSION['success'] = 'Đã xóa lịch khởi hành thành công!';
            } else {
                $_SESSION['error'] = 'Lỗi hệ thống khi xóa.';
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? SITE_URL . '/admin/departure-dates';
        redirect($referer);
    }
}