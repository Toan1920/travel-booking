<div class="container py-5">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center pt-4">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <h5 class="mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                    <p class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></p>
                    
                    <div class="badge bg-warning text-dark mb-3">
                        <i class="fas fa-crown"></i> <?php echo ucfirst($user['member_level'] ?? 'Standard'); ?> Member
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo SITE_URL; ?>/user/dashboard" class="list-group-item list-group-item-action active">
                        <i class="fas fa-th-large me-2"></i> Dashboard
                    </a>
                    <a href="<?php echo SITE_URL; ?>/user/profile" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-circle me-2"></i> Hồ sơ cá nhân
                    </a>
                    <a href="<?php echo SITE_URL; ?>/logout" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card bg-primary text-white h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Điểm tích lũy</h6>
                                    <h2 class="mb-0"><?php echo number_format($user['points'] ?? 0); ?></h2>
                                </div>
                                <i class="fas fa-coins fa-2x" style="opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card bg-success text-white h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Đã đặt tour</h6>
                                    <h2 class="mb-0"><?php echo count($bookings); ?></h2>
                                </div>
                                <i class="fas fa-suitcase-rolling fa-2x" style="opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-history me-2"></i>Lịch sử đặt tour</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Mã đơn</th>
                                    <th>Tên Tour</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($bookings) > 0): ?>
                                    <?php foreach ($bookings as $book): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold">#<?php echo htmlspecialchars($book['booking_code']); ?></td>
                                        <td>
                                            <a href="<?php echo SITE_URL; ?>/tour/<?php echo htmlspecialchars($book['tour_slug']); ?>" class="text-decoration-none text-dark fw-bold">
                                                <?php echo htmlspecialchars($book['tour_title']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($book['created_at'])); ?></td>
                                        <td class="text-danger fw-bold"><?php echo formatCurrency($book['final_amount']); ?></td>
                                        <td>
                                            <?php 
                                                $badge = $sttClass[$book['booking_status']] ?? 'secondary';
                                                $label = $sttLabel[$book['booking_status']] ?? $book['booking_status'];

                                                if ($book['payment_status'] == 'paid') {
                                                    $badge = 'success';
                                                    $label = 'Đã thanh toán';
                                                } elseif ($book['booking_status'] == 'cancelled') {
                                                    $badge = 'danger';
                                                    $label = 'Đã hủy';
                                                }
                                            ?>
                                            <span class="badge bg-<?php echo $badge; ?>">
                                                <?php echo $label; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($book['payment_status'] == 'pending' && $book['booking_status'] != 'cancelled'): ?>
                                                <a href="<?php echo SITE_URL; ?>/payment/vnpay_create?booking_id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    Thanh toán
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-secondary" disabled>Chi tiết</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                                            <p class="mt-3">Bạn chưa đặt tour nào. <a href="<?php echo SITE_URL; ?>/tours">Khám phá ngay!</a></p>
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
</div>