<?php
// Lấy đường dẫn hiện tại để xử lý hiệu ứng "Active" cho menu
$currentUri = $_SERVER['REQUEST_URI'];
$isActive = function($path) use ($currentUri) {
    // Nếu path là /admin và URI chỉ đúng /admin (không chứa thư mục con)
    if ($path === '/admin' && (rtrim($currentUri, '/') === rtrim(parse_url(SITE_URL, PHP_URL_PATH) . '/admin', '/'))) {
        return 'active';
    }
    // Các trang con khác
    if ($path !== '/admin' && strpos($currentUri, $path) !== false) {
        return 'active';
    }
    return '';
};
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --primary-color: #3498DB; --secondary-color: #2C3E50; --white: #ffffff; --light-bg: #f8f9fa; }
        body { background: var(--light-bg); font-family: 'Segoe UI', sans-serif; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; color: #fff; border: none; }
        .alert-success { background-color: #2ecc71; }
        .alert-danger { background-color: #e74c3c; }
        .alert-warning { background-color: #f39c12; }
        
        /* Chỉnh lại 1 chút form search cho gọn trên Bootstrap */
        .topbar-search { display: flex; align-items: center; background: #f1f3f5; padding: 5px 15px; border-radius: 20px;}
        .topbar-search input { border: none; background: transparent; outline: none; margin-left: 10px; width: 250px;}
    </style>
</head>
<body>

<div class="admin-wrapper d-flex">
    <aside class="admin-sidebar bg-dark text-white" id="sidebar" style="width: 250px; min-height: 100vh;">
        <div class="sidebar-header p-3 text-center border-bottom border-secondary">
            <h4 class="mb-0 text-white"><i class="fas fa-plane-departure text-info"></i> TravelVN</h4>
            <small class="text-muted">Administrator Panel</small>
        </div>
        
        <ul class="sidebar-menu list-unstyled p-0 m-0">
            <li class="p-2">
                <a href="<?php echo SITE_URL; ?>/admin" class="text-decoration-none text-white d-block p-2 rounded <?php echo $isActive('/admin'); ?>">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i> Dashboard
                </a>
            </li>
            
            <li class="menu-header text-uppercase fw-bold mt-3 mb-1 px-3" style="color: #bdc3c7; font-size: 11px;">SẢN PHẨM</li>
            
            <li class="px-2 mb-1">
                <a href="<?php echo SITE_URL; ?>/admin/categories" class="text-decoration-none text-white d-block p-2 rounded <?php echo $isActive('/admin/categories'); ?>">
                    <i class="fas fa-folder fa-fw me-2"></i> Danh mục Tour
                </a>
            </li>
            <li class="px-2 mb-1">
                <a href="<?php echo SITE_URL; ?>/admin/tours" class="text-decoration-none text-white d-block p-2 rounded <?php echo $isActive('/admin/tours'); ?>">
                    <i class="fas fa-map-marked-alt fa-fw me-2"></i> Quản lý Tour
                </a>
            </li>
            <li class="px-2 mb-1">
                <a href="<?php echo SITE_URL; ?>/admin/departure-dates" class="text-decoration-none text-white d-block p-2 rounded <?php echo $isActive('/admin/departure-dates'); ?>">
                    <i class="fas fa-calendar-alt fa-fw me-2"></i> Lịch khởi hành
                </a>
            </li>

            <li class="menu-header text-uppercase fw-bold mt-3 mb-1 px-3" style="color: #bdc3c7; font-size: 11px;">KINH DOANH</li>

            <li class="px-2 mb-1">
                <a href="<?php echo SITE_URL; ?>/admin/bookings" class="text-decoration-none text-white d-block p-2 rounded <?php echo $isActive('/admin/bookings'); ?>">
                    <i class="fas fa-shopping-cart fa-fw me-2"></i> Đơn đặt tour
                </a>
            </li>
            <li class="px-2 mb-1">
                <a href="<?php echo SITE_URL; ?>/admin/coupons" class="text-decoration-none text-white d-block p-2 rounded <?php echo $isActive('/admin/coupons'); ?>">
                    <i class="fas fa-ticket-alt fa-fw me-2"></i> Mã giảm giá
                </a>
            </li>
            <li class="px-2 mb-1">
                <a href="<?php echo SITE_URL; ?>/admin/customers" class="text-decoration-none text-white d-block p-2 rounded <?php echo $isActive('/admin/customers'); ?>">
                    <i class="fas fa-users fa-fw me-2"></i> Khách hàng
                </a>
            </li>

            <li class="menu-header text-uppercase fw-bold mt-3 mb-1 px-3" style="color: #bdc3c7; font-size: 11px;">HỆ THỐNG</li>

            <li class="px-2 mb-1">
                <a href="<?php echo SITE_URL; ?>/admin/blog" class="text-decoration-none text-white d-block p-2 rounded <?php echo $isActive('/admin/blog'); ?>">
                    <i class="fas fa-blog fa-fw me-2"></i> Tin tức / Blog
                </a>
            </li>
            <li class="px-2 mb-1">
                <a href="<?php echo SITE_URL; ?>/admin/reviews" class="text-decoration-none text-white d-block p-2 rounded <?php echo $isActive('/admin/reviews'); ?>">
                    <i class="fas fa-star fa-fw me-2"></i> Đánh giá
                </a>
            </li>
            <li class="px-2 mb-1">
                <a href="<?php echo SITE_URL; ?>/admin/settings" class="text-decoration-none text-white d-block p-2 rounded <?php echo $isActive('/admin/settings'); ?>">
                    <i class="fas fa-cogs fa-fw me-2"></i> Cấu hình
                </a>
            </li>
            <li class="px-2 mt-4">
                <a href="<?php echo SITE_URL; ?>/logout" onclick="return confirm('Bạn muốn đăng xuất khỏi hệ thống quản trị?');" class="text-decoration-none text-danger d-block p-2 rounded border border-danger text-center">
                    <i class="fas fa-sign-out-alt fa-fw me-2"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </aside>

    <main class="admin-main flex-grow-1 bg-light">
        <header class="admin-topbar bg-white shadow-sm p-3 d-flex justify-content-between align-items-center mb-4">
            <div class="topbar-search">
                <i class="fas fa-search text-muted"></i>
                <input type="text" placeholder="Tìm kiếm nhanh...">
            </div>
            
            <div class="topbar-actions d-flex align-items-center gap-3">
                <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-sm btn-info text-white">
                    <i class="fas fa-external-link-alt me-1"></i> Xem Website
                </a>
                
                <div class="admin-user d-flex align-items-center gap-2 border-start ps-3">
                    <div class="admin-avatar rounded-circle bg-primary text-white d-flex justify-content-center align-items-center fw-bold" style="width: 35px; height: 35px;">
                        <?php echo isset($_SESSION['full_name']) ? strtoupper(substr($_SESSION['full_name'], 0, 1)) : 'A'; ?>
                    </div>
                    <div class="admin-user-info lh-1">
                        <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></h6>
                        <small class="text-muted">Administrator</small>
                    </div>
                </div>
                
                <div class="topbar-icon d-md-none fs-4" id="sidebarToggle" style="cursor: pointer;">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </header>

        <div class="admin-content px-4">
            
            <?php 
            // HIỂN THỊ THÔNG BÁO FLASH MESSAGE
            if (isset($_SESSION['success'])): 
            ?>
                <div class="alert alert-success shadow-sm">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger shadow-sm">
                    <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
          