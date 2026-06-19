<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class ReviewController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ DANH SÁCH ĐÁNH GIÁ
    // ==========================================
    public function index()
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();
        $sql = "SELECT r.*, t.title as tour_title, u.full_name 
                FROM reviews r 
                LEFT JOIN tours t ON r.tour_id = t.id 
                LEFT JOIN users u ON r.user_id = u.id 
                ORDER BY r.created_at DESC";
        $stmt = $db->query($sql);
        $reviews = $stmt ? $stmt->fetchAll() : [];

        return $this->view('admin/reviews', [
            'pageTitle' => 'Quản lý Đánh giá - Admin Panel',
            'reviews' => $reviews
        ]);
    }

    // ==========================================
    // 2. DUYỆT ĐÁNH GIÁ (POST)
    // ==========================================
    public function approve()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') redirect(SITE_URL . '/admin/reviews');
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) die("Lỗi bảo mật CSRF!");

        $id = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT);
        $tour_id = filter_input(INPUT_POST, 'tour_id', FILTER_VALIDATE_INT);

        if ($id && $tour_id) {
            $db = Database::getInstance();
            if ($db->execute("UPDATE reviews SET status = 'approved' WHERE id = ?", [$id])) {
                $this->updateTourRating($tour_id, $db); // Tính lại điểm Tour
                $_SESSION['success'] = 'Đã duyệt đánh giá thành công!';
            } else {
                $_SESSION['error'] = 'Lỗi hệ thống khi duyệt.';
            }
        }
        redirect(SITE_URL . '/admin/reviews');
    }

    // ==========================================
    // 3. XÓA ĐÁNH GIÁ (POST)
    // ==========================================
    public function destroy()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') redirect(SITE_URL . '/admin/reviews');
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) die("Lỗi bảo mật CSRF!");

        $id = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT);
        $tour_id = filter_input(INPUT_POST, 'tour_id', FILTER_VALIDATE_INT);

        if ($id && $tour_id) {
            $db = Database::getInstance();
            if ($db->execute("DELETE FROM reviews WHERE id = ?", [$id])) {
                $this->updateTourRating($tour_id, $db); // Tính lại điểm Tour sau khi xóa
                $_SESSION['success'] = 'Đã xóa đánh giá thành công!';
            } else {
                $_SESSION['error'] = 'Lỗi hệ thống khi xóa.';
            }
        }
        redirect(SITE_URL . '/admin/reviews');
    }

    // ==========================================
    // HÀM NỘI BỘ: TÍNH LẠI ĐIỂM TOUR
    // ==========================================
    private function updateTourRating($tourId, $db)
    {
        $sql = "SELECT COUNT(*) as total, AVG(rating) as avg_rate 
                FROM reviews 
                WHERE tour_id = ? AND status = 'approved'";
        $stmt = $db->query($sql, [$tourId]);
        $res = $stmt ? $stmt->fetch() : null;
        
        $total = $res['total'] ?? 0;
        $avg = round($res['avg_rate'] ?? 0, 1); // Làm tròn 1 chữ số thập phân
        
        // Cập nhật vào bảng tours
        $sqlUpdate = "UPDATE tours SET rating = ?, total_reviews = ? WHERE id = ?";
        $db->execute($sqlUpdate, [$avg, $total, $tourId]);
    }
}