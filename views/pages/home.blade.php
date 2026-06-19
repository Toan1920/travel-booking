<section class="hero-slider">
    <div class="slide active" style="background-image: url('<?php echo SITE_URL; ?>/assets/images/hero-bg.jpg');">
        <div class="slide-overlay"></div> 
        <div class="slide-content">
            <h1 class="slide-title">Khám Phá Vẻ Đẹp Việt Nam</h1>
            <p class="slide-subtitle">Trọn gói ưu đãi - Trải nghiệm đẳng cấp - Thanh toán an toàn</p>
            <a href="<?php echo SITE_URL; ?>/tours" class="btn btn-primary btn-lg shadow">Đặt Tour Ngay</a>
        </div>
    </div>
</section>

<section class="search-section">
    <div class="container">
        <div class="search-box shadow-lg"> 
            <form action="<?php echo SITE_URL; ?>/tours" method="GET" class="search-form">
                <div class="row g-3 align-items-end"> 
                    <div class="col-md-4">
                        <label class="form-label"><i class="fas fa-map-marker-alt"></i> Điểm đến</label>
                        <input type="text" name="q" placeholder="Bạn muốn đi đâu? (Đà Nẵng, Sapa...)" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fas fa-calendar-alt"></i> Ngày đi</label>
                        <input type="date" name="date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fas fa-wallet"></i> Ngân sách</label>
                        <select name="price" class="form-select form-control">
                            <option value="">Tất cả mức giá</option>
                            <option value="0-5000000">Dưới 5 triệu</option>
                            <option value="5000000-10000000">5 - 10 triệu</option>
                            <option value="10000000-20000000">10 - 20 triệu</option>
                            <option value="20000000-100000000">Trên 20 triệu</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-search w-100">
                            <i class="fas fa-search"></i> Tìm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="features-section py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 feature-item">
                <i class="fas fa-check-circle fa-3x text-primary mb-3"></i>
                <h5>Giá tốt nhất</h5>
                <p class="text-muted">Cam kết giá cạnh tranh nhất thị trường</p>
            </div>
            <div class="col-md-3 feature-item">
                <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                <h5>Hỗ trợ 24/7</h5>
                <p class="text-muted">Luôn sẵn sàng tư vấn mọi lúc mọi nơi</p>
            </div>
            <div class="col-md-3 feature-item">
                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                <h5>Thanh toán an toàn</h5>
                <p class="text-muted">Bảo mật thông tin tuyệt đối</p>
            </div>
            <div class="col-md-3 feature-item">
                <i class="fas fa-star fa-3x text-primary mb-3"></i>
                <h5>Dịch vụ uy tín</h5>
                <p class="text-muted">Hàng ngàn khách hàng hài lòng</p>
            </div>
        </div>
    </div>
</section>

<?php if (count($flashSaleTours) > 0): ?>
<section class="flash-sale-section bg-light py-5">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title text-danger">
                <i class="fas fa-bolt"></i> Flash Sale Giờ Vàng
            </h2>
            <a href="<?php echo SITE_URL; ?>/tours?sale=1" class="btn btn-outline-danger btn-sm">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="row">
            <?php foreach ($flashSaleTours as $tour): 
                $images = json_decode($tour['images'], true);
                $thumb = (!empty($images) && is_array($images)) ? UPLOAD_URL. 'tours/' . $images[0] : SITE_URL . '/assets/images/no-image.png';
                $originalPrice = $tour['price_adult'];
                $salePrice = $originalPrice * (100 - $tour['discount_percent']) / 100;
            ?>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card tour-card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="<?php echo $thumb; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($tour['title']); ?>" style="height: 200px; object-fit: cover;">
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">-<?php echo $tour['discount_percent']; ?>%</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-info text-dark mb-2 w-auto align-self-start"><?php echo htmlspecialchars($tour['category_name']); ?></span>
                        <h5 class="card-title text-truncate">
                            <a href="<?php echo SITE_URL; ?>/tour/<?php echo $tour['slug']; ?>" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($tour['title']); ?>
                            </a>
                        </h5>
                        <div class="small text-muted mb-2">
                            <i class="fas fa-clock"></i> <?php echo htmlspecialchars($tour['duration']); ?> &bull; 
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($tour['destination']); ?>
                        </div>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-decoration-line-through text-muted small"><?php echo formatCurrency($originalPrice); ?></span>
                                    <div class="fw-bold text-danger fs-5"><?php echo formatCurrency($salePrice); ?></div>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/tour/<?php echo $tour['slug']; ?>" class="btn btn-sm btn-danger">Đặt ngay</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (count($featuredTours) > 0): ?>
