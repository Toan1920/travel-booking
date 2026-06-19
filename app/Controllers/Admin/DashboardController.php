<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Kiểm tra quyền Admin (Bảo vệ kép ngoài Middleware)
        if (!isAdmin()) {
            redirect(SITE_URL . '/login');
        }

        $db = Database::getInstance();
        $stats = [
            'revenue' => 0,
            'pending' => 0,
            'tours' => 0,
            'customers' => 0
        ];

        try {
            // Lấy các con số thống kê
            $stmtRev = $db->query("SELECT SUM(final_amount) as total FROM bookings WHERE payment_status = 'paid'");
            $stats['revenue'] = $stmtRev ? ($stmtRev->fetch()['total'] ?? 0) : 0;

            $stmtPen = $db->query("SELECT COUNT(*) as total FROM bookings WHERE booking_status = 'new'");
            $stats['pending'] = $stmtPen ? ($stmtPen->fetch()['total'] ?? 0) : 0;

            $stmtTour = $db->query("SELECT COUNT(*) as total FROM tours WHERE status = 'active'");
            $stats['tours'] = $stmtTour ? ($stmtTour->fetch()['total'] ?? 0) : 0;

            $stmtCus = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
            $stats['customers'] = $stmtCus ? ($stmtCus->fetch()['total'] ?? 0) : 0;

            // Đơn hàng gần đây
            $recentSql = "SELECT b.*, t.title as tour_title 
                          FROM bookings b 
                          LEFT JOIN tours t ON b.tour_id = t.id 
                          ORDER BY b.created_at DESC LIMIT 5";
            $stmtRecent = $db->query($recentSql);
            $recentBookings = $stmtRecent ? $stmtRecent->fetchAll() : [];

            // Dữ liệu biểu đồ (Xử lý mảng ngay tại Controller)
            $chartSql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(final_amount) as revenue 
                         FROM bookings 
                         WHERE payment_status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) 
                         GROUP BY month ORDER BY month ASC";
            $stmtChart = $db->query($chartSql);
            $chartDataRaw = $stmtChart ? $stmtChart->fetchAll() : [];

            $chartLabels = [];
            $chartValues = [];
            foreach ($chartDataRaw as $row) {
                $chartLabels[] = "Thg " . date('m', strtotime($row['month']));
                $chartValues[] = $row['revenue'];
            }

        } catch (\PDOException $e) {
            error_log("Lỗi truy vấn Admin Dashboard: " . $e->getMessage());
            $recentBookings = [];
            $chartLabels = [];
            $chartValues = [];
        }

        return $this->view('admin/dashboard', [
            'pageTitle' => 'Tổng quan hệ thống - Admin Panel',
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'chartLabels' => json_encode($chartLabels), // Chuyển sang JSON để JS dễ đọc
            'chartValues' => json_encode($chartValues)
        ]);
    }
}