<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Wishlist
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Lấy danh sách tour yêu thích của một user cụ thể (ĐÃ TỐI ƯU CỘT TRUY VẤN)
    public function getUserWishlist($userId)
    {
        // Loại bỏ t.*, chỉ gắp đúng các cột cần thiết cho giao diện Card
        $sql = "SELECT t.id, t.title, t.slug, t.images, t.price_adult, t.discount_percent, 
                       t.duration, t.destination, t.rating, w.created_at as liked_at 
                FROM wishlists w 
                JOIN tours t ON w.tour_id = t.id 
                WHERE w.user_id = ? 
                ORDER BY w.created_at DESC";
                
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Bật/tắt trạng thái yêu thích (Thêm hoặc Xóa)
    public function toggleWishlist($userId, $tourId)
    {
        // Kiểm tra xem đã có trong danh sách chưa
        $sqlCheck = "SELECT id FROM wishlists WHERE user_id = ? AND tour_id = ?";
        $stmtCheck = $this->db->query($sqlCheck, [$userId, $tourId]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Nếu đã có -> Xóa
            $this->db->execute("DELETE FROM wishlists WHERE id = ?", [$existing['id']]);
            return ['status' => 'removed', 'message' => 'Đã xóa khỏi danh sách yêu thích!'];
        } else {
            // Nếu chưa có -> Thêm
            $this->db->execute("INSERT INTO wishlists (user_id, tour_id) VALUES (?, ?)", [$userId, $tourId]);
            return ['status' => 'added', 'message' => 'Đã thêm vào danh sách yêu thích!'];
        }
    }

    // Đếm tổng số lượng tour trong Wishlist của user
    public function getWishlistCountByUser($userId)
    {
        $sql = "SELECT COUNT(id) as total FROM wishlists WHERE user_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        $res = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null; // Đã chuẩn hóa cú pháp PDO
        return $res['total'] ?? 0;
    }
}