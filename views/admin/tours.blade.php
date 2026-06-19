<?php include __DIR__ . '/includes/header.blade.php'; ?>

<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h3 class="fw-bold mb-0"><i class="fas fa-map-marked-alt me-2 text-primary"></i> Quản lý Tour du lịch</h3>
    <a href="<?php echo SITE_URL; ?>/admin/tours/create" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus-circle me-1"></i> Thêm Tour mới
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" action="<?php echo SITE_URL; ?>/admin/tours" class="d-flex w-50">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Tìm kiếm tên tour..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
                <?php if($search): ?>
                    <a href="<?php echo SITE_URL; ?>/admin/tours" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
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
                        <th width="80">Ảnh</th>
                        <th>Thông tin Tour</th>
                        <th>Giá bán</th>
                        <th class="text-center">Nổi bật / Sale</th>
                        <th class="text-center">Trạng thái</th>
                        <th width="150" class="text-center pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($tours) > 0): ?>
                        <?php foreach ($tours as $row): 
                            $images = json_decode($row['images'], true);
                            $thumb = (!empty($images) && isset($images[0])) ? $images[0] : null;
                            $imgSrc = $thumb ? UPLOAD_URL . 'tours/' . $thumb : SITE_URL . '/assets/images/no-image.png';
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#<?php echo $row['id']; ?></td>
                            <td>
                                <img src="<?php echo $imgSrc; ?>" onerror="this.src='<?php echo SITE_URL; ?>/assets/images/no-image.png'" class="rounded shadow-sm" style="width: 60px; height: 45px; object-fit: cover;">
                            </td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/tour/<?php echo $row['slug']; ?>" target="_blank" class="fw-bold text-dark text-decoration-none d-block mb-1">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </a>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info"><i class="fas fa-folder-open me-1"></i><?php echo htmlspecialchars($row['cat_name'] ?? 'Chưa phân loại'); ?></span>
                            </td>
                            <td>
                                <div class="text-danger fw-bold"><?php echo formatCurrency($row['price_adult']); ?></div>
                                <?php if($row['discount_percent'] > 0): ?>
                                    <span class="badge bg-danger rounded-pill"><i class="fas fa-arrow-down me-1"></i><?php echo $row['discount_percent']; ?>%</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <form method="POST" action="<?php echo SITE_URL; ?>/admin/tours/toggle">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="tour_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="featured">
                                        <button type="submit" class="btn btn-sm <?php echo $row['featured'] ? 'btn-warning text-dark shadow-sm' : 'btn-light text-muted border'; ?>" title="Bật/Tắt Nổi bật">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>

                                    <form method="POST" action="<?php echo SITE_URL; ?>/admin/tours/toggle">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="tour_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="flash_sale">
                                        <button type="submit" class="btn btn-sm <?php echo $row['flash_sale'] ? 'btn-danger shadow-sm' : 'btn-light text-muted border'; ?>" title="Bật/Tắt Flash Sale">
                                            <i class="fas fa-bolt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <td class="text-center">
                                <?php if ($row['status'] == 'active'): ?>
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i>Đã ẩn</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?php echo SITE_URL; ?>/admin/departure-dates?tour_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-warning" title="Quản lý Lịch khởi hành">
                                        <i class="fas fa-calendar-alt"></i>
                                    </a>
                                    
                                    <a href="<?php echo SITE_URL; ?>/admin/tours/edit/<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info" title="Sửa thông tin Tour">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" action="<?php echo SITE_URL; ?>/admin/tours/delete" onsubmit="return confirm('CẢNH BÁO: Hành động này có thể gây lỗi nếu tour đã có người đặt. Bạn có chắc chắn muốn xóa?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa Tour">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3 opacity-50"></i>
                                <p class="text-muted mb-0">Chưa có tour du lịch nào. Hãy thêm tour đầu tiên!</p>
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