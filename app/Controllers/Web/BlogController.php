<?php
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Database;

class BlogController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();

        // 1. Xử lý phân trang
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        if ($page < 1) $page = 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        // 2. Đếm tổng số bài viết
        $stmtCount = $db->query("SELECT COUNT(id) as total FROM blog_posts WHERE status = 'published'");
        $totalPosts = $stmtCount ? $stmtCount->fetch()['total'] : 0;
        $totalPages = ceil($totalPosts / $limit);

        // 3. Lấy danh sách bài viết
        $sql = "SELECT b.*, u.full_name as author 
                FROM blog_posts b 
                LEFT JOIN users u ON b.author_id = u.id 
                WHERE b.status = 'published' 
                ORDER BY b.created_at DESC 
                LIMIT $limit OFFSET $offset";

        $stmt = $db->query($sql);
        $posts = $stmt ? $stmt->fetchAll() : [];

        return $this->view('pages/blog', [
            'pageTitle' => 'Cẩm nang du lịch - TravelVN',
            'posts' => $posts,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
// ==========================================
    // 2. TRANG CHI TIẾT BÀI VIẾT
    // ==========================================
    public function detail($slug)
    {
        $slug = sanitize($slug);
        if (empty($slug)) {
            redirect(SITE_URL . '/blog');
        }

        $db = Database::getInstance();

        // 1. Lấy chi tiết bài viết
        $sql = "SELECT b.*, u.full_name 
                FROM blog_posts b 
                LEFT JOIN users u ON b.author_id = u.id 
                WHERE b.slug = ? AND b.status = 'published'";
        $stmt = $db->query($sql, [$slug]);
        $post = $stmt ? $stmt->fetch() : null;

        if (!$post) {
            redirect(SITE_URL . '/blog');
        }

        // 2. Tăng view (Dùng hàm execute thay vì query cho lệnh UPDATE)
        $db->execute("UPDATE blog_posts SET views = views + 1 WHERE id = ?", [$post['id']]);

        // 3. Lấy bài viết mới nhất khác
        $sqlRelated = "SELECT title, slug, featured_image, created_at 
                       FROM blog_posts 
                       WHERE status = 'published' AND id != ? 
                       ORDER BY created_at DESC LIMIT 3";
        $stmtRelated = $db->query($sqlRelated, [$post['id']]);
        $relatedPosts = $stmtRelated ? $stmtRelated->fetchAll() : [];

        // Xử lý ảnh
        $imgUrl = !empty($post['featured_image']) ? UPLOAD_URL . 'blog/' . $post['featured_image'] : '';

        return $this->view('pages/blog-detail', [
            'pageTitle' => $post['title'] . ' - TravelVN',
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'imgUrl' => $imgUrl
        ]);
    }
}