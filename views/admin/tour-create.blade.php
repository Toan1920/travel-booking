<?php include __DIR__ . '/includes/header.blade.php'; ?>

<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h3 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i> Thêm Tour Mới</h3>
    <a href="<?php echo SITE_URL; ?>/admin/tours" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<form method="POST" action="<?php echo SITE_URL; ?>/admin/tours/store" enctype="multipart/form-data">
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
                        <input type="text" name="title" class="form-control" required placeholder="VD: Khám phá Vịnh Hạ Long..." onkeyup="document.getElementById('slug').value = toSlug(this.value)">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Đường dẫn (Slug URL)</label>
                            <input type="text" name="slug" id="slug" class="form-control" placeholder="Tu-dong-tao-tu-ten-tour">
                            <small class="text-muted">Có thể chỉnh sửa nếu muốn đường dẫn ngắn gọn hơn.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thời gian (VD: 3N2Đ)</label>
                            <input type="text" name="duration" class="form-control" required placeholder="3 Ngày 2 Đêm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Điểm đến</label>
                            <input type="text" name="destination" class="form-control" required placeholder="Hạ Long, Quảng Ninh">
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giá người lớn (VNĐ)</label>
                            <input type="number" name="price_adult" class="form-control text-danger fw-bold" required min="0" placeholder="VD: 2500000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giá trẻ em (VNĐ)</label>
                            <input type="number" name="price_child" class="form-control text-info fw-bold" min="0" value="0">
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
                        <textarea name="description" class="form-control" rows="4" placeholder="Đoạn văn giới thiệu hấp dẫn về tour..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Lịch trình chi tiết</label>
                        <textarea name="itinerary" class="form-control" rows="8" placeholder="Ngày 1: ...&#10;Ngày 2: ..."></textarea>
                    </div>
                    <div class="row mb-0">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-success"><i class="fas fa-check me-1"></i> Bao gồm (Xuống dòng cho mỗi mục)</label>
                            <textarea name="includes" class="form-control border-success" rows="4" placeholder="- Xe đưa đón&#10;- Khách sạn 4 sao"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger"><i class="fas fa-times me-1"></i> Không bao gồm</label>
                            <textarea name="excludes" class="form-control border-danger" rows="4" placeholder="- Thuế VAT&#10;- Tiền Tip"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-images me-2 text-primary"></i> Hình ảnh Tour</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Chọn ảnh (Có thể chọn nhiều)</label>
                        <input type="file" name="tour_images[]" class="form-control" multiple accept="image/*" required>
                        <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Nhấn giữ phím <strong>Ctrl</strong> (hoặc Cmd) để chọn nhiều ảnh cùng lúc.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase shadow-sm">
                        <i class="fas fa-save me-2"></i> Lưu Tour Mới
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Chuyển đổi Tiếng Việt có dấu thành không dấu (URL SEO)
function toSlug(str) {
    str = str.toLowerCase();
    str = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    str = str.replace(/[đĐ]/g, "d");
    str = str.replace(/([^0-9a-z-\s])/g, "");
    str = str.replace(/(\s+)/g, "-");
    str = str.replace(/^-+|-+$/g, "");
    return str;
}
</script>

<?php include __DIR__ . '/includes/footer.blade.php'; ?>