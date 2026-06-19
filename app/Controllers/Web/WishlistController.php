<?php
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    // ==========================================
    // 1. HÀM HIỂN THỊ TRANG YÊU THÍCH (Giao diện)
    // ==========================================
    public function index()
    {
        // Router middleware('auth') đã tự động lo việc check đăng nhập
        $userId = $_SESSION['user_id'];
        $wishlistModel = new Wishlist();
        
        // Lấy dữ liệu từ Model
        $wishlistItems = $wishlistModel->getUserWishlist($userId);

        // Gọi View
        return $this->view('pages/wishlist', [
            'pageTitle' => 'Danh sách yêu thích của bạn',
            'wishlistItems' => $wishlistItems
        ]);
    }

    // ==========================================
    // 2. HÀM XỬ LÝ API (AJAX TOGGLE THÊM/XÓA)
    // ==========================================
    public function toggle()
    {
        // Ép kiểu trả về là JSON để trình duyệt hiểu đây là API
        header('Content-Type: application/json');

        // Vẫn giữ check đăng nhập ở đây vì API trả về JSON chứ không Redirect như Middleware
        if (!isLoggedIn()) {
            echo json_encode([
                'success' => false, 
                'message' => 'Vui lòng đăng nhập để sử dụng tính năng này!', 
                'redirect' => SITE_URL . '/login'
            ]);
            exit();
        }

        // Lọc bảo mật: Ép kiểu dữ liệu bắt buộc phải là số nguyên (INT)
        $tourId = filter_input(INPUT_POST, 'tour_id', FILTER_VALIDATE_INT);
        
        if (!$tourId) {
            echo json_encode([
                'success' => false, 
                'message' => 'Dữ liệu không hợp lệ!'
            ]);
            exit();
        }

        $userId = $_SESSION['user_id'];
        $wishlistModel = new Wishlist();

        try {
            // Giao phó toàn bộ logic DB cho Model xử lý
            $result = $wishlistModel->toggleWishlist($userId, $tourId);
            
            echo json_encode([
                'success' => true, 
                'message' => $result['message'],
                'action' => $result['status'] // 'added' hoặc 'removed'
            ]);

        } catch (\Exception $e) {
            // Ghi log ẩn giấu lỗi thật (bảo mật server), chỉ báo lỗi chung chung ra ngoài
            error_log("Lỗi Wishlist API: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Đã xảy ra lỗi hệ thống, vui lòng thử lại sau.'
            ]);
        }
        
        exit();
    }
}