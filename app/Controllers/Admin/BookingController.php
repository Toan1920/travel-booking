<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class BookingController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ DANH SÁCH ĐƠN HÀNG
    // ==========================================
    public function index()
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        $db = Database::getInstance();

        // 1. Nhận tham số Lọc & Tìm kiếm
        $statusFilter = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $keyword = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // 2. Xây dựng câu SQL động
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($statusFilter)) {
            if (in_array($statusFilter, ['paid', 'pending', 'refunded'])) {
                $where .= " AND b.payment_status = ?";
                $params[] = $statusFilter;
            } else {
                $where .= " AND b.booking_status = ?";
                $params[] = $statusFilter;
            }
        }

        if (!empty($keyword)) {
            $where .= " AND (b.booking_code LIKE ? OR b.full_name LIKE ? OR b.phone LIKE ?)";
            // Thêm 3 lần biến keyword cho 3 dấu chấm hỏi
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        // 3. Đếm tổng số bản ghi để phân trang
        $stmtCount = $db->query("SELECT COUNT(*) as total FROM bookings b $where", $params);
        $totalRecords = $stmtCount ? $stmtCount->fetch()['total'] : 0;
        $totalPages = ceil($totalRecords / $limit);

        // 4. Lấy dữ liệu chi tiết
        $sql = "SELECT b.*, t.title as tour_title, dd.departure_date 
                FROM bookings b 
                LEFT JOIN tours t ON b.tour_id = t.id 
                LEFT JOIN departure_dates dd ON b.departure_date_id = dd.id
                $where 
                ORDER BY b.created_at DESC 
                LIMIT $limit OFFSET $offset";
        
        $stmt = $db->query($sql, $params);
        $bookings = $stmt ? $stmt->fetchAll() : [];

        // 5. Định nghĩa mảng trạng thái để View dùng chung
        $sttClass = [
            'new' => 'info',
            'processing' => 'warning',
            'confirmed' => 'success',
            'completed' => 'secondary',
            'cancelled' => 'danger'
        ];
        $sttLabel = [
            'new' => 'Mới',
            'processing' => 'Đang xử lý',
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        // 6. Gửi dữ liệu ra View
        return $this->view('admin/bookings', [
            'pageTitle' => 'Quản lý Đơn đặt tour - Admin Panel',
            'bookings' => $bookings,
            'statusFilter' => $statusFilter,
            'keyword' => $keyword,
            'page' => $page,
            'totalPages' => $totalPages,
            'sttClass' => $sttClass,
            'sttLabel' => $sttLabel
        ]);
    }

// ==========================================
    // 2. HIỂN THỊ CHI TIẾT ĐƠN HÀNG (GET)
    // ==========================================
    public function detail($id)
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();

        $sql = "SELECT b.*, t.title as tour_title, t.slug as tour_slug, dd.departure_date
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN departure_dates dd ON b.departure_date_id = dd.id
                WHERE b.id = ?";
        
        $stmt = $db->query($sql, [$id]);
        $booking = $stmt ? $stmt->fetch() : null;

        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng này!';
            redirect(SITE_URL . '/admin/bookings');
        }

        return $this->view('admin/booking-detail', [
            'pageTitle' => 'Chi tiết Đơn hàng #' . $booking['booking_code'] . ' - Admin Panel',
            'booking' => $booking
        ]);
    }

    // ==========================================
    // 3. XỬ LÝ CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG (POST)
    // ==========================================
    public function update($id)
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/bookings');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die('Lỗi bảo mật: CSRF Token không hợp lệ!');
        }

        $newBookingStatus = sanitize($_POST['booking_status'] ?? '');
        $newPaymentStatus = sanitize($_POST['payment_status'] ?? '');
        $note = sanitize($_POST['admin_note'] ?? '');

        $db = Database::getInstance();
        $pdo = $db->getConnection(); // Lấy đối tượng PDO gốc để dùng Transaction

        // Lấy dữ liệu cũ để so sánh
        $stmtOld = $db->query("SELECT booking_status, departure_date_id, adults, children FROM bookings WHERE id = ?", [$id]);
        $oldData = $stmtOld ? $stmtOld->fetch() : null;

        if (!$oldData) {
            redirect(SITE_URL . '/admin/bookings');
        }

        try {
            $pdo->beginTransaction();

            // A. Cập nhật Booking
            $sqlUpdate = "UPDATE bookings SET booking_status = ?, payment_status = ?, notes = ? WHERE id = ?";
            $db->execute($sqlUpdate, [$newBookingStatus, $newPaymentStatus, $note, $id]);
            
            $totalGuests = $oldData['adults'] + $oldData['children'];

            // B. Logic HOÀN SLOT (Nếu chuyển từ trạng thái khác sang Hủy)
            if ($oldData['booking_status'] !== 'cancelled' && $newBookingStatus === 'cancelled') {
                $sqlRestore = "UPDATE departure_dates SET available_slots = available_slots + ? WHERE id = ?";
                $db->execute($sqlRestore, [$totalGuests, $oldData['departure_date_id']]);
            }
            
            // C. Logic TRỪ SLOT (Nếu khôi phục từ Hủy sang trạng thái khác)
            if ($oldData['booking_status'] === 'cancelled' && $newBookingStatus !== 'cancelled') {
                $sqlDeduct = "UPDATE departure_dates SET available_slots = available_slots - ? WHERE id = ?";
                $db->execute($sqlDeduct, [$totalGuests, $oldData['departure_date_id']]);
            }

            // D. Ghi Log giao dịch nếu Admin xác nhận thanh toán thủ công
            if ($newPaymentStatus === 'paid' && $oldData['payment_status'] !== 'paid') {
                 $txnRef = 'MANUAL_' . $_SESSION['user_id'] . '_' . time();
                 $sqlLog = "INSERT INTO transactions (booking_id, payment_method, transaction_ref, amount, status, description) 
                            VALUES (?, 'manual', ?, 0, 'success', 'Admin xác nhận thanh toán thủ công')";
                 $db->execute($sqlLog, [$id, $txnRef]);
            }

            $pdo->commit();
            $_SESSION['success'] = 'Cập nhật đơn hàng thành công!';

        } catch (\Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
        }
        
        redirect(SITE_URL . '/admin/bookings/detail/' . $id);
    }

}