<?php include __DIR__ . '/includes/header.blade.php'; ?>

<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h3 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-primary"></i> Sửa Tour Du Lịch</h3>
    <a href="<?php echo SITE_URL; ?>/admin/tours" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<form method="POST" action="<?php echo SITE_URL; ?>/admin/tours/update/<?php echo $tour['id']; ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i> Thông tin cơ bản</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Tour <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($tour['title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Slug (URL SEO)</label>
                            <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($tour['slug'] ?? ''); ?>">
                            <small class="text-muted">Để trống hệ thống sẽ tự tạo từ Tên Tour.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $tour['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thời gian (VD: 3 Ngày 2 Đêm)</label>
                            <input type="text" name="duration" class="form-control" value="<?php echo htmlspecialchars($tour['duration'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Điểm đến</label>
                            <input type="text" name="destination" class="form-control" value="<?php echo htmlspecialchars($tour['destination'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giá người lớn (VNĐ)</label>
                            <input type="number" name="price_adult" class="form-control text-danger fw-bold" value="<?php echo $tour['price_adult']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giá trẻ em (VNĐ)</label>
                            <input type="number" name="price_child" class="form-control text-info fw-bold" value="<?php echo $tour['price_child']; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2 text-primary"></i> Nội dung chi tiết</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Giới thiệu tổng quan</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($tour['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Lịch trình chi tiết</label>
                        <textarea name="itinerary" class="form-control" rows="8"><?php echo htmlspecialchars($tour['itinerary'] ?? ''); ?></textarea>
                    </div>
                    <div class="row mb-0">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-success"><i class="fas fa-check me-1"></i> Bao gồm</label>
                            <textarea name="includes" class="form-control border-success" rows="4"><?php echo htmlspecialchars($tour['includes'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger"><i class="fas fa-times me-1"></i> Không bao gồm</label>
                            <textarea name="excludes" class="form-control border-danger" rows="4"><?php echo htmlspecialchars($tour['excludes'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-cog me-2 text-primary"></i> Trạng thái & Hình ảnh</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Trạng thái Tour</label>
                        <select name="status" class="form-select border-primary shadow-sm">
                            <option value="active" <?php echo $tour['status']=='active'?'selected':''; ?>>Đang hoạt động (Hiển thị)</option>
                            <option value="inactive" <?php echo $tour['status']=='inactive'?'selected':''; ?>>Tạm ẩn</option>
                        </select>
                    </div>

                    <div class="mb-4 border-top pt-4">
                        <label class="form-label fw-bold mb-2">Hình ảnh hiện tại (Tích chọn để xóa bớt)</label>
                        
                        <div class="row g-2 mb-3">
                            <?php 
                            $imgs = json_decode($tour['images'], true) ?: [];
                            if (count($imgs) > 0):
                                foreach($imgs as $index => $img): ?>
                                    <div class="col-4 text-center">
                                        <div class="position-relative border rounded p-1 bg-light shadow-sm">
                                            <img src="<?php echo UPLOAD_URL . 'tours/' . $img; ?>" onerror="this.onerror=null; this.src='<?php echo SITE_URL; ?>/assets/images/no-image.png'" class="rounded" style="width:100%; height:65px; object-fit:cover;">
                                            <div class="form-check justify-content-center d-flex mt-1 mb-0">
                                                <input class="form-check-input border-danger" type="checkbox" name="delete_images[]" value="<?php echo $img; ?>" id="del_img_<?php echo $index; ?>">
                                                <label class="form-check-label small text-danger fw-bold ms-1" for="del_img_<?php echo $index; ?>" style="font-size: 11px; cursor:pointer;">Xóa</label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; 
                            else: ?>
                                <p class="text-muted small w-100 ps-2">Chưa có hình ảnh nào cho tour này.</p>
                            <?php endif; ?>
                        </div>

                        <label class="form-label fw-bold small mt-2">Bổ sung thêm ảnh mới</label>
                        <input type="file" name="tour_images[]" class="form-control form-control-sm" multiple accept="image/*">
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Giữ phím Ctrl để chọn upload nhiều ảnh một lúc.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold text-uppercase shadow-sm">
                        <i class="fas fa-save me-2"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.blade.php'; ?>