<?php include 'includes/header.blade.php'; ?>

<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h3 class="fw-bold mb-0"><i class="fas fa-blog me-2 text-primary"></i> Quản lý Blog / Tin tức</h3>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-pen-nib me-2 text-primary"></i> Đăng bài mới</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo SITE_URL; ?>/admin/blog/store" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="Nhập tiêu đề...">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ảnh đại diện</label>
                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả ngắn (SEO)</label>
                        <textarea name="excerpt" class="form-control" rows="3" placeholder="Tóm tắt nội dung bài viết..."></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nội dung chi tiết <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="8" required placeholder="Nội dung bài viết..."></textarea>
                        <small class="text-muted">Hỗ trợ các thẻ HTML cơ bản.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="fas fa-paper-plane me-2"></i> Xuất bản bài viết
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Danh sách bài viết đã đăng</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="80" class="ps-3">Ảnh</th>
                                <th>Thông tin bài viết</th>
                                <th width="120" class="text-center">Lượt xem</th>
                                <th width="120" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($posts) > 0): ?>
                                <?php foreach ($posts as $row): 
                                    $img = !empty($row['featured_image']) ? UPLOAD_URL . 'blog/' . $row['featured_image'] : SITE_URL . '/assets/images/no-image.png';
                                ?>
                                <tr>
                                    <td class="ps-3">
                                        <img src="<?php echo $img; ?>" class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL . '/blog/' . $row['slug']; ?>" target="_blank" class="fw-bold text-dark text-decoration-none d-block mb-1">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </a>
                                        <small class="text-muted">
                                            <i class="far fa-user me-1"></i> <?php echo htmlspecialchars($row['author_name'] ?? 'Admin'); ?> &bull; 
                                            <i class="far fa-clock me-1"></i> <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-white"><i class="fas fa-eye me-1"></i> <?php echo $row['views']; ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="<?php echo SITE_URL; ?>/admin/blog/edit/<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Sửa bài viết">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form method="POST" action="<?php echo SITE_URL; ?>/admin/blog/delete" onsubmit="return confirm('Hành động này không thể hoàn tác. Bạn có chắc muốn xóa bài viết này không?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted">Chưa có bài viết nào được xuất bản.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.blade.php'; ?>