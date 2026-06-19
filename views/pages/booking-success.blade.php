<?php if (!$booking): ?>
    <div class="container py-5 text-center" style="min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <i class="fas fa-search-minus fa-4x text-muted mb-4"></i>
        <div class="alert alert-danger w-100" style="max-width: 500px;">
            <h4 class="mb-0">Không tìm thấy mã đơn hàng này!</h4>
        </div>
        <p class="text-muted">Đơn hàng có thể không tồn tại hoặc bạn nhập sai mã.</p>
        <a href="<?php echo SITE_URL; ?>" class="btn btn-primary rounded-pill px-4 mt-2">Về trang chủ</a>
    </div>
<?php else: ?>
    <div class="container py-5 my-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                    <div class="card-header text-center py-4 <?php echo ($booking['payment_status'] == 'paid') ? 'bg-success' : 'bg-primary'; ?> text-white">
                        <div class="mb-2" style="font-size: 4rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 class="fw-bold">ĐẶT TOUR THÀNH CÔNG!</h2>
                        <p class="mb-0 fs-5">Cảm ơn quý khách đã tin tưởng dịch vụ của chúng tôi.</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        
                        <div class="text-center mb-4">
                            <p class="text-muted">Mã đơn hàng của quý khách là:</p>
                            <h3 class="text-primary fw-bold p-2 bg-light d-inline-block rounded px-4 border border-dashed">
                                #<?php echo htmlspecialchars($booking['booking_code']); ?>
                            </h3>
                        </div>

                        <hr class="border-secondary opacity-25">

                        <div class="row mb-4 align-items-center">
                            <div class="col-md-3 text-center text-md-start mb-3 mb-md-0">
                                <img src="<?php echo $tourImage; ?>" class="img-fluid rounded shadow-sm" style="max-height: 120px; object-fit: cover;" alt="Tour Image">
                            </div>
                            <div class="col-md-9">
                                <h5 class="fw-bold mb-1 text-primary">
                                    <a href="<?php echo SITE_URL; ?>/tour/<?php echo $booking['tour_slug']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($booking['tour_title']); ?>
                                    </a>
                                </h5>
                                <div class="text-muted small mb-2">
                                    <i class="far fa-clock"></i> <?php echo htmlspecialchars($booking['duration']); ?> 
                                    <span class="mx-2">|</span> 
                                    <i class="fas fa-user-friends"></i> <?php echo $booking['adults']; ?> lớn, <?php echo $booking['children']; ?> nhỏ
                                </div>
                            </div>
                        </div>

                        <div class="bg-light p-4 rounded mb-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3">CHI TIẾT THANH TOÁN</h6>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phương thức:</span>
                                <span class="fw-bold text-dark text-uppercase">
                                    <?php 
                                        if($booking['payment_method'] == 'vnpay') echo 'VNPAY QR';
                                        elseif($booking['payment_method'] == 'momo') echo 'Ví MoMo';
                                        elseif($booking['payment_method'] == 'bank_transfer') echo 'Chuyển khoản NH';
                                        else echo 'Tiền mặt';
                                    ?>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Trạng thái:</span>
                                <span>
                                    <?php if($booking['payment_status'] == 'paid'): ?>
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                    <?php endif; ?>
                                </span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between fs-5 fw-bold text-danger">
                                <span>Tổng tiền:</span>
                                <span><?php echo formatCurrency($booking['final_amount']); ?></span>
                            </div>
                        </div>

                        <?php if($booking['payment_method'] == 'bank_transfer' && $booking['payment_status'] == 'pending'): ?>
                        <div class="alert alert-warning">
                            <h6 class="fw-bold"><i class="fas fa-info-circle"></i> Hướng dẫn chuyển khoản:</h6>
                            <ul class="mb-0 small">
                                <li>Ngân hàng: <strong>Vietcombank</strong></li>
                                <li>Số tài khoản: <strong>0123456789</strong></li>
                                <li>Chủ tài khoản: <strong>CONG TY DU LICH VIET</strong></li>
                                <li>Nội dung CK: <strong>THANHTOAN <?php echo $booking['booking_code']; ?></strong></li>
                            </ul>
                            <div class="mt-2 text-danger small fst-italic">* Vé của bạn sẽ được tự động kích hoạt sau khi chúng tôi nhận được thanh toán.</div>
                        </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4 print-hide">
                            <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-secondary px-4 rounded-pill">
                                <i class="fas fa-home"></i> Về trang chủ
                            </a>
                            
                            <?php if(isLoggedIn()): ?>
                                <a href="<?php echo SITE_URL; ?>/user/dashboard" class="btn btn-primary px-4 rounded-pill shadow-sm">
                                    <i class="fas fa-history"></i> Quản lý đơn hàng
                                </a>
                            <?php else: ?>
                                <a href="<?php echo SITE_URL; ?>/login" class="btn btn-primary px-4 rounded-pill shadow-sm">
                                    <i class="fas fa-sign-in-alt"></i> Đăng nhập để theo dõi
                                </a>
                            <?php endif; ?>
                            
                            <button onclick="window.print()" class="btn btn-success px-4 rounded-pill shadow-sm">
                                <i class="fas fa-print"></i> In vé
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
/* Ẩn các nút bấm và header/footer khi in vé */
@media print {
    header, footer, .print-hide { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { background-color: white !important; }
}
</style>