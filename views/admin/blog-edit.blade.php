<?php include 'includes/header.blade.php'; ?>

<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h3 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-primary"></i> Sửa bài viết</h3>
    <a href="<?php echo SITE_URL; ?>/admin/blog" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<form method="POST" action="<?php echo SITE_URL; ?>/admin/blog/update/<?php echo $post['id']; ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả ngắn (Excerpt)</label>
                        <textarea name="excerpt" class="form-control" rows="3" placeholder="Tóm tắt nội dung..."><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
                        <small class="text-muted">Hiển thị ở trang danh sách tin tức.</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Nội dung chi tiết <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="15" required><?php echo $post['content']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-image me-2 text-primary"></i> Ảnh đại diện</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <?php if ($post['featured_image']): ?>
                            <img src="<?php echo UPLOAD_URL . 'blog/' . $post['featured_image']; ?>" class="img-fluid rounded shadow-sm border" style="max-height: 200px;">
                        <?php else: ?>
                            <div class="bg-light border rounded py-4 text-muted">
                                <i class="fas fa-image fa-3x mb-2 opacity-25"></i>
                                <p class="mb-0">Chưa có ảnh</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <label class="form-label fw-bold small">Thay đổi ảnh mới</label>
                    <input type="file" name="featured_image" class="form-control form-control-sm" accept="image/*">
                    <small class="text-muted d-block mt-1">Để trống nếu muốn giữ nguyên ảnh cũ.</small>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i> Thông tin xuất bản</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="far fa-eye me-1"></i> Lượt xem</span>
                            <span class="badge bg-info rounded-pill"><?php echo $post['views']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="far fa-clock me-1"></i> Ngày đăng</span>
                            <span class="fw-bold small"><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></span>
                        </li>
                    </ul>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fas fa-save me-2"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include 'includes/footer.blade.php'; ?>