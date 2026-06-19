<?php
namespace App\Core; // <-- Dòng này cực kỳ quan trọng, phải là App\Core

use App\Models\Category;
use App\Models\Wishlist;

class Controller
{
    protected function view($viewPath, $data = [])
    {
        // 1. KIỂM TRA PHÂN LUỒNG: CÓ PHẢI LÀ TRANG ADMIN KHÔNG?
        // Nếu tên file view bắt đầu bằng chữ 'admin/' thì đích thị là khu vực Quản trị
        $isAdminPage = (strpos($viewPath, 'admin/') === 0);

        // 2. KHỞI TẠO DỮ LIỆU TOÀN CỤC CƠ BẢN
        $globalData = [
            'metaTitle' => $data['pageTitle'] ?? 'Du Lịch Việt - Trải nghiệm tuyệt vời',
            'metaDesc' => $data['metaDesc'] ?? 'Đặt tour du lịch giá tốt, tour trong nước và quốc tế chất lượng cao.',
            'metaImage' => $data['metaImage'] ?? SITE_URL . '/assets/images/logo-share.jpg'
        ];

        // 3. TỐI ƯU HÓA: CHỈ LẤY DỮ LIỆU DANH MỤC & WISHLIST CHO TRANG KHÁCH HÀNG
        // Khỏi bắt Database làm việc thừa thãi nếu đang ở trong Admin
        if (!$isAdminPage) {
            $categoryModel = new Category();
            $cats = $categoryModel->getAllActiveCategories();
            
            $domesticCats = [];
            $internationalCats = [];
            
            foreach ($cats as $cat) {
                if ($cat['type'] == 'domestic') {
                    $domesticCats[] = $cat;
                } elseif ($cat['type'] == 'international') {
                    $internationalCats[] = $cat;
                }
            }

            $wishlistCount = 0;
            if (isLoggedIn()) {
                $wishlistModel = new Wishlist();
                $wishlistCount = $wishlistModel->getWishlistCountByUser($_SESSION['user_id']);
            }

            // Gắn thêm dữ liệu vào gói Toàn cục
            $globalData['domesticCats'] = $domesticCats;
            $globalData['internationalCats'] = $internationalCats;
            $globalData['wishlistCount'] = $wishlistCount;
        }

        // 4. Gộp dữ liệu toàn cục với dữ liệu riêng của từng trang
        $viewData = array_merge($globalData, $data);
        extract($viewData);

        // 5. ĐỊNH TUYẾN ĐẾN FILE VIEW
        $file = __DIR__ . '/../../views/' . $viewPath . '.blade.php';

        if (file_exists($file)) {
            
            // NẾU LÀ TRANG KHÁCH: Gọi Header Khách Hàng
            if (!$isAdminPage) {
                require_once __DIR__ . '/../../views/layouts/header.blade.php';
            }

            // GỌI NỘI DUNG CHÍNH (Admin tự gọi Header/Footer trong file của nó rồi)
            require_once $file;

            // NẾU LÀ TRANG KHÁCH: Gọi Footer Khách Hàng
            if (!$isAdminPage) {
                require_once __DIR__ . '/../../views/layouts/footer.blade.php';
            }

        } else {
            // Ném lỗi giao diện thay vì dùng die() cứng nhắc
            http_response_code(404);
            die("<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>
                    <h1 style='color:red;'>Lỗi Hệ Thống: 404</h1>
                    <p>Không tìm thấy file giao diện: <strong>{$viewPath}.blade.php</strong></p>
                 </div>");
        }
    }
}