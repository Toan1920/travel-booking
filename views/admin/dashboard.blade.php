<?php include 'includes/header.blade.php'; ?>
<div class="page-header mb-4">
    <h2 class="fw-bold"><i class="fas fa-tachometer-alt me-2 text-primary"></i> Tổng quan hệ thống</h2>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white h-100 shadow-sm border-0 hover-card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Doanh thu</h6>
                    <h3 class="mb-0 fw-bold"><?php echo formatCurrency($stats['revenue']); ?></h3>
                </div>
                <i class="fas fa-dollar-sign fa-2x" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <a href="<?php echo SITE_URL; ?>/admin/bookings?status=new" class="text-decoration-none">
            <div class="card bg-warning text-dark h-100 shadow-sm border-0 hover-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Đơn chờ xử lý</h6>
                        <h3 class="mb-0 fw-bold"><?php echo $stats['pending']; ?></h3>
                    </div>
                    <i class="fas fa-shopping-bag fa-2x" style="opacity: 0.5;"></i>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white h-100 shadow-sm border-0 hover-card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Tour đang chạy</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $stats['tours']; ?></h3>
                </div>
                <i class="fas fa-map-marked-alt fa-2x" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white h-100 shadow-sm border-0 hover-card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-1" style="opacity: 0.8;">Khách hàng</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $stats['customers']; ?></h3>
                </div>
                <i class="fas fa-users fa-2x" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i> Biểu đồ doanh thu</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i> Đơn hàng mới</h5>
                <a href="<?php echo SITE_URL; ?>/admin/bookings" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Mã đơn</th>
                                <th>Trạng thái</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentBookings as $b): ?>
                            <tr>
                                <td class="ps-3">
                                    <strong><?php echo htmlspecialchars($b['booking_code']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($b['full_name'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <?php if ($b['payment_status'] == 'paid'): ?>
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo SITE_URL; ?>/admin/bookings/detail/<?php echo $b['id']; ?>" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartData = {
        labels: <?php echo $chartLabels; ?>, // Đã được JSON_encode ở Controller
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?php echo $chartValues; ?>,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    };
    new Chart(ctx, { 
        type: 'line', 
        data: chartData, 
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        } 
    });
</script>

<style>
    .hover-card:hover { transform: translateY(-3px); transition: 0.3s; box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>

<?php include 'includes/footer.blade.php'; ?>