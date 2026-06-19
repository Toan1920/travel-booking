<?php include __DIR__ . '/includes/header.blade.php'; ?>

<div class="page-header mb-4">
    <h3 class="fw-bold mb-0"><i class="fas fa-folder me-2 text-primary"></i> Quản lý Danh mục Tour</h3>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i> Thêm mới</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo SITE_URL; ?>/admin/categories/store">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="VD: Du lịch Biển">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Loại tour</label>
                        <select name="type" class="form-select">
                            <option value="domestic">Trong nước</option>
                            <option value="international">Quốc tế</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Mô tả ngắn</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Ghi chú về danh mục này..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                        <i class="fas fa-save me-2"></i> Lưu danh mục
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i> Danh sách hiện có</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tên danh mục</th>
                                <th>Phân loại</th>
                                <th width="100" class="text-center pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($categories) > 0): ?>
                                <?php foreach ($categories as $c): ?>
                                <tr>
                                    <td class="ps-4">
                                        <strong class="text-dark d-block"><?php echo htmlspecialchars($c['name']); ?></strong>
                                        <small class="text-muted"><i class="fas fa-link me-1"></i>/<?php echo htmlspecialchars($c['slug']); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($c['type'] == 'domestic'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-map-marker-alt me-1"></i> Trong nước</span>
                                        <?php else: ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info"><i class="fas fa-globe-asia me-1"></i> Quốc tế</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <form method="POST" action="<?php echo SITE_URL; ?>/admin/categories/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="delete_id" value="<?php echo $c['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa danh mục">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-50"></i>
                                        <p class="text-muted mb-0">Chưa có danh mục nào trên hệ thống.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.blade.php'; ?>