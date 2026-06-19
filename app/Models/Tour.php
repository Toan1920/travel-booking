<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Tour
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Lấy danh sách Tour nổi bật
    public function getFeaturedTours($limit = 6)
    {
        $sql = "SELECT t.id, t.title, t.slug, t.images, t.price_adult, t.discount_percent, 
                       t.duration, t.destination, t.rating, t.total_reviews, c.name as category_name 
                FROM tours t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE t.featured = 1 AND t.status = 'active' 
                ORDER BY t.created_at DESC LIMIT " . (int)$limit;
        
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Lấy danh sách Tour Flash Sale
    public function getFlashSaleTours($limit = 4)
    {
        $sql = "SELECT t.id, t.title, t.slug, t.images, t.price_adult, t.discount_percent, 
                        t.duration, t.destination, t.rating, t.total_reviews, c.name as category_name 
                 FROM tours t 
                 LEFT JOIN categories c ON t.category_id = c.id 
                 WHERE t.flash_sale = 1 AND t.status = 'active' 
                 ORDER BY t.discount_percent DESC LIMIT " . (int)$limit;
                 
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Xử lý Lọc, Sắp xếp và Phân trang Tour (ĐÃ TỐI ƯU CỘT TRUY VẤN)
    public function getFilteredTours($filters = [], $page = 1, $limit = 9)
    {
        $where = "WHERE t.status = 'active'";
        $params = [];

        // 1. Lọc theo từ khóa
        if (!empty($filters['search'])) {
            $where .= " AND (t.title LIKE ? OR t.destination LIKE ?)";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
        }

        // 2. Lọc theo ID danh mục
        if (!empty($filters['category_id'])) {
            $where .= " AND t.category_id = ?";
            $params[] = $filters['category_id'];
        }

        // 3. Lọc theo khoảng giá
        if (!empty($filters['price_range'])) {
            $ranges = explode('-', $filters['price_range']);
            if (count($ranges) == 2) {
                $where .= " AND t.price_adult BETWEEN ? AND ?";
                $params[] = (float)$ranges[0];
                $params[] = (float)$ranges[1];
            }
        }

        // 4. Sắp xếp
        $orderBy = "ORDER BY t.created_at DESC";
        switch ($filters['sort'] ?? '') {
            case 'price_asc': $orderBy = "ORDER BY t.price_adult ASC"; break;
            case 'price_desc': $orderBy = "ORDER BY t.price_adult DESC"; break;
            case 'name_asc': $orderBy = "ORDER BY t.title ASC"; break;
        }

        // 5. Đếm tổng số bản ghi (để làm phân trang)
        $sqlCount = "SELECT COUNT(t.id) as total FROM tours t $where";
        $stmtCount = $this->db->query($sqlCount, $params);
        $totalRecords = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // 6. Lấy dữ liệu theo trang (Bỏ t.*, chỉ lấy các trường cần thiết cho Card)
        $offset = ($page - 1) * $limit;
        $sql = "SELECT t.id, t.title, t.slug, t.images, t.price_adult, t.discount_percent, 
                       t.duration, t.destination, c.name as cat_name 
                FROM tours t 
                LEFT JOIN categories c ON t.category_id = c.id 
                $where 
                $orderBy 
                LIMIT " . (int)$offset . ", " . (int)$limit;
                
        $stmt = $this->db->query($sql, $params);
        $tours = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        return [
            'tours' => $tours,
            'totalRecords' => $totalRecords,
            'totalPages' => ceil($totalRecords / $limit)
        ];
    }

    // Lấy chi tiết 1 Tour bằng Slug
    public function getTourBySlug($slug)
    {
        // Ở trang chi tiết (chỉ 1 tour), dùng t.* là hợp lý vì ta cần mọi bài viết/HTML
        $sql = "SELECT t.*, c.name as cat_name, c.slug as cat_slug 
                FROM tours t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE t.slug = ? AND t.status = 'active' LIMIT 1";
        $stmt = $this->db->query($sql, [$slug]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    // Lấy lịch khởi hành tương lai
    public function getDepartureDates($tourId)
    {
        $sql = "SELECT id, departure_date, available_slots, price_adult, price_child
                FROM departure_dates 
                WHERE tour_id = ? 
                AND departure_date >= CURDATE() 
                AND status = 'available' 
                AND available_slots > 0
                ORDER BY departure_date ASC LIMIT 10";
        $stmt = $this->db->query($sql, [$tourId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }    
}