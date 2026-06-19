<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-unlock-alt fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold">Quên mật khẩu?</h3>
                        <p class="text-muted small">Nhập email đăng ký, hệ thống sẽ gửi liên kết khôi phục cho bạn.</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger text-center small"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($message): ?>
                        <div class="alert alert-success text-center">
                            <i class="fas fa-envelope-open-text fa-2x mb-2"></i><br>
                            <span class="small"><?php echo $message; ?></span>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="<?php echo SITE_URL; ?>/forgot-password/submit">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">EMAIL ĐĂNG KÝ</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3 fw-bold shadow-sm hover-top">
                                Gửi liên kết khôi phục
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <a href="<?php echo SITE_URL; ?>/login" class="text-decoration-none small text-muted hover-primary">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại đăng nhập
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-top:hover { transform: translateY(-2px); transition: 0.3s; }
    .hover-primary:hover { color: #0d6efd !important; transition: 0.2s; }
</style>