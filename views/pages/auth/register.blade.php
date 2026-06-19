<div class="auth-page py-5 bg-light" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container d-flex justify-content-center">
        <div class="auth-box card shadow-lg border-0 p-4 p-md-5" style="max-width: 500px; width: 100%; border-radius: 15px;">
            <div class="text-center mb-4">
                <i class="fas fa-user-plus fa-4x text-success mb-3"></i>
                <h2 class="fw-bold text-dark h3">Tạo tài khoản</h2>
                <p class="text-muted small">Tham gia cùng hàng ngàn khách hàng của TravelVN</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center small rounded-3">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo SITE_URL; ?>/register" method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Họ và tên <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" name="full_name" class="form-control border-start-0 ps-0" required 
                               placeholder="Nhập họ tên..." 
                               value="<?php echo isset($oldData['full_name']) ? htmlspecialchars($oldData['full_name']) : ''; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0 ps-0" required 
                               placeholder="Nhập email..." 
                               value="<?php echo isset($oldData['email']) ? htmlspecialchars($oldData['email']) : ''; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Số điện thoại</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-phone text-muted"></i></span>
                        <input type="text" name="phone" class="form-control border-start-0 ps-0" 
                               placeholder="Nhập số điện thoại..." 
                               value="<?php echo isset($oldData['phone']) ? htmlspecialchars($oldData['phone']) : ''; ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small text-secondary">Mật khẩu <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0" required placeholder="******">
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small text-secondary">Xác nhận <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-check-circle text-muted"></i></span>
                            <input type="password" name="confirm_password" class="form-control border-start-0 ps-0" required placeholder="******">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 mb-3 fw-bold rounded-pill shadow-sm">
                    <i class="fas fa-user-check me-2"></i> Đăng Ký Ngay
                </button>
            </form>
            
            <div class="text-center mt-3 small text-muted">
                Đã có tài khoản? <a href="<?php echo SITE_URL; ?>/login" class="text-success text-decoration-none fw-bold">Đăng nhập</a>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group-text { border-right: none; }
    .form-control:focus { box-shadow: none; border-color: #dee2e6; }
    .input-group:focus-within { box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25); border-radius: 0.375rem; }
</style>