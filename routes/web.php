<?php
// routes/web.php
use App\Controllers\Web\AuthController;
use App\Controllers\Web\WishlistController;
use App\Controllers\Web\HomeController;
use App\Controllers\Web\TourController;
use App\Controllers\Web\BlogController; // <-- BẮT BUỘC PHẢI CÓ DÒNG NÀY
use App\Controllers\Web\BookingController; // Sẽ mở khóa khi làm đến Booking
use App\Controllers\Web\UserController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\BlogController as AdminBlogController; 
// Đặt tên giả (Alias) là AdminBlogController để không bị trùng lặp với BlogController của Web ngoài
use App\Controllers\Admin\BookingController as AdminBookingController;
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\TourController as AdminTourController;
use App\Controllers\Admin\CouponController as AdminCouponController;
use App\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Controllers\Admin\SettingController as AdminSettingController;
use App\Controllers\Admin\ReviewController as AdminReviewController;
use App\Controllers\Admin\DepartureDateController as AdminDepartureDateController;
use App\Controllers\Web\CouponController;
use App\Controllers\Web\PaymentController;
// ==========================================
// 1. TRANG CHUNG (General Pages)
// ==========================================
$router->get('/', [HomeController::class, 'index']);

$router->get('/about', [HomeController::class, 'about']);
// THÊM 2 DÒNG NÀY VÀO DƯỚI CÙNG MỤC 1
$router->get('/contact', [HomeController::class, 'contact']);
$router->post('/contact/submit', [HomeController::class, 'submitContact']);
// ==========================================
// 2. TOUR & WISHLIST
// ==========================================
$router->get('/tours', [TourController::class, 'index']);
$router->get('/tour/{slug}', [TourController::class, 'detail']);

// Đã gắn khiên bảo vệ: Bắt buộc đăng nhập
$router->get('/wishlist', [WishlistController::class, 'index'])->middleware('auth');

// ==========================================
// 3. XÁC THỰC (Authentication)
// ==========================================
// Đăng nhập
$router->get('/login', [AuthController::class, 'showLoginForm'])->middleware('guest');
$router->post('/login', [AuthController::class, 'login'])->middleware('guest');
$router->get('/forgot-password', [AuthController::class, 'showForgotPasswordForm']);
$router->post('/forgot-password/submit', [AuthController::class, 'processForgotPassword']);
// Đăng ký (Đã dọn đường sẵn sàng cho bước tiếp theo)
$router->get('/register', [AuthController::class, 'showRegisterForm'])->middleware('guest');
$router->post('/register', [AuthController::class, 'register'])->middleware('guest');
$router->get('/reset-password', [AuthController::class, 'showResetPasswordForm']);
$router->post('/reset-password/submit', [AuthController::class, 'processResetPassword']);
// Đăng xuất
$router->get('/logout', [AuthController::class, 'logout']);

// ==========================================
// 4. ĐẶT TOUR (Booking) - Dự kiến
// ==========================================
// Đã gắn khiên bảo vệ: Bắt buộc đăng nhập mới được đặt tour

$router->get('/booking', [BookingController::class, 'showBookingForm'])->middleware('auth');
$router->post('/booking/checkout', [BookingController::class, 'checkout'])->middleware('auth');
// THÊM DÒNG NÀY (Không dùng middleware auth vì khách chưa đăng nhập cũng có thể xem mã đơn của họ)
$router->get('/booking-success', [BookingController::class, 'success']);
// ==========================================
// 5. CẨM NANG DU LỊCH (Blog)
// ==========================================
// ==========================================
// 5. CẨM NANG DU LỊCH (Blog)
// ==========================================
$router->get('/blog', [BlogController::class, 'index']);

// ĐÃ SỬA: Đổi từ Regex sang {slug} cho giống hệt như phần Tour
$router->get('/blog/{slug}', [BlogController::class, 'detail']);
// ==========================================
// 6. TRANG CÁ NHÂN KHÁCH HÀNG (User Panel)
// ==========================================
$router->get('/user/dashboard', [UserController::class, 'dashboard'])->middleware('auth');

