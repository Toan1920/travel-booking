<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class BlogController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ DANH SÁCH & FORM THÊM MỚI
    // ==========================================
    public function index()
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();
        
        $sql = "SELECT b.*, u.full_name as author_name 
                FROM blog_posts b 
                LEFT JOIN users u ON b.author_id = u.id 
                ORDER BY b.created_at DESC";
        $stmt = $db->query($sql);
        $posts = $stmt ? $stmt->fetchAll() : [];

        return $this->view('admin/blog', [
            'pageTitle' => 'Quản lý Tin tức - Admin Panel',
            'posts' => $posts
        ]);
    }

    // ==========================================
    // 2. XỬ LÝ LƯU BÀI VIẾT MỚI (POST)
    // ==========================================
    public function store()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/blog');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $title = sanitize($_POST['title'] ?? '');
        $slug = generateSlug($title);
        $excerpt = sanitize($_POST['excerpt'] ?? ''); 
        $content = $_POST['content'] ?? ''; // Giữ nguyên HTML cho bài viết
        $author_id = $_SESSION['user_id'];

        if (empty($title) || empty($content)) {
            $_SESSION['error'] = 'Tiêu đề và nội dung không được để trống!';
            redirect(SITE_URL . '/admin/blog');
        }

        // Xử lý Upload ảnh
        $imagePath = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $file = $_FILES['featured_image'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imagePath = time() . '_' . uniqid() . '.' . $ext; // Tạo tên file unique
            $destination = UPLOAD_PATH . 'blog/' . $imagePath;
            
            // Đảm bảo thư mục tồn tại
            if (!is_dir(UPLOAD_PATH . 'blog/')) {
                mkdir(UPLOAD_PATH . 'blog/', 0777, true);
            }

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $imagePath = null; // Nếu lỗi upload thì bỏ qua ảnh
            }
        }

        $db = Database::getInstance();
        $sql = "INSERT INTO blog_posts (author_id, title, slug, excerpt, content, featured_image, views, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 0, 'published', NOW())";
        
        if ($db->execute($sql, [$author_id, $title, $slug, $excerpt, $content, $imagePath])) {
            $_SESSION['success'] = 'Đăng bài viết mới thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi lưu vào cơ sở dữ liệu.';
        }

        redirect(SITE_URL . '/admin/blog');
    }

    // ==========================================
    // 3. XỬ LÝ XÓA BÀI VIẾT (POST)
    // ==========================================
    public function destroy()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/blog');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
        if (!$id) redirect(SITE_URL . '/admin/blog');

        $db = Database::getInstance();

        // Lấy tên ảnh cũ để xóa file vật lý
        $stmtGet = $db->query("SELECT featured_image FROM blog_posts WHERE id = ?", [$id]);
        $post = $stmtGet ? $stmtGet->fetch() : null;

        if ($post) {
            // Xóa file ảnh vật lý nếu có
            if (!empty($post['featured_image'])) {
                $file = UPLOAD_PATH . 'blog/' . $post['featured_image'];
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            // Xóa record trong DB
            if ($db->execute("DELETE FROM blog_posts WHERE id = ?", [$id])) {
                $_SESSION['success'] = 'Đã xóa bài viết thành công!';
            } else {
                $_SESSION['error'] = 'Lỗi hệ thống khi xóa bài viết.';
            }
        }

        redirect(SITE_URL . '/admin/blog');
    }
// ==========================================
    // 4. HIỂN THỊ FORM SỬA BÀI VIẾT (GET)
    // ==========================================
    public function edit($id)
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM blog_posts WHERE id = ?", [$id]);
        $post = $stmt ? $stmt->fetch() : null;

        if (!$post) {
            $_SESSION['error'] = 'Bài viết không tồn tại trên hệ thống!';
            redirect(SITE_URL . '/admin/blog');
        }

        return $this->view('admin/blog-edit', [
            'pageTitle' => 'Sửa bài viết: ' . $post['title'] . ' - Admin Panel',
            'post' => $post
        ]);
    }

    // ==========================================
    // 5. XỬ LÝ CẬP NHẬT BÀI VIẾT (POST)
    // ==========================================
    public function update($id)
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/blog');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $db = Database::getInstance();
        
        // 1. Lấy thông tin bài viết cũ để xử lý ảnh
        $stmt = $db->query("SELECT featured_image FROM blog_posts WHERE id = ?", [$id]);
        $post = $stmt ? $stmt->fetch() : null;
        if (!$post) {
            $_SESSION['error'] = 'Bài viết không tồn tại!';
            redirect(SITE_URL . '/admin/blog');
        }

        // 2. Lấy dữ liệu từ Form
        $title = sanitize($_POST['title'] ?? '');
        $slug = generateSlug($title);
        $excerpt = sanitize($_POST['excerpt'] ?? '');
        $content = $_POST['content'] ?? ''; // Giữ nguyên HTML
        $imagePath = $post['featured_image']; // Mặc định giữ lại ảnh cũ

        // 3. Xử lý Upload ảnh mới (nếu có)
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $file = $_FILES['featured_image'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newImage = time() . '_' . uniqid() . '.' . $ext;
            $destination = UPLOAD_PATH . 'blog/' . $newImage;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Upload thành công -> Xóa ảnh cũ đi cho nhẹ server
                if (!empty($post['featured_image'])) {
                    $oldFile = UPLOAD_PATH . 'blog/' . $post['featured_image'];
                    if (file_exists($oldFile)) unlink($oldFile);
                }
                $imagePath = $newImage; // Gán ảnh mới
            }
        }

        // 4. Cập nhật vào DB
        $sql = "UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ? WHERE id = ?";
        if ($db->execute($sql, [$title, $slug, $excerpt, $content, $imagePath, $id])) {
            $_SESSION['success'] = 'Cập nhật bài viết thành công!';
            redirect(SITE_URL . '/admin/blog');
        } else {
            $_SESSION['error'] = 'Lỗi hệ thống khi cập nhật, vui lòng thử lại.';
            redirect(SITE_URL . '/admin/blog/edit/' . $id);
        }
    }
    }