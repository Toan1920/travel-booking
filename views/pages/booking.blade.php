<div class="container my-5">
    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-edit me-2"></i> Thông tin liên hệ</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="/booking/checkout">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        
                        <input type="hidden" name="tour_id" value="<?php echo $tour['tour_id']; ?>">
                        <input type="hidden" name="departure_date_id" value="<?php echo $tour['date_id']; ?>">
                        <input type="hidden" name="adults" value="<?php echo $adults; ?>">
                        <input type="hidden" name="children" value="<?php echo $children; ?>">
                        
                        <input type="hidden" id="base-order-amount" name="base_amount" value="<?php echo $totalAmount; ?>">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">HỌ VÀ TÊN <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" 
                                       value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['full_name'] ?? '') : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">SỐ ĐIỆN THOẠI <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" 
                                       value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['phone'] ?? '') : ''; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">EMAIL NHẬN VÉ <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['email'] ?? '') : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">GHI CHÚ THÊM</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="VD: Ăn chay, đón sân bay, yêu cầu đặc biệt..."></textarea>
                        </div>
                        
                        <div class="mb-4 bg-light p-3 border rounded">
                            <label class="form-label fw-bold small text-dark"><i class="fas fa-ticket-alt text-primary"></i> ÁP DỤNG MÃ GIẢM GIÁ</label>
                            <div class="input-group">
                                <input type="text" name="coupon_code" id="coupon-code" class="form-control" placeholder="Nhập mã coupon của bạn...">
                                <button class="btn btn-dark" type="button" id="btn-apply-coupon">Áp dụng ngay</button>
                            </div>
                            <div id="coupon-message"></div>
                        </div>

                        <hr>

                        <h5 class="mb-3 fw-bold">Phương thức thanh toán</h5>
                        
                        <div class="payment-methods">
                            <div class="form-check p-3 border rounded mb-2 bg-light cursor-pointer payment-box">
                                <input class="form-check-input mt-1" type="radio" name="payment_method" id="pay_bank" value="bank_transfer" checked>
                                <label class="form-check-label d-block ms-2 cursor-pointer" for="pay_bank">
                                    <strong class="text-dark">Chuyển khoản ngân hàng</strong>
                                    <div class="small text-muted mt-1">Nhận thông tin Tài khoản qua email. Vé sẽ được kích hoạt sau khi xác nhận nhận tiền.</div>
                                </label>
                            </div>

                            <div class="form-check p-3 border rounded mb-2 bg-light cursor-pointer payment-box">
                                <input class="form-check-input mt-1" type="radio" name="payment_method" id="pay_vnpay" value="vnpay">
                                <label class="form-check-label d-block ms-2 cursor-pointer" for="pay_vnpay">
                                    <strong class="text-primary">Thanh toán VNPAY-QR</strong> <span class="badge bg-danger ms-1">Khuyên dùng</span>
                                    <div class="small text-muted mt-1">Quét mã QR qua App ngân hàng, hoặc thanh toán bằng Thẻ ATM, Visa/Mastercard. Mượt mà, an toàn.</div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 py-3 fw-bold shadow mt-4 btn-lg">
                            XÁC NHẬN ĐẶT VÉ <i class="fas fa-check-circle ms-2"></i>
                        </button>
                        <p class="text-center small text-muted mt-3"><i class="fas fa-shield-alt text-success"></i> Thông tin của bạn được bảo mật tuyệt đối.</p>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-0 bg-white sticky-top" style="top: 100px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold">Tóm tắt chuyến đi</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <img src="<?php echo $tourThumb ?? SITE_URL.'/public/assets/images/no-image.png'; ?>" class="rounded me-3 shadow-sm" style="width: 90px; height: 70px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold mb-1 line-clamp-2"><?php echo htmlspecialchars($tour['title']); ?></h6>
                            <small class="text-muted"><i class="far fa-calendar-alt text-primary"></i> <?php echo formatDate($tour['departure_date']); ?></small>
                        </div>
                    </div>
                    
                    <div class="bg-light p-3 rounded mb-3 border">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>Người lớn:</span>
                            <span class="fw-bold"><?php echo $adults; ?> x <?php echo formatCurrency($priceAdult); ?></span>
                        </div>
                        <?php if ($children > 0): ?>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>Trẻ em:</span>
                            <span class="fw-bold"><?php echo $children; ?> x <?php echo formatCurrency($priceChild); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($discount) && $discount > 0): ?>
                        <div class="d-flex justify-content-between mb-2 small text-success fw-bold">
                            <span>Ưu đãi hệ thống:</span>
                            <span>-<?php echo $discount; ?>%</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between fs-4 fw-bold text-danger border-top pt-3">
                        <span>Tổng cộng:</span>
                        <span id="total-price-display"><?php echo formatCurrency($totalAmount); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    /* Hiệu ứng làm nổi bật khi chọn phương thức thanh toán */
    .payment-box { transition: all 0.3s ease; }
    .payment-box:hover { border-color: #3498db !important; background-color: #f8fbff !important; }
</style>