// THÊM 2 DÒNG NÀY CHO TRANG HỒ SƠ
$router->get('/user/profile', [UserController::class, 'profile'])->middleware('auth');
$router->post('/user/profile/update', [UserController::class, 'updateProfile'])->middleware('auth');
// ==========================================
// 7. HỆ THỐNG QUẢN TRỊ (ADMIN PANEL)
// ==========================================
// Middleware 'auth' đảm bảo người dùng phải đăng nhập
// Controller sẽ đảm nhận việc kiểm tra người đó có phải là Admin hay không
$router->get('/admin', [DashboardController::class, 'index'])->middleware('auth');
$router->get('/admin/blog', [AdminBlogController::class, 'index'])->middleware('auth');
$router->post('/admin/blog/store', [AdminBlogController::class, 'store'])->middleware('auth');
$router->post('/admin/blog/delete', [AdminBlogController::class, 'destroy'])->middleware('auth');
// THÊM 2 DÒNG NÀY CHO CHỨC NĂNG SỬA BÀI VIẾT
$router->get('/admin/blog/edit/{id}', [AdminBlogController::class, 'edit'])->middleware('auth');
$router->post('/admin/blog/update/{id}', [AdminBlogController::class, 'update'])->middleware('auth');
// QUẢN LÝ ĐƠN HÀNG (BOOKINGS)
$router->get('/admin/bookings', [AdminBookingController::class, 'index'])->middleware('auth');
$router->get('/admin/bookings/detail/{id}', [AdminBookingController::class, 'detail'])->middleware('auth');
$router->post('/admin/bookings/update/{id}', [AdminBookingController::class, 'update'])->middleware('auth');
// QUẢN LÝ DANH MỤC
$router->get('/admin/categories', [AdminCategoryController::class, 'index'])->middleware('auth');
$router->post('/admin/categories/store', [AdminCategoryController::class, 'store'])->middleware('auth');
$router->post('/admin/categories/delete', [AdminCategoryController::class, 'destroy'])->middleware('auth');

// ==========================================
// QUẢN LÝ TOUR
// ==========================================
$router->get('/admin/tours', [AdminTourController::class, 'index'])->middleware('auth');
$router->post('/admin/tours/toggle', [AdminTourController::class, 'toggle'])->middleware('auth');
$router->post('/admin/tours/delete', [AdminTourController::class, 'destroy'])->middleware('auth');
// THÊM 2 DÒNG NÀY CHO TÍNH NĂNG SỬA TOUR
$router->get('/admin/tours/edit/{id}', [AdminTourController::class, 'edit'])->middleware('auth');
$router->post('/admin/tours/update/{id}', [AdminTourController::class, 'update'])->middleware('auth');
// THÊM 2 DÒNG NÀY CHO TÍNH NĂNG TẠO TOUR MỚI
$router->get('/admin/tours/create', [AdminTourController::class, 'create'])->middleware('auth');
$router->post('/admin/tours/store', [AdminTourController::class, 'store'])->middleware('auth');
// QUẢN LÝ MÃ GIẢM GIÁ
$router->get('/admin/coupons', [AdminCouponController::class, 'index'])->middleware('auth');
$router->post('/admin/coupons/store', [AdminCouponController::class, 'store'])->middleware('auth');
$router->post('/admin/coupons/delete', [AdminCouponController::class, 'destroy'])->middleware('auth');
// QUẢN LÝ KHÁCH HÀNG
$router->get('/admin/customers', [AdminCustomerController::class, 'index'])->middleware('auth');
$router->post('/admin/customers/delete', [AdminCustomerController::class, 'destroy'])->middleware('auth');
// CẤU HÌNH HỆ THỐNG
$router->get('/admin/settings', [AdminSettingController::class, 'index'])->middleware('auth');
$router->post('/admin/settings/update', [AdminSettingController::class, 'update'])->middleware('auth');
// QUẢN LÝ ĐÁNH GIÁ (REVIEWS)
$router->get('/admin/reviews', [AdminReviewController::class, 'index'])->middleware('auth');
$router->post('/admin/reviews/approve', [AdminReviewController::class, 'approve'])->middleware('auth');
$router->post('/admin/reviews/delete', [AdminReviewController::class, 'destroy'])->middleware('auth');
// ==========================================
// QUẢN LÝ LỊCH KHỞI HÀNH (DEPARTURE DATES)
// ==========================================
$router->get('/admin/departure-dates', [AdminDepartureDateController::class, 'index'])->middleware('auth');
$router->post('/admin/departure-dates/store', [AdminDepartureDateController::class, 'store'])->middleware('auth');
$router->post('/admin/departure-dates/delete', [AdminDepartureDateController::class, 'destroy'])->middleware('auth');
$router->post('/api/wishlist/add', [WishlistController::class, 'toggle']);
$router->post('/api/coupon/check', [CouponController::class, 'check']);
$router->get('/payment/vnpay_create', [PaymentController::class, 'createPayment']);
$router->get('/payment/vnpay_return', [PaymentController::class, 'vnpayReturn']);