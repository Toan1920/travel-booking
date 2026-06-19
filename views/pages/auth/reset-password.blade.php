<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-key fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold">Đặt lại mật khẩu</h3>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger text-center small"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                            <span class="small"><?php echo $success; ?></span>
                        </div>
                        <div class="text-center mt-4">
                            <a href="<?php echo SITE_URL; ?>/login" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Đăng nhập ngay</a>
                            <p class="small text-muted mt-2">Đang tự động chuyển hướng...</p>
                        </div>
                        
                        <script>setTimeout(() => window.location.href = '<?php echo SITE_URL; ?>/login', 3000);</script>
                        
                    <?php elseif ($isValidLink): ?>
                        <form method="POST" action="<?php echo SITE_URL; ?>/reset-password/submit">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">MẬT KHẨU MỚI</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" name="password" class="form-control" required minlength="6" placeholder="Ít nhất 6 ký tự">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">XÁC NHẬN MẬT KHẨU</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-check-circle text-muted"></i></span>
                                    <input type="password" name="confirm_password" class="form-control" required minlength="6" placeholder="Nhập lại mật khẩu mới">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm hover-top">Xác nhận đổi mật khẩu</button>
                        </form>
                    <?php else: ?>
                        <div class="text-center mt-3">
                            <a href="<?php echo SITE_URL; ?>/forgot-password" class="btn btn-outline-primary w-100 py-2 fw-bold shadow-sm">Gửi lại yêu cầu khôi phục</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-top:hover { transform: translateY(-2px); transition: 0.3s; }
</style>