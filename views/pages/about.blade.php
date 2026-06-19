<div class="bg-primary text-white py-5 text-center" style="background: linear-gradient(rgba(13, 110, 253, 0.9), rgba(13, 110, 253, 0.8)), url('<?php echo SITE_URL; ?>/assets/images/hero-bg.jpg') center/cover;">
    <div class="container">
        <h1 class="fw-bold display-4">Về TravelVN</h1>
        <p class="lead mb-0">Hành trình vạn dặm bắt đầu từ một bước chân</p>
    </div>
</div>

<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="position-relative">
                <img src="<?php echo SITE_URL; ?>/assets/images/about-1.jpg" class="img-fluid rounded shadow-lg" alt="Về chúng tôi" onerror="this.src='https://via.placeholder.com/600x400?text=TravelVN'">
                <div class="position-absolute bottom-0 start-0 bg-white p-3 m-3 rounded shadow d-none d-lg-block">
                    <h5 class="fw-bold text-primary mb-0">Since 2023</h5>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h2 class="mb-3 fw-bold text-dark">Câu chuyện của chúng tôi</h2>
            <p class="text-muted lead">TravelVN sinh ra với sứ mệnh kết nối con người với những vùng đất mới.</p>
            <p>Được thành lập vào năm 2023, chúng tôi tin rằng du lịch không chỉ là đi chơi, mà là trải nghiệm văn hóa, ẩm thực và con người. Mỗi chuyến đi là một câu chuyện riêng biệt, và chúng tôi ở đây để giúp bạn viết nên câu chuyện đẹp nhất của mình.</p>
            <p>Với đội ngũ hướng dẫn viên chuyên nghiệp và tận tâm, chúng tôi cam kết mang đến cho bạn những hành trình an toàn, thú vị và đáng nhớ nhất.</p>
            
            <div class="mt-4">
                <a href="<?php echo SITE_URL; ?>/contact" class="btn btn-outline-primary me-2">Liên hệ ngay</a>
                <a href="<?php echo SITE_URL; ?>/tours" class="btn btn-primary">Xem Tour</a>
            </div>
        </div>
    </div>

    <div class="row text-center bg-light py-5 rounded mb-5">
        <div class="col-md-4 mb-4 mb-md-0">
            <h2 class="fw-bold text-primary display-5"><?php echo $stats['tours'] + 50; ?>+</h2> 
            <p class="text-muted mb-0">Tour du lịch đa dạng</p>
        </div>
        <div class="col-md-4 mb-4 mb-md-0">
            <h2 class="fw-bold text-primary display-5"><?php echo $stats['customers'] + 100; ?>+</h2>
            <p class="text-muted mb-0">Khách hàng hài lòng</p>
        </div>
        <div class="col-md-4">
            <h2 class="fw-bold text-primary display-5"><?php echo $stats['reviews'] + 200; ?>+</h2>
            <p class="text-muted mb-0">Đánh giá 5 sao</p>
        </div>
    </div>

    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="p-4 border rounded h-100 hover-shadow transition-all">
                <div class="d-inline-block p-3 bg-primary bg-opacity-10 rounded-circle mb-3">
                    <i class="fas fa-medal fa-2x text-primary"></i>
                </div>
                <h4>Chất lượng hàng đầu</h4>
                <p class="text-muted">Cam kết dịch vụ chuẩn 5 sao, đối tác khách sạn và nhà xe tin cậy.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="p-4 border rounded h-100 hover-shadow transition-all">
                <div class="d-inline-block p-3 bg-success bg-opacity-10 rounded-circle mb-3">
                    <i class="fas fa-dollar-sign fa-2x text-success"></i>
                </div>
                <h4>Giá cả tốt nhất</h4>
                <p class="text-muted">Luôn có ưu đãi hấp dẫn và tích điểm cho khách hàng thân thiết.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="p-4 border rounded h-100 hover-shadow transition-all">
                <div class="d-inline-block p-3 bg-warning bg-opacity-10 rounded-circle mb-3">
                    <i class="fas fa-headset fa-2x text-warning"></i>
                </div>
                <h4>Hỗ trợ 24/7</h4>
                <p class="text-muted">Đội ngũ CSKH luôn bên cạnh bạn trên mọi nẻo đường, bất kể ngày đêm.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; transform: translateY(-5px); }
    .transition-all { transition: all 0.3s ease; }
</style>