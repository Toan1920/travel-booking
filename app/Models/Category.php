<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Category
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Lấy thông tin 1 danh mục theo Slug
    public function getCategoryBySlug($slug)
    {
        $sql = "SELECT id, name FROM categories WHERE slug = ?";
        $stmt = $this->db->query($sql, [$slug]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    // Lấy tất cả danh mục kèm số lượng tour đang active (ĐÃ TỐI ƯU BẰNG LEFT JOIN)
    public function getCategoriesWithTourCount()
    {
        // Thay vì truy vấn lồng (subquery) gây chậm, ta dùng LEFT JOIN + GROUP BY
        $sql = "SELECT c.name, c.slug, COUNT(t.id) as count 
                FROM categories c 
                LEFT JOIN tours t ON c.id = t.category_id AND t.status = 'active' 
                WHERE c.status = 'active' 
                GROUP BY c.id, c.name, c.slug";
                
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // Lấy tất cả danh mục đang active dùng chung cho Header
    public function getAllActiveCategories()
    {
        $sql = "SELECT name, slug, type FROM categories WHERE status = 'active' ORDER BY id ASC";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []; // Đã fix đồng bộ cú pháp PDO
    }
}