<section class="featured-tours-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Tour Nổi Bật</h2>
            <p class="text-muted">Những hành trình được yêu thích nhất mùa này</p>
        </div>
        <div class="row">
            <?php foreach ($featuredTours as $tour): 
                 $images = json_decode($tour['images'], true);
                 $thumb = (!empty($images) && is_array($images)) ? UPLOAD_URL. 'tours/' . $images[0] : SITE_URL . '/assets/images/no-image.png';
                 $finalPrice = $tour['price_adult'] * (100 - $tour['discount_percent']) / 100;
            ?>
            <div class="col-md-4 mb-4">
                <div class="card tour-card h-100">
                    <div class="position-relative overflow-hidden">
                        <img src="<?php echo $thumb; ?>" class="card-img-top hover-zoom" alt="<?php echo htmlspecialchars($tour['title']); ?>" style="height: 250px; object-fit: cover;">
                        <?php if ($tour['discount_percent'] > 0): ?>
                            <div class="badge bg-danger position-absolute top-0 end-0 m-3">Giảm <?php echo $tour['discount_percent']; ?>%</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-primary small fw-bold"><?php echo htmlspecialchars($tour['category_name']); ?></span>
                            <div class="text-warning small">
                                <?php 
                                $stars = round($tour['rating']);
                                for ($i = 1; $i <= 5; $i++) echo ($i <= $stars) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; 
                                ?>
                                <span class="text-muted"> (<?php echo $tour['total_reviews']; ?>)</span>
                            </div>
                        </div>
                        
                        <h4 class="card-title h5">
                            <a href="<?php echo SITE_URL; ?>/tour/<?php echo $tour['slug']; ?>" class="text-dark text-decoration-none">
                                <?php echo htmlspecialchars($tour['title']); ?>
                            </a>
                        </h4>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Giá từ:</small>
                                <div class="fw-bold text-primary fs-4"><?php echo formatCurrency($finalPrice); ?></div>
                            </div>
                            <a href="<?php echo SITE_URL; ?>/tour/<?php echo $tour['slug']; ?>" class="btn btn-outline-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/tours" class="btn btn-primary px-5 py-2">Xem Tất Cả Tour</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (count($reviews) > 0): ?>
<section class="reviews-section py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Khách hàng nói gì về chúng tôi?</h2>
        <div class="row">
            <?php foreach ($reviews as $review): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm p-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-weight: bold;">
                                <?php echo strtoupper(substr($review['full_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($review['full_name']); ?></h6>
                                <div class="text-warning small">
                                    <?php for ($i = 1; $i <= 5; $i++) echo ($i <= $review['rating']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                </div>
                            </div>
                        </div>
                        <p class="card-text text-muted fst-italic">"<?php echo nl2br(htmlspecialchars($review['comment'])); ?>"</p>
                        <div class="mt-3 pt-3 border-top small text-primary">
                            <i class="fas fa-map-marked"></i> Tour: <a href="<?php echo SITE_URL . '/tour/' . $review['tour_slug']; ?>"><?php echo htmlspecialchars($review['tour_title']); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-section py-5 bg-primary text-white text-center">
    <div class="container">
        <h2>Đăng ký nhận thông tin khuyến mãi</h2>
        <p class="mb-4">Nhập email để không bỏ lỡ những deal du lịch giá sốc nhất!</p>
        <form class="d-flex justify-content-center" style="max-width: 500px; margin: 0 auto;">
            <input type="email" class="form-control me-2" placeholder="Email của bạn...">
            <button type="submit" class="btn btn-warning">Đăng ký</button>
        </form>
    </div>
</section>