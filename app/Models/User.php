<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ==========================================
    // CÁC HÀM XÁC THỰC & TÌM KIẾM
    // ==========================================

    // Lấy thông tin user bằng Email để kiểm tra đăng nhập
    public function getUserByEmail($email)
    {
        $sql = "SELECT id, full_name, email, password, role, status FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->db->query($sql, [$email]);
        
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    // Lấy thông tin user bằng ID (Dùng cho trang Profile sau này)
    public function getUserById($id)
    {
        $sql = "SELECT id, full_name, email, phone, address, role, status FROM users WHERE id = ? LIMIT 1";
        $stmt = $this->db->query($sql, [$id]);
        
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    // ==========================================
    // CÁC HÀM THAO TÁC DỮ LIỆU (INSERT/UPDATE)
    // ==========================================

    // Tạo người dùng mới (Dành cho chức năng Đăng ký)
    public function createUser($data)
    {
        $sql = "INSERT INTO users (full_name, email, password, phone, role, status) 
                VALUES (?, ?, ?, ?, 'user', 'active')";
        
        // Mật khẩu đã được mã hóa từ Controller trước khi truyền vào đây
        return $this->db->execute($sql, [
            $data['full_name'],
            $data['email'],
            $data['password'],
            $data['phone'] ?? null
        ]);
    }
}