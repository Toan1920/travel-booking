<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class CustomerController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ DANH SÁCH & TÌM KIẾM (GET)
    // ==========================================
    public function index()
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();

        // Xử lý Tìm kiếm & Phân trang
        $search = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $where = "WHERE role = 'customer'";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $searchTerm = "%$search%";
            array_push($params, $searchTerm, $searchTerm, $searchTerm);
        }

        // Đếm tổng số khách hàng để phân trang
        $stmtCount = $db->query("SELECT COUNT(*) as total FROM users $where", $params);
        $total = $stmtCount ? $stmtCount->fetch()['total'] : 0;
        $totalPages = ceil($total / $limit);

        // Lấy danh sách chi tiết
        $sql = "SELECT * FROM users $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $stmt = $db->query($sql, $params);
        $customers = $stmt ? $stmt->fetchAll() : [];

        return $this->view('admin/customers', [
            'pageTitle' => 'Quản lý Khách hàng - Admin Panel',
            'customers' => $customers,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    // ==========================================
    // 2. XỬ LÝ XÓA KHÁCH HÀNG (POST)
    // ==========================================
    public function destroy()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/customers');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
        if (!$id) redirect(SITE_URL . '/admin/customers');

        $db = Database::getInstance();

        // KIỂM TRA RÀNG BUỘC: Khách hàng đã có đơn đặt tour chưa?
        $stmtCheck = $db->query("SELECT COUNT(*) as total FROM bookings WHERE user_id = ?", [$id]);
        $hasBooking = $stmtCheck ? $stmtCheck->fetch()['total'] : 0;

        if ($hasBooking > 0) {
            $_SESSION['error'] = 'Không thể xóa khách hàng đã có đơn đặt tour (để bảo lưu lịch sử kế toán). Hãy khóa tài khoản thay vì xóa!';
        } else {
            // Xóa an toàn
            if ($db->execute("DELETE FROM users WHERE id = ?", [$id])) {
                $_SESSION['success'] = 'Đã xóa tài khoản khách hàng thành công.';
            } else {
                $_SESSION['error'] = 'Lỗi hệ thống khi xóa khách hàng.';
            }
        }

        redirect(SITE_URL . '/admin/customers');
    }
}