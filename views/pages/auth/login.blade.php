<div class="auth-page py-5 bg-light" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container d-flex justify-content-center">
        <div class="auth-box card shadow-lg border-0 p-4 p-md-5" style="max-width: 450px; width: 100%; border-radius: 15px;">
            <div class="text-center mb-4">
                <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                <h2 class="fw-bold text-dark h3">Đăng nhập</h2>
                <p class="text-muted small">Chào mừng bạn quay trở lại TravelVN</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center small rounded-3">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo SITE_URL; ?>/login" method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0 ps-0" required 
                               placeholder="Nhập email..." 
                               value="<?php echo isset($oldEmail) ? htmlspecialchars($oldEmail) : ''; ?>">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 ps-0" required placeholder="******">
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label small text-muted cursor-pointer" for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/forgot-password" class="small text-primary text-decoration-none fw-bold">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3 fw-bold rounded-pill shadow-sm">
                    <i class="fas fa-sign-in-alt me-2"></i> Đăng Nhập
                </button>
            </form>
            
            <div class="text-center mt-3 small text-muted">
                Chưa có tài khoản? <a href="<?php echo SITE_URL; ?>/register" class="text-primary text-decoration-none fw-bold">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group-text { border-right: none; }
    .form-control:focus { box-shadow: none; border-color: #dee2e6; }
    .input-group:focus-within { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); border-radius: 0.375rem; }
    .cursor-pointer { cursor: pointer; }
</style>