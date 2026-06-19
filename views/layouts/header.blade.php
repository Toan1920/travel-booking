<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($metaTitle ?? 'TravelVN - Du lịch muôn nơi'); ?></title>
    
    <meta name="description" content="<?php echo htmlspecialchars($metaDesc ?? 'Website đặt tour du lịch uy tín hàng đầu'); ?>">
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">   
    <meta property="og:title" content="<?php echo htmlspecialchars($metaTitle ?? 'TravelVN'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDesc ?? 'Website đặt tour du lịch uy tín hàng đầu'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($metaImage ?? SITE_URL.'/assets/images/logo-share.jpg'); ?>">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>"> 
    
    <style>
        .top-bar .dropdown-menu { z-index: 9999 !important; }
        body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
        .top-bar { background: #f8f9fa; border-bottom: 1px solid #eee; padding: 5px 0; font-size: 14px; }
        .navbar-brand { font-weight: bold; color: #0d6efd !important; font-size: 24px; }
        .nav-link { font-weight: 500; color: #333; transition: color 0.3s; }
        .nav-link:hover { color: #0d6efd; }
        .dropdown-menu { border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px; }
        .wishlist-icon { position: relative; transition: transform 0.2s; }
        .wishlist-icon:hover { transform: scale(1.1); }
        .wishlist-badge { position: absolute; top: -8px; right: -10px; font-size: 10px; padding: 4px 6px; }
        .main-content { min-height: 600px; padding-bottom: 50px; }
        @media (min-width: 992px) {
            .navbar .dropdown:hover .dropdown-menu { display: block; margin-top: 0; }
        }
    </style>
</head>
<body>

    <div class="top-bar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="top-info text-muted">
                <a href="tel:19001000" class="text-decoration-none text-muted me-3"><i class="fas fa-phone-alt"></i> 1900 1000</a>
                <a href="mailto:admin@travel.com" class="text-decoration-none text-muted"><i class="fas fa-envelope"></i> admin@travel.com</a>
            </div>
            <div class="top-auth">
                <?php if (isLoggedIn()): ?>
                    <div class="dropdown d-inline-block">
                        <a href="#" class="text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> Xin chào, <strong><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Bạn'); ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/profile"><i class="fas fa-id-card me-2 text-primary"></i> Hồ sơ cá nhân</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/dashboard"><i class="fas fa-history me-2 text-info"></i> Lịch sử đặt tour</a></li>
                            <?php if (isAdmin()): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger fw-bold" href="<?php echo ADMIN_URL; ?>"><i class="fas fa-cogs me-2"></i> Quản trị Admin</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login" class="text-dark text-decoration-none me-3 fw-bold"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
                    <span class="text-muted">|</span>
                    <a href="<?php echo SITE_URL; ?>/register" class="text-dark text-decoration-none ms-3"><i class="fas fa-user-plus"></i> Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <i class="fas fa-plane-departure text-primary"></i> Travel<span class="text-dark">VN</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>">Trang chủ</a>
                    </li>
                    <li class="nav-item">
        <a class="nav-link" href="<?php echo SITE_URL; ?>/about">Giới thiệu</a>
    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Tour Trong Nước</a>
                        <ul class="dropdown-menu">
                            <?php foreach ($domesticCats ?? [] as $cat): ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/tours?cat=<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Tour Quốc Tế</a>
                        <ul class="dropdown-menu">
                            <?php foreach ($internationalCats ?? [] as $cat): ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/tours?cat=<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/blog">Cẩm nang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/contact">Liên hệ</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <a href="<?php echo SITE_URL; ?>/tours" class="text-dark me-4 h5 mb-0"><i class="fas fa-search"></i></a>
                    
                    <a href="<?php echo SITE_URL; ?>/wishlist" class="text-danger wishlist-icon h5 mb-0 position-relative">
                        <i class="fas fa-heart"></i>
                        <span id="wishlist-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger wishlist-badge" style="display: <?php echo ($wishlistCount > 0) ? 'inline-block' : 'none'; ?>">
                            <?php echo $wishlistCount; ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?> alert-dismissible fade show shadow-sm" role="alert">
                <?php echo $_SESSION['flash_message']['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>
    </div>

    <main class="main-content">