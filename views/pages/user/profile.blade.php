<div class="container py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center pt-4">
                     <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <h5 class="mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo SITE_URL; ?>/user/dashboard" class="list-group-item list-group-item-action">
                        <i class="fas fa-th-large me-2"></i> Dashboard
                    </a>
                    <a href="<?php echo SITE_URL; ?>/user/profile" class="list-group-item list-group-item-action active">
                        <i class="fas fa-user-circle me-2"></i> Hồ sơ cá nhân
                    </a>
                    <a href="<?php echo SITE_URL; ?>/logout" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-user-edit me-2"></i> Cập nhật thông tin</h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($success): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> <?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo SITE_URL; ?>/user/profile/update">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Email (Không thể đổi)</label>
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Điểm tích lũy</label>
                                <input type="text" class="form-control bg-light" value="<?php echo number_format($user['points'] ?? 0); ?>" disabled>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và tên</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>

                        <hr class="my-4">
                        
                        <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-key me-2"></i>Đổi mật khẩu</h6>
                        <p class="text-muted small">Bỏ trống ô này nếu bạn không muốn thay đổi mật khẩu hiện tại.</p>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Ít nhất 6 ký tự">
                        </div>

                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm hover-top">
                            <i class="fas fa-save me-2"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-top:hover { transform: translateY(-2px); transition: 0.3s; }
</style>