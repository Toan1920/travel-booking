<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class CategoryController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ DANH SÁCH & FORM THÊM
    // ==========================================
    public function index()
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM categories ORDER BY id DESC");
        $categories = $stmt ? $stmt->fetchAll() : [];

        return $this->view('admin/categories', [
            'pageTitle' => 'Quản lý Danh mục Tour - Admin Panel',
            'categories' => $categories
        ]);
    }

    // ==========================================
    // 2. XỬ LÝ THÊM MỚI DANH MỤC (POST)
    // ==========================================
    public function store()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/categories');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $name = sanitize($_POST['name'] ?? '');
        $slug = generateSlug($name);
        $type = sanitize($_POST['type'] ?? 'domestic');
        $desc = sanitize($_POST['description'] ?? '');

        if (empty($name)) {
            $_SESSION['error'] = 'Tên danh mục không được để trống!';
            redirect(SITE_URL . '/admin/categories');
        }

        $db = Database::getInstance();
        $sql = "INSERT INTO categories (name, slug, description, type, status) VALUES (?, ?, ?, ?, 'active')";
        
        if ($db->execute($sql, [$name, $slug, $desc, $type])) {
            $_SESSION['success'] = 'Thêm danh mục mới thành công!';
        } else {
            $_SESSION['error'] = 'Lỗi hệ thống, không thể thêm danh mục.';
        }

        redirect(SITE_URL . '/admin/categories');
    }

    // ==========================================
    // 3. XỬ LÝ XÓA DANH MỤC (POST)
    // ==========================================
    public function destroy()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/categories');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
        if (!$id) redirect(SITE_URL . '/admin/categories');

        $db = Database::getInstance();

        // LOGIC PHÒNG THỦ: Kiểm tra xem có tour nào đang dùng danh mục này không?
        $stmtCheck = $db->query("SELECT COUNT(*) as total FROM tours WHERE category_id = ?", [$id]);
        $count = $stmtCheck ? $stmtCheck->fetch()['total'] : 0;

        if ($count > 0) {
            $_SESSION['error'] = "Không thể xóa! Đang có <strong>{$count} tour</strong> thuộc danh mục này. Hãy xóa hoặc chuyển tour sang danh mục khác trước.";
        } else {
            if ($db->execute("DELETE FROM categories WHERE id = ?", [$id])) {
                $_SESSION['success'] = 'Đã xóa danh mục thành công!';
            } else {
                $_SESSION['error'] = 'Lỗi hệ thống khi xóa danh mục.';
            }
        }

        redirect(SITE_URL . '/admin/categories');
    }
}