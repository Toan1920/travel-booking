<?php
// routes/api.php

use App\Controllers\Web\WishlistController;

// ==========================================
// CÁC API DÀNH CHO AJAX / FETCH (JSON RESPONSE)
// ==========================================

// API Thêm/Xóa danh sách yêu thích
// Sử dụng phương thức POST vì dữ liệu thay đổi trạng thái (Database)
$router->post('/api/wishlist/add', [WishlistController::class, 'add']);

// Bạn có thể mở rộng các API khác ở đây sau này
// VD: Kiểm tra mã giảm giá trực tiếp không cần load lại trang
// $router->post('/api/coupon/check', [CouponController::class, 'check']);