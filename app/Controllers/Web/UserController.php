<?php
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Database;

class UserController extends Controller
{
    // ==========================================
    // 1. TRANG DASHBOARD KHÁCH HÀNG
    // ==========================================
    public function dashboard()
    {
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];

        // 1. Lấy thông tin user
        $stmtUser = $db->query("SELECT full_name, email, phone, points, member_level FROM users WHERE id = ?", [$userId]);
        $user = $stmtUser ? $stmtUser->fetch() : null;

        if (!$user) {
            redirect(SITE_URL . '/login');
        }

        // 2. Lấy lịch sử đặt tour
        $sqlBookings = "SELECT b.*, t.title as tour_title, t.slug as tour_slug 
                        FROM bookings b
                        LEFT JOIN tours t ON b.tour_id = t.id
                        WHERE b.user_id = ? 
                        ORDER BY b.created_at DESC";
        $stmtBookings = $db->query($sqlBookings, [$userId]);
        $bookings = $stmtBookings ? $stmtBookings->fetchAll() : [];

        // 3. Định nghĩa mảng trạng thái
        $sttClass = [
            'new'        => 'secondary', 
            'processing' => 'info',
            'confirmed'  => 'primary', 
            'completed'  => 'success',
            'cancelled'  => 'danger'
        ];
        
        $sttLabel = [
            'new'        => 'Mới', 
            'processing' => 'Đang xử lý',
            'confirmed'  => 'Đã xác nhận', 
            'completed'  => 'Hoàn thành',
            'cancelled'  => 'Đã hủy'
        ];

        return $this->view('pages/user/dashboard', [
            'pageTitle' => 'Dashboard Khách Hàng - TravelVN',
            'user' => $user,
            'bookings' => $bookings,
            'sttClass' => $sttClass,
            'sttLabel' => $sttLabel
        ]);
    }

// ==========================================
    // 2. HIỂN THỊ HỒ SƠ CÁ NHÂN (GET)
    // ==========================================
    public function profile()
    {
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];

        // Tạo CSRF Token bảo mật cho form
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Lấy thông tin user
        $stmtGet = $db->query("SELECT * FROM users WHERE id = ?", [$userId]);
        $user = $stmtGet ? $stmtGet->fetch() : null;

        if (!$user) {
            redirect(SITE_URL . '/login');
        }

        // Nhận thông báo từ session (Flash Message)
        $success = $_SESSION['success'] ?? '';
        $error = $_SESSION['error'] ?? '';
        unset($_SESSION['success'], $_SESSION['error']);

        return $this->view('pages/user/profile', [
            'pageTitle' => 'Hồ sơ cá nhân - TravelVN',
            'user' => $user,
            'success' => $success,
            'error' => $error
        ]);
    }

    // ==========================================
    // 3. XỬ LÝ CẬP NHẬT HỒ SƠ (POST)
    // ==========================================
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/user/profile');
        }

        // Validate CSRF
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật: Token không hợp lệ. Vui lòng tải lại trang!");
        }

        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        
        $fullName = sanitize($_POST['full_name'] ?? '');
        $phone    = sanitize($_POST['phone'] ?? '');
        $newPass  = $_POST['new_password'] ?? '';

        if (empty($fullName) || empty($phone)) {
            $_SESSION['error'] = "Họ tên và số điện thoại không được để trống.";
            redirect(SITE_URL . '/user/profile');
        }

        // 1. Cập nhật thông tin cơ bản
        $sql = "UPDATE users SET full_name = ?, phone = ? WHERE id = ?";
        if ($db->execute($sql, [$fullName, $phone, $userId])) {
            $_SESSION['full_name'] = $fullName; // Cập nhật lại session hiển thị trên Header
            $_SESSION['success'] = "Cập nhật thông tin thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra với máy chủ, vui lòng thử lại.";
            redirect(SITE_URL . '/user/profile');
        }

        // 2. Cập nhật mật khẩu (Nếu có nhập)
        if (!empty($newPass)) {
            if (strlen($newPass) < 6) {
                $_SESSION['error'] = "Mật khẩu mới phải từ 6 ký tự trở lên.";
                unset($_SESSION['success']); // Xóa thông báo thành công trước đó nếu lỗi pass
            } else {
                $hashed = password_hash($newPass, PASSWORD_DEFAULT);
                $sqlPass = "UPDATE users SET password = ? WHERE id = ?";
                if ($db->execute($sqlPass, [$hashed, $userId])) {
                    $_SESSION['success'] .= " Đã đổi mật khẩu mới thành công.";
                }
            }
        }

        // Redirect về lại trang profile để dọn dẹp data POST
        redirect(SITE_URL . '/user/profile');
    }

}