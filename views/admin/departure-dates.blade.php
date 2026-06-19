<?php include __DIR__ . '/includes/header.blade.php'; ?>

<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h3 class="fw-bold mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i> Quản lý Lịch khởi hành</h3>
    <?php if ($filterTourId > 0): ?>
        <a href="<?php echo SITE_URL; ?>/admin/tours" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Trở về DS Tour</a>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-filter me-2 text-primary"></i> Lọc Tour cần cấu hình</h5>
            </div>
            <div class="card-body p-4">
                <form method="GET" id="selectTourForm" class="mb-4">
                    <label class="form-label fw-bold">Chọn Tour <span class="text-danger">*</span></label>
                    <select name="tour_id" class="form-select border-primary shadow-sm" onchange="document.getElementById('selectTourForm').submit()">
                        <option value="">-- Chọn Tour để xem lịch --</option>
                        <?php foreach ($tours as $t): ?>
                            <option value="<?php echo $t['id']; ?>" <?php echo $t['id'] == $filterTourId ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <?php if ($filterTourId > 0): ?>
                <hr>
                <h6 class="fw-bold text-success mb-3"><i class="fas fa-plus-circle me-1"></i> Thêm ngày mới cho Tour này</h6>
                
                <form method="POST" action="<?php echo SITE_URL; ?>/admin/departure-dates/store">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="tour_id" value="<?php echo $filterTourId; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ngày khởi hành <span class="text-danger">*</span></label>
                        <input type="date" name="departure_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Số chỗ mở bán <span class="text-danger">*</span></label>
                        <input type="number" name="available_slots" class="form-control fw-bold" value="20" min="1" required>
                    </div>

                    <div class="mb-3 border rounded p-3 bg-light">
                        <h6 class="fw-bold small text-muted mb-2">GIÁ PHỤ THU LỄ/TẾT (TÙY CHỌN)</h6>
                        <div class="mb-2">
                            <label class="form-label small">Giá Người lớn</label>
                            <input type="number" name="price_adult" class="form-control form-control-sm text-danger fw-bold" placeholder="Để trống nếu lấy giá gốc">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Giá Trẻ em</label>
                            <input type="number" name="price_child" class="form-control form-control-sm text-info fw-bold" placeholder="Để trống nếu lấy giá gốc">
                        </div>
                        <small class="text-danger" style="font-size: 11px;">Nếu nhập, hệ thống sẽ ưu tiên lấy mức giá này thay vì giá gốc của Tour.</small>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                        <i class="fas fa-save me-2"></i> Lưu ngày khởi hành
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-list-alt me-2 text-primary"></i> Chi tiết các ngày khởi hành</h5>
            </div>
            
            <div class="card-body p-0">
                <?php if ($filterTourId == 0): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-hand-point-left fa-3x text-muted mb-3 opacity-50"></i>
                        <h5 class="text-muted">Vui lòng chọn Tour ở cột bên trái</h5>
                        <p class="text-muted small">Bạn cần chọn một Tour cụ thể để xem và thiết lập lịch khởi hành.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Ngày đi</th>
                                    <th class="text-center">Chỗ trống</th>
                                    <th>Thiết lập Giá</th>
                                    <th class="text-center">Tình trạng</th>
                                    <th width="80" class="text-center pe-4">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($dates) > 0): ?>
                                    <?php foreach ($dates as $d): 
                                        $dateStr = strtotime($d['departure_date']);
                                        $isPast = time() > ($dateStr + 86400); 
                                    ?>
                                    <tr class="<?php echo $isPast ? 'bg-light opacity-75' : ''; ?>">
                                        <td class="ps-4">
                                            <div class="fw-bold <?php echo $isPast ? 'text-muted' : 'text-primary'; ?>">
                                                <i class="far fa-calendar me-1"></i> <?php echo date('d/m/Y', $dateStr); ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?php echo $d['available_slots'] > 0 ? 'bg-info text-dark' : 'bg-danger'; ?> fs-6">
                                                <?php echo $d['available_slots']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($d['price_adult']): ?>
                                                <div class="text-danger fw-bold small"><i class="fas fa-tag me-1"></i> <?php echo formatCurrency($d['price_adult']); ?></div>
                                                <?php if($d['price_child']) echo '<div class="text-info fw-bold small">Trẻ em: '.formatCurrency($d['price_child']).'</div>'; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">(Áp dụng giá gốc)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($isPast): ?>
                                                <span class="badge bg-secondary">Đã đi</span>
                                            <?php elseif($d['available_slots'] > 0): ?>
                                                <span class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i>Mở bán</span>
                                            <?php else: ?>
                                                <span class="text-danger small fw-bold"><i class="fas fa-times-circle me-1"></i>Hết chỗ</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <form method="POST" action="<?php echo SITE_URL; ?>/admin/departure-dates/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ngày này?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="delete_id" value="<?php echo $d['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa lịch này">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="far fa-calendar-times fa-3x text-muted mb-3 opacity-50"></i>
                                            <p class="text-muted mb-0">Tour này chưa có lịch khởi hành.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.blade.php'; ?>