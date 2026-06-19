<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class CouponController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ DANH SÁCH & FORM TẠO MÃ
    // ==========================================
    public function index()
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM coupons ORDER BY id DESC");
        $coupons = $stmt ? $stmt->fetchAll() : [];

        return $this->view('admin/coupons', [
            'pageTitle' => 'Quản lý Mã giảm giá - Admin Panel',
            'coupons' => $coupons
        ]);
    }

    // ==========================================
    // 2. XỬ LÝ TẠO MÃ MỚI (POST)
    // ==========================================
    public function store()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/coupons');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $db = Database::getInstance();

        // Nhận và làm sạch dữ liệu
        $code = strtoupper(trim(sanitize($_POST['code'] ?? ''))); // Xóa khoảng trắng và in hoa
        $type = sanitize($_POST['type'] ?? 'percent');
        $value = filter_input(INPUT_POST, 'value', FILTER_VALIDATE_FLOAT) ?: 0;
        $min_order = filter_input(INPUT_POST, 'min_order', FILTER_VALIDATE_FLOAT) ?: 0;
        $limit = filter_input(INPUT_POST, 'usage_limit', FILTER_VALIDATE_INT) ?: 100;
        $valid_to = sanitize($_POST['valid_to'] ?? '');

        if (empty($code) || empty($valid_to)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ Mã Code và Hạn sử dụng.';
            redirect(SITE_URL . '/admin/coupons');
        }

        // Kiểm tra mã đã tồn tại chưa
        $stmtCheck = $db->query("SELECT id FROM coupons WHERE code = ?", [$code]);
        if ($stmtCheck && $stmtCheck->rowCount() > 0) {
            $_SESSION['error'] = "Mã giảm giá <strong>{$code}</strong> đã tồn tại trên hệ thống!";
        } else {
            // Thêm mới
            $sql = "INSERT INTO coupons (code, type, value, min_order, usage_limit, valid_to, status) 
                    VALUES (?, ?, ?, ?, ?, ?, 'active')";
            if ($db->execute($sql, [$code, $type, $value, $min_order, $limit, $valid_to])) {
                $_SESSION['success'] = 'Tạo mã giảm giá mới thành công!';
            } else {
                $_SESSION['error'] = 'Lỗi hệ thống khi tạo mã giảm giá.';
            }
        }

        redirect(SITE_URL . '/admin/coupons');
    }

    // ==========================================
    // 3. XỬ LÝ XÓA MÃ (POST)
    // ==========================================
    public function destroy()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/coupons');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
        if ($id) {
            $db = Database::getInstance();
            if ($db->execute("DELETE FROM coupons WHERE id = ?", [$id])) {
                $_SESSION['success'] = 'Đã xóa mã giảm giá thành công!';
            } else {
                $_SESSION['error'] = 'Lỗi hệ thống khi xóa mã giảm giá.';
            }
        }

        redirect(SITE_URL . '/admin/coupons');
    }
}