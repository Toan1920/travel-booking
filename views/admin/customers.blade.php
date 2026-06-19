<?php include __DIR__ . '/includes/header.blade.php'; ?>

<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h3 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i> Quản lý Khách hàng</h3>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" action="<?php echo SITE_URL; ?>/admin/customers" class="d-flex w-50">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Tìm tên, email, số điện thoại..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
                <?php if($search): ?>
                    <a href="<?php echo SITE_URL; ?>/admin/customers" class="btn btn-outline-secondary" title="Xóa bộ lọc"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80" class="ps-4">ID</th>
                        <th>Họ và tên</th>
                        <th>Thông tin liên hệ</th>
                        <th>Hạng thành viên</th>
                        <th>Ngày tham gia</th>
                        <th width="120" class="text-center pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($customers) > 0): ?>
                        <?php foreach ($customers as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#<?php echo $row['id']; ?></td>
                            <td>
                                <strong class="text-dark"><?php echo htmlspecialchars($row['full_name']); ?></strong>
                            </td>
                            <td>
                                <div class="small">
                                    <div class="mb-1"><i class="fas fa-envelope text-muted me-2"></i><?php echo htmlspecialchars($row['email']); ?></div>
                                    <div><i class="fas fa-phone-alt text-muted me-2"></i><?php echo htmlspecialchars($row['phone'] ?? 'Chưa cập nhật'); ?></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info mb-1">
                                    <i class="fas fa-crown me-1"></i> <?php echo ucfirst($row['member_level'] ?? 'Member'); ?>
                                </span>
                                <div class="small text-muted fw-bold">
                                    <?php echo number_format($row['points'] ?? 0); ?> điểm
                                </div>
                            </td>
                            <td>
                                <div class="small text-muted">
                                    <i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?php echo SITE_URL; ?>/admin/bookings?q=<?php echo urlencode($row['email']); ?>" class="btn btn-sm btn-outline-primary" title="Xem lịch sử đặt tour">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    
                                    <form method="POST" action="<?php echo SITE_URL; ?>/admin/customers/delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khách hàng này? (Sẽ không thể xóa nếu khách đã có đơn đặt tour)');">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa tài khoản">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3 opacity-50"></i>
                                <p class="text-muted mb-0">Không tìm thấy khách hàng nào phù hợp.</p>
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
                    <a class="page-link" href="?page=<?php echo $i; ?>&q=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.blade.php'; ?>