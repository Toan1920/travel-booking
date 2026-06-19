<?php include __DIR__ . '/includes/header.blade.php'; ?>

<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h3 class="fw-bold mb-0"><i class="fas fa-star me-2 text-warning"></i> Quản lý Đánh giá</h3>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Danh sách phản hồi từ Khách hàng</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" width="200">Khách hàng</th>
                        <th width="250">Tour tham gia</th>
                        <th width="120">Điểm số</th>
                        <th>Nội dung đánh giá</th>
                        <th class="text-center" width="120">Trạng thái</th>
                        <th class="text-center pe-4" width="120">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($reviews) > 0): ?>
                        <?php foreach ($reviews as $r): ?>
                        <tr>
                            <td class="ps-4">
                                <strong class="text-dark d-block"><?php echo htmlspecialchars($r['full_name'] ?? 'Khách ẩn danh'); ?></strong>
                                <small class="text-muted"><i class="far fa-clock me-1"></i><?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?></small>
                            </td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/admin/tours/edit/<?php echo $r['tour_id']; ?>" class="text-decoration-none text-primary fw-bold small" title="Xem Tour">
                                    <?php echo mb_strimwidth(htmlspecialchars($r['tour_title'] ?? 'Tour đã bị xóa'), 0, 40, '...'); ?>
                                </a>
                            </td>
                            <td>
                                <div class="text-warning fw-bold">
                                    <?php echo $r['rating']; ?> <i class="fas fa-star"></i>
                                </div>
                            </td>
                            <td>
                                <div class="bg-light p-2 rounded small text-dark border">
                                    <?php echo nl2br(htmlspecialchars($r['comment'])); ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($r['status'] == 'approved'): ?>
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Đã duyệt</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>Chờ duyệt</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <?php if ($r['status'] == 'pending'): ?>
                                        <form method="POST" action="<?php echo SITE_URL; ?>/admin/reviews/approve">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="review_id" value="<?php echo $r['id']; ?>">
                                            <input type="hidden" name="tour_id" value="<?php echo $r['tour_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Duyệt đánh giá này">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="<?php echo SITE_URL; ?>/admin/reviews/delete" onsubmit="return confirm('Bạn chắc chắn muốn xóa đánh giá này vĩnh viễn?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="review_id" value="<?php echo $r['id']; ?>">
                                        <input type="hidden" name="tour_id" value="<?php echo $r['tour_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa đánh giá">
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
                                <i class="far fa-star fa-3x text-muted mb-3 opacity-50"></i>
                                <p class="text-muted mb-0">Chưa có đánh giá nào từ khách hàng.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.blade.php'; ?>