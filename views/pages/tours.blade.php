<div class="bg-light py-5">
    <div class="container">
        <div class="row">
            
            <div class="col-lg-3 mb-4">
                <div class="sticky-sidebar" style="position: sticky; top: 100px; z-index: 1;">
                    
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-filter text-primary"></i> Bộ lọc tìm kiếm</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo SITE_URL; ?>/tours" method="GET">
                                <?php if($catSlug) echo '<input type="hidden" name="cat" value="'.htmlspecialchars($catSlug).'">'; ?>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">TỪ KHÓA</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                        <input type="text" name="q" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Bạn muốn đi đâu?">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">KHOẢNG GIÁ</label>
                                    <select name="price" class="form-select">
                                        <option value="">Tất cả mức giá</option>
                                        <option value="0-5000000" <?php echo $priceRange == '0-5000000'?'selected':''; ?>>Dưới 5 triệu</option>
                                        <option value="5000000-10000000" <?php echo $priceRange == '5000000-10000000'?'selected':''; ?>>5 - 10 triệu</option>
                                        <option value="10000000-20000000" <?php echo $priceRange == '10000000-20000000'?'selected':''; ?>>10 - 20 triệu</option>
                                        <option value="20000000-100000000" <?php echo $priceRange == '20000000-100000000'?'selected':''; ?>>Trên 20 triệu</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Áp dụng</button>
                                <a href="<?php echo SITE_URL; ?>/tours" class="btn btn-link w-100 text-decoration-none mt-2 text-muted small">Xóa bộ lọc</a>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm border-0 d-none d-lg-block">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Danh mục Tour</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="<?php echo SITE_URL; ?>/tours" class="list-group-item list-group-item-action <?php echo !$catSlug ? 'active' : ''; ?>">
                                <i class="fas fa-globe me-2"></i> Tất cả
                            </a>
                            <?php foreach($sidebarCategories as $c): ?>
                            <a href="<?php echo SITE_URL; ?>/tours?cat=<?php echo $c['slug']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $catSlug == $c['slug'] ? 'active' : ''; ?>">
                                <span><i class="fas fa-angle-right me-2"></i> <?php echo htmlspecialchars($c['name']); ?></span>
                                <span class="badge bg-light text-dark rounded-pill border"><?php echo $c['count']; ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div> 
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm border">
                    <h2 class="h5 mb-0 fw-bold text-primary">
                        <?php echo htmlspecialchars($catName); ?> 
                        <span class="text-muted fw-normal fs-6 ms-2">(<?php echo $totalRecords; ?> kết quả)</span>
                    </h2>
                    
                    <form id="sortForm" class="d-flex align-items-center">
                        <label class="me-2 small text-muted d-none d-md-block">Sắp xếp:</label>
                        <?php 
                        foreach($_GET as $key => $val) {
                            if($key != 'sort') echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($val).'">'; 
                        }
                        ?>
                        <select name="sort" class="form-select form-select-sm" onchange="document.getElementById('sortForm').submit()" style="min-width: 150px;">
                            <option value="newest" <?php echo $sort == 'newest'?'selected':''; ?>>Mới nhất</option>
                            <option value="price_asc" <?php echo $sort == 'price_asc'?'selected':''; ?>>Giá tăng dần</option>
                            <option value="price_desc" <?php echo $sort == 'price_desc'?'selected':''; ?>>Giá giảm dần</option>
                            <option value="name_asc" <?php echo $sort == 'name_asc'?'selected':''; ?>>Tên A-Z</option>
                        </select>
                    </form>
                </div>

                <div class="row">
                    <?php if (count($tours) > 0): ?>
                        <?php foreach ($tours as $tour): 
                            $images = json_decode($tour['images'], true);
                            $thumb = (!empty($images) && is_array($images)) ? UPLOAD_URL .'tours/'. $images[0] : SITE_URL . '/assets/images/no-image.png';
                            $finalPrice = $tour['price_adult'] * (100 - $tour['discount_percent']) / 100;
                            // Đổi link chi tiết tour
                            $link = SITE_URL . "/tour/" . $tour['slug'];
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-0 hover-card">
                                <div class="position-relative overflow-hidden">
                                    <a href="<?php echo $link; ?>">
                                        <img src="<?php echo $thumb; ?>" class="card-img-top hover-zoom" 
                                             style="height: 220px; object-fit: cover;" 
                                             alt="<?php echo htmlspecialchars($tour['title']); ?>">
                                    </a>
                                    <?php if ($tour['discount_percent'] > 0): ?>
                                        <div class="badge bg-danger position-absolute top-0 start-0 m-3 shadow">-<?php echo $tour['discount_percent']; ?>%</div>
                                    <?php endif; ?>
                                    
                                    <button onclick="addToWishlist(<?php echo $tour['id']; ?>)" 
                                            class="btn btn-sm btn-light text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm" 
                                            title="Thêm vào yêu thích" style="z-index: 2;">
                                        <i class="far fa-heart"></i>
                                    </button>

                                    <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-gradient-dark text-white d-flex align-items-center small" style="background: rgba(0,0,0,0.6);">
                                        <i class="fas fa-clock me-1"></i> <?php echo htmlspecialchars($tour['duration']); ?>
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($tour['destination']); ?>
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <span class="badge bg-light text-primary border"><?php echo htmlspecialchars($tour['cat_name']); ?></span>
                                    </div>
                                    
                                    <h5 class="card-title text-truncate mb-3">
                                        <a href="<?php echo $link; ?>" class="text-dark text-decoration-none fw-bold">
                                            <?php echo htmlspecialchars($tour['title']); ?>
                                        </a>
                                    </h5>
                                    
                                    <div class="mt-auto pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <?php if($tour['discount_percent'] > 0): ?>
                                                    <small class="text-decoration-line-through text-muted"><?php echo formatCurrency($tour['price_adult']); ?></small>
                                                <?php endif; ?>
                                                <div class="text-danger fw-bold fs-5"><?php echo formatCurrency($finalPrice); ?></div>
                                            </div>
                                            <a href="<?php echo $link; ?>" class="btn btn-primary btn-sm px-3 rounded-pill">
                                                Đặt ngay <i class="fas fa-arrow-right small"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5 bg-white rounded shadow-sm">
                            <img src="<?php echo SITE_URL; ?>/assets/images/no-results.png" alt="Không tìm thấy" style="max-width: 150px; opacity: 0.6;">
                            <h4 class="mt-3 text-muted">Không tìm thấy tour nào!</h4>
                            <p>Vui lòng thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.</p>
                            <a href="<?php echo SITE_URL; ?>/tours" class="btn btn-outline-primary mt-2">Xem tất cả tour</a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link rounded-start" href="?page=<?php echo $page - 1; ?>&cat=<?php echo urlencode($catSlug); ?>&q=<?php echo urlencode($search); ?>&price=<?php echo urlencode($priceRange); ?>&sort=<?php echo urlencode($sort); ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&cat=<?php echo urlencode($catSlug); ?>&q=<?php echo urlencode($search); ?>&price=<?php echo urlencode($priceRange); ?>&sort=<?php echo urlencode($sort); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link rounded-end" href="?page=<?php echo $page + 1; ?>&cat=<?php echo urlencode($catSlug); ?>&q=<?php echo urlencode($search); ?>&price=<?php echo urlencode($priceRange); ?>&sort=<?php echo urlencode($sort); ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; transition: 0.3s; }
    .hover-zoom:hover { transform: scale(1.05); transition: 0.5s; }
    .sticky-top { top: 90px; } 
</style>