<?php include __DIR__ . '/includes/header.blade.php'; ?>

<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h3 class="fw-bold mb-0"><i class="fas fa-cogs me-2 text-primary"></i> Cấu hình hệ thống</h3>
</div>

<form method="POST" action="<?php echo SITE_URL; ?>/admin/settings/update">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i> Thông tin chung Website</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên Website</label>
                            <input type="text" name="site_name" class="form-control fw-bold text-primary" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Đơn vị tiền tệ hiển thị</label>
                            <input type="text" name="currency" class="form-control" value="<?php echo htmlspecialchars($settings['currency'] ?? 'VND'); ?>" placeholder="VD: VND, USD">
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email liên hệ (Hotmail)</label>
                            <input type="email" name="site_email" class="form-control" value="<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại Hotline</label>
                            <input type="text" name="site_phone" class="form-control" value="<?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-share-alt me-2 text-primary"></i> Liên kết Mạng xã hội (Footer)</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary"><i class="fab fa-facebook me-1"></i> Facebook Fanpage Link</label>
                        <input type="url" name="social_facebook" class="form-control" value="<?php echo htmlspecialchars($settings['social_facebook'] ?? ''); ?>" placeholder="https://facebook.com/...">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-danger"><i class="fab fa-youtube me-1"></i> Youtube Channel Link</label>
                        <input type="url" name="social_youtube" class="form-control" value="<?php echo htmlspecialchars($settings['social_youtube'] ?? ''); ?>" placeholder="https://youtube.com/...">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-shield-alt fa-3x text-success opacity-50 mb-3"></i>
                        <h6 class="fw-bold">Lưu ý bảo mật</h6>
                        <p class="small text-muted mb-0">Các thay đổi tại đây sẽ được áp dụng ngay lập tức trên toàn bộ giao diện của người dùng ngoài hệ thống.</p>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase shadow-sm">
                        <i class="fas fa-save me-2"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.blade.php'; ?>