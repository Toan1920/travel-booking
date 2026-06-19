<?php include 'includes/header.blade.php'; ?>

<div class="page-header mb-4">
    <h3 class="fw-bold mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i> Quản lý Đơn đặt tour</h3>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" action="<?php echo SITE_URL; ?>/admin/bookings" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted mb-1">Trạng thái đơn</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <optgroup label="Tình trạng thanh toán">
                        <option value="paid" <?php echo $statusFilter == 'paid' ? 'selected' : ''; ?>>Đã thanh toán</option>
                        <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Chờ thanh toán</option>
                    </optgroup>
                    <optgroup label="Tiến độ xử lý">
                        <option value="new" <?php echo $statusFilter == 'new' ? 'selected' : ''; ?>>Mới đặt</option>
                        <option value="confirmed" <?php echo $statusFilter == 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                        <option value="completed" <?php echo $statusFilter == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                        <option value="cancelled" <?php echo $statusFilter == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                    </optgroup>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted mb-1">Tìm kiếm</label>
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Mã đơn, tên khách, SĐT..." value="<?php echo htmlspecialchars($keyword); ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="col-md-2">
                 <a href="<?php echo SITE_URL; ?>/admin/bookings" class="btn btn-secondary w-100"><i class="fas fa-sync me-1"></i> Xóa bộ lọc</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tour đăng ký</th>
                        <th>Ngày đi</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-center">Thanh toán</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bookings) > 0): ?>
                        <?php foreach ($bookings as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <a href="<?php echo SITE_URL; ?>/admin/bookings/detail/<?php echo $row['id']; ?>" class="fw-bold text-decoration-none text-primary">
                                    #<?php echo htmlspecialchars($row['booking_code']); ?>
                                </a>
                                <div class="small text-muted mt-1"><i class="far fa-clock me-1"></i><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></div>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                <div class="small text-muted"><i class="fas fa-phone-alt me-1"></i><?php echo htmlspecialchars($row['phone']); ?></div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;" title="<?php echo htmlspecialchars($row['tour_title']); ?>">
                                    <?php echo htmlspecialchars($row['tour_title']); ?>
                                </div>
                            </td>
                            <td>
                                <?php echo $row['departure_date'] ? date('d/m/Y', strtotime($row['departure_date'])) : '<span class="text-muted">Chưa chốt</span>'; ?>
                            </td>
                            <td class="text-end fw-bold text-danger">
                                <?php echo formatCurrency($row['final_amount']); ?>
                            </td>
                            <td class="text-center">
                                <?php if ($row['payment_status'] == 'paid'): ?>
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Đã TT</span>
                                <?php elseif ($row['payment_status'] == 'refunded'): ?>
                                    <span class="badge bg-secondary">Đã hoàn</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Chờ TT</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?php echo $sttClass[$row['booking_status']] ?? 'secondary'; ?>">
                                    <?php echo $sttLabel[$row['booking_status']] ?? $row['booking_status']; ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <a href="<?php echo SITE_URL; ?>/admin/bookings/detail/<?php echo $row['id']; ?>" class="btn btn-sm btn-info text-white shadow-sm" title="Xem chi tiết đơn hàng">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3 opacity-50"></i>
                                <p class="text-muted mb-0">Không tìm thấy đơn hàng nào phù hợp với bộ lọc.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-white py-3 border-0">
        <ul class="pagination justify-content-center mb-0">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo htmlspecialchars($statusFilter); ?>&q=<?php echo urlencode($keyword); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.blade.php'; ?>