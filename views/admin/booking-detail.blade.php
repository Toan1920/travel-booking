<?php include 'includes/header.blade.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center mb-4 d-print-none">
    <h3 class="fw-bold mb-0">
        <i class="fas fa-file-invoice me-2 text-primary"></i> Chi tiết đơn hàng #<?php echo htmlspecialchars($booking['booking_code']); ?>
        <?php if ($booking['payment_status'] == 'paid'): ?>
            <span class="badge bg-success ms-2 px-3 align-middle" style="font-size: 0.5em;"><i class="fas fa-check-circle me-1"></i>ĐÃ THANH TOÁN</span>
        <?php else: ?>
            <span class="badge bg-warning text-dark ms-2 px-3 align-middle" style="font-size: 0.5em;"><i class="fas fa-clock me-1"></i>CHỜ THANH TOÁN</span>
        <?php endif; ?>
    </h3>
    <div class="d-flex gap-2">
        <a href="<?php echo SITE_URL; ?>/admin/bookings" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        <button onclick="window.print()" class="btn btn-dark shadow-sm"><i class="fas fa-print me-1"></i> In hóa đơn</button>
    </div>
</div>

<div class="d-none d-print-block mb-4 text-center">
    <h2>HÓA ĐƠN ĐẶT TOUR TRAVELVN</h2>
    <p>Mã đơn: <strong>#<?php echo htmlspecialchars($booking['booking_code']); ?></strong> | Ngày in: <?php echo date('d/m/Y H:i'); ?></p>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-plane-departure me-2 text-primary"></i> Thông tin Tour</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <small class="text-muted fw-bold d-block mb-1">TÊN TOUR DU LỊCH</small>
                        <h5 class="fw-bold mb-3">
                            <a href="<?php echo SITE_URL; ?>/tour/<?php echo htmlspecialchars($booking['tour_slug']); ?>" target="_blank" class="text-dark text-decoration-none">
                                <?php echo htmlspecialchars($booking['tour_title']); ?> <i class="fas fa-external-link-alt fs-6 text-muted"></i>
                            </a>
                        </h5>
                        <p class="mb-0"><i class="far fa-calendar-alt me-2 text-muted"></i> Ngày khởi hành: <strong class="text-primary"><?php echo $booking['departure_date'] ? date('d/m/Y', strtotime($booking['departure_date'])) : 'N/A'; ?></strong></p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0 border-start ps-4">
                        <small class="text-muted fw-bold d-block mb-1">TỔNG TIỀN THANH TOÁN</small>
                        <h2 class="fw-bold text-danger mb-0"><?php echo formatCurrency($booking['final_amount']); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-user-circle me-2 text-primary"></i> Thông tin Khách hàng</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-muted small fw-bold">Họ và tên người đặt</label>
                        <input type="text" class="form-control bg-light fw-bold" value="<?php echo htmlspecialchars($booking['full_name']); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">Số điện thoại</label>
                        <input type="text" class="form-control bg-light fw-bold" value="<?php echo htmlspecialchars($booking['phone']); ?>" readonly>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-muted small fw-bold">Địa chỉ Email</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($booking['email']); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">Số lượng khách</label>
                        <div class="d-flex align-items-center form-control bg-light">
                            <span class="badge bg-primary me-2"><?php echo $booking['adults']; ?> Người lớn</span> 
                            <span class="badge bg-info text-dark"><?php echo $booking['children']; ?> Trẻ em</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="form-label text-muted small fw-bold">Ghi chú của khách lúc đặt (Yêu cầu đặc biệt)</label>
                    <textarea class="form-control bg-light" rows="3" readonly><?php echo htmlspecialchars($booking['notes'] ?: 'Không có yêu cầu đặc biệt.'); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4 d-print-none">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-primary"></i> Cập nhật trạng thái</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo SITE_URL; ?>/admin/bookings/update/<?php echo $booking['id']; ?>" onsubmit="return confirm('Bạn có chắc chắn muốn áp dụng thay đổi này?');">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Tiến độ xử lý đơn</label>
                        <select name="booking_status" class="form-select border-primary shadow-sm">
                            <option value="new" <?php echo $booking['booking_status'] == 'new' ? 'selected' : ''; ?>>Mới tạo</option>
                            <option value="processing" <?php echo $booking['booking_status'] == 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                            <option value="confirmed" <?php echo $booking['booking_status'] == 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận (Chốt Tour)</option>
                            <option value="completed" <?php echo $booking['booking_status'] == 'completed' ? 'selected' : ''; ?>>Đã hoàn thành</option>
                            <option value="cancelled" <?php echo $booking['booking_status'] == 'cancelled' ? 'selected' : ''; ?>>Đã Hủy Đơn</option>
                        </select>
                        <small class="text-danger mt-2 d-block"><i class="fas fa-exclamation-triangle me-1"></i> Chọn "Đã Hủy" hệ thống sẽ tự động hoàn lại chỗ trống cho Tour.</small>
                    </div>

                    <div class="mb-4 border-top pt-3">
                        <label class="form-label fw-bold">Tình trạng thanh toán</label>
                        <select name="payment_status" class="form-select border-success shadow-sm">
                            <option value="pending" <?php echo $booking['payment_status'] == 'pending' ? 'selected' : ''; ?>>Chờ thanh toán</option>
                            <option value="paid" <?php echo $booking['payment_status'] == 'paid' ? 'selected' : ''; ?>>Đã thanh toán đủ</option>
                            <option value="refunded" <?php echo $booking['payment_status'] == 'refunded' ? 'selected' : ''; ?>>Đã hoàn lại tiền</option>
                        </select>
                        <small class="text-success mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Nếu Admin tự chuyển sang "Đã thanh toán", hệ thống sẽ lưu Log giao dịch.</small>
                    </div>

                    <div class="mb-4 border-top pt-3">
                        <label class="form-label fw-bold">Ghi chú nội bộ</label>
                        <textarea name="admin_note" class="form-control" rows="4" placeholder="Ví dụ: Khách hẹn thanh toán tiền mặt lúc lên xe..."><?php echo htmlspecialchars($booking['notes']); ?></textarea>
                        <small class="text-muted">Chỉ Admin mới thấy ghi chú này.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold text-uppercase shadow-sm">
                        <i class="fas fa-save me-2"></i> Lưu thay đổi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS ẩn Sidebar và Header chung khi nhấn Ctrl+P in ấn */
    @media print {
        #sidebar, .admin-topbar { display: none !important; }
        .admin-main { margin-left: 0 !important; width: 100% !important; padding: 0 !important; background: #fff !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>

<?php include 'includes/footer.blade.php'; ?>