</main> 

<footer class="bg-dark text-light pt-5 pb-2 mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="text-uppercase fw-bold text-primary mb-3">
                    <i class="fas fa-plane-departure"></i> TravelVN
                </h5>
                <p class="small text-muted">
                    Chúng tôi cung cấp những tour du lịch chất lượng hàng đầu, đem lại trải nghiệm tuyệt vời và kỷ niệm khó quên cho mọi hành trình của bạn.
                </p>
                <ul class="list-unstyled text-small text-muted">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Đường ABC, Quận 1, TP.HCM</li>
                    <li class="mb-2"><i class="fas fa-phone me-2"></i> <a href="tel:19001234" class="text-muted text-decoration-none">1900 1234</a></li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i> <a href="mailto:contact@travelvn.com" class="text-muted text-decoration-none">contact@travelvn.com</a></li>
                </ul>
            </div>

            <div class="col-md-2 col-6 mb-4">
                <h5 class="text-uppercase fw-bold mb-3 h6">Về chúng tôi</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/about" class="text-decoration-none text-muted hover-white">Giới thiệu</a></li>
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/contact" class="text-decoration-none text-muted hover-white">Liên hệ</a></li>
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/blog" class="text-decoration-none text-muted hover-white">Tin tức & Cẩm nang</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-white">Tuyển dụng</a></li>
                </ul>
            </div>

            <div class="col-md-2 col-6 mb-4">
                <h5 class="text-uppercase fw-bold mb-3 h6">Hỗ trợ</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-white">Câu hỏi thường gặp</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-white">Chính sách bảo mật</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-white">Điều khoản sử dụng</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-white">Hướng dẫn đặt tour</a></li>
                </ul>
            </div>

            <div class="col-md-4 mb-4">
                <h5 class="text-uppercase fw-bold mb-3 h6">Đăng ký nhận tin</h5>
                <p class="small text-muted">Nhận thông tin khuyến mãi mới nhất từ chúng tôi.</p>
                <form action="#" method="POST" class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email của bạn" aria-label="Email" required>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
                
                <h6 class="text-uppercase fw-bold mb-2 h6 mt-4">Kết nối với chúng tôi</h6>
                <div class="d-flex gap-3 social-icons">
                    <a href="#" class="text-light fs-5 transition-hover"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-light fs-5 transition-hover"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="text-light fs-5 transition-hover"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light fs-5 transition-hover"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0 small text-muted">
                    &copy; <?php echo date('Y'); ?> <strong>TravelVN</strong>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end payment-icons">
                <i class="fab fa-cc-visa fa-2x text-muted mx-1"></i>
                <i class="fab fa-cc-mastercard fa-2x text-muted mx-1"></i>
                <i class="fab fa-cc-paypal fa-2x text-muted mx-1"></i>
                <i class="fas fa-wallet fa-2x text-muted mx-1"></i> 
            </div>
        </div>
    </div>
</footer>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="systemToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="toastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<button type="button" class="btn btn-primary btn-lg rounded-circle shadow" id="btn-back-to-top" style="position: fixed; bottom: 30px; right: 30px; display: none; z-index: 1000; width: 50px; height: 50px;">
    <i class="fas fa-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo SITE_URL; ?>/assets/js/main.js?v=<?php echo time(); ?>"></script>

<script>
    const SITE_URL = "<?php echo SITE_URL; ?>"; 

    // 1. Tự động tắt Flash Message sau 4 giây
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 4000);
        });
    });

    // 2. Xử lý nút Back to Top
    const btnBackToTop = document.getElementById("btn-back-to-top");
    window.onscroll = function () {
        if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
            btnBackToTop.style.display = "block";
        } else {
            btnBackToTop.style.display = "none";
        }
    };
    btnBackToTop.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // 3. Hàm hiển thị Toast Notification
    function showToast(message, isSuccess = true) {
        const toastEl = document.getElementById('systemToast');
        const toastBody = document.getElementById('toastMessage');
        
        // Đổi màu nền dựa trên trạng thái
        toastEl.className = `toast align-items-center text-white border-0 ${isSuccess ? 'bg-success' : 'bg-danger'}`;
        toastBody.innerHTML = isSuccess ? `<i class="fas fa-check-circle me-1"></i> ${message}` : `<i class="fas fa-exclamation-triangle me-1"></i> ${message}`;
        
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    }

    // 4. Xử lý Wishlist chuẩn SPA (Không reload trang)
    function addToWishlist(tourId) {
        const formData = new FormData();
        formData.append('tour_id', tourId);

        fetch(SITE_URL + '/api/wishlist/add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hiển thị thông báo góc màn hình
                showToast(data.message, true);
                
                // Cập nhật số lượng trên icon Header (Giả sử bạn có id="wishlist-count" ở Header)
                const badge = document.getElementById('wishlist-count');
                if(badge) {
                    let currentCount = parseInt(badge.innerText) || 0;
                    if(data.action === 'added') {
                        badge.innerText = currentCount + 1;
                    } else if(data.action === 'removed') {
                        badge.innerText = Math.max(0, currentCount - 1);
                    }
                }
                
                // (Tùy chọn) Đổi icon trái tim nếu đang ở trang chi tiết
                const btnIcon = document.querySelector(`button[onclick="addToWishlist(${tourId})"] i`);
                if(btnIcon) {
                    btnIcon.classList.toggle('far'); // Tim rỗng
                    btnIcon.classList.toggle('fas'); // Tim đặc
                }

            } else {
                showToast(data.message, false);
                if(data.redirect) {
                    setTimeout(() => window.location.href = data.redirect, 1500);
                }
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            showToast("Lỗi kết nối server!", false);
        });
    }
</script>

</body>
</html>