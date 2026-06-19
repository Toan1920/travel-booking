<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class SettingController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ TRANG CẤU HÌNH (GET)
    // ==========================================
    public function index()
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();
        
        // Lấy tất cả cài đặt và chuyển thành mảng dạng [key => value]
        $stmt = $db->query("SELECT * FROM settings");
        $settingsRaw = $stmt ? $stmt->fetchAll() : [];
        
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $this->view('admin/settings', [
            'pageTitle' => 'Cấu hình hệ thống - Admin Panel',
            'settings' => $settings
        ]);
    }

    // ==========================================
    // 2. XỬ LÝ LƯU CẤU HÌNH (POST)
    // ==========================================
    public function update()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/settings');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $db = Database::getInstance();
        $pdo = $db->getConnection();

        // 1. Loại bỏ các trường không được phép lưu vào Database
        $postData = $_POST;
        unset($postData['csrf_token']); // Loại bỏ token bảo mật
        
        // Có thể unset thêm nếu form có nút submit mang name
        // unset($postData['submit']);

        try {
            $pdo->beginTransaction();

            $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
            $stmt = $pdo->prepare($sql);

            // 2. Lặp qua các dữ liệu an toàn và lưu
            foreach ($postData as $key => $value) {
                // Làm sạch thẻ HTML nguy hiểm trước khi lưu
                $cleanValue = strip_tags(trim($value)); 
                $stmt->execute([$cleanValue, $key]);
            }

            $pdo->commit();
            $_SESSION['success'] = 'Đã lưu cấu hình hệ thống thành công!';
            
        } catch (\Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Lỗi hệ thống khi lưu cấu hình: ' . $e->getMessage();
        }

        redirect(SITE_URL . '/admin/settings');
    }
}