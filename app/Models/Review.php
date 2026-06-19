<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Review
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Lấy các đánh giá mới nhất cho Trang chủ
    public function getLatestReviews($limit = 6)
    {
        $sql = "SELECT r.rating, r.comment, r.created_at, u.full_name, t.title as tour_title, t.slug as tour_slug 
                FROM reviews r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN tours t ON r.tour_id = t.id 
                WHERE r.status = 'approved' 
                ORDER BY r.created_at DESC LIMIT " . (int)$limit;
                
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Lấy đánh giá của một tour cụ thể (ĐÃ TỐI ƯU: Liệt kê rõ cột và gắn Limit bảo vệ)
    public function getReviewsByTourId($tourId, $limit = 50)
    {
        // Loại bỏ r.* để tiết kiệm bộ nhớ, chỉ lấy các trường thực sự hiển thị trên giao diện
        $sql = "SELECT r.id, r.rating, r.comment, r.created_at, u.full_name 
                FROM reviews r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE r.tour_id = ? AND r.status = 'approved' 
                ORDER BY r.created_at DESC LIMIT " . (int)$limit;
                
        $stmt = $this->db->query($sql, [$tourId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}