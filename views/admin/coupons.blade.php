
<?php include __DIR__ . '/includes/header.blade.php'; ?>

<div class="page-header mb-4">
    <h3 class="fw-bold mb-0"><i class="fas fa-gift me-2 text-primary"></i> Quản lý Mã giảm giá (Coupons)</h3>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i> Tạo mã mới</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?php echo SITE_URL; ?>/admin/coupons/store">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mã Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase fw-bold text-primary" required placeholder="VD: SUMMER2024">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Loại giảm</label>
                            <select name="type" class="form-select">
                                <option value="percent">Phần trăm (%)</option>
                                <option value="fixed">Tiền cố định</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Giá trị <span class="text-danger">*</span></label>
                            <input type="number" name="value" class="form-control" required min="1">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Đơn tối thiểu (VNĐ)</label>
                        <input type="number" name="min_order" class="form-control" value="0" min="0">
                        <small class="text-muted">Nhập 0 nếu không yêu cầu.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Giới hạn số lần dùng</label>
                        <input type="number" name="usage_limit" class="form-control" value="100" min="1">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Hạn sử dụng <span class="text-danger">*</span></label>
                        <input type="date" name="valid_to" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fas fa-gift me-2"></i> Khởi tạo mã
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i> Danh sách mã đang hoạt động</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Mã Code</th>
                                <th>Mức giảm</th>
                                <th>Điều kiện (Min)</th>
                                <th class="text-center">Đã dùng</th>
                                <th class="text-center">Hạn dùng</th>
                                <th width="80" class="text-center pe-4">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($coupons) > 0): ?>
                                <?php foreach ($coupons as $c): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary fs-6 px-2 py-1">
                                            <i class="fas fa-ticket-alt me-1"></i> <?php echo htmlspecialchars($c['code']); ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold text-danger">
                                        <?php echo $c['type'] == 'percent' ? $c['value'].'%' : formatCurrency($c['value']); ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?php echo $c['min_order'] > 0 ? formatCurrency($c['min_order']) : 'Mọi đơn hàng'; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            // Cảnh báo nếu sắp hết lượt
                                            $usagePercent = ($c['used_count'] / $c['usage_limit']) * 100;
                                            $badgeClass = $usagePercent >= 90 ? 'bg-danger' : 'bg-info text-dark';
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $c['used_count']; ?> / <?php echo $c['usage_limit']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $date = strtotime($c['valid_to']);
                                            $isExpired = time() > ($date + 86400); // Cộng thêm 1 ngày để hết hẳn ngày đó mới tính là hết hạn
                                            if ($isExpired):
                                        ?>
                                            <span class="badge bg-secondary"><i class="fas fa-calendar-times me-1"></i> Hết hạn</span>
                                        <?php else: ?>
                                            <span class="fw-bold text-success"><?php echo date('d/m/Y', $date); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <form method="POST" action="<?php echo SITE_URL; ?>/admin/coupons/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="delete_id" value="<?php echo $c['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa mã">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-ticket-alt fa-3x text-muted mb-3 opacity-50"></i>
                                        <p class="text-muted mb-0">Chưa có mã giảm giá nào trên hệ thống.</p>
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