<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-primary">Cẩm nang du lịch</h1>
            <p class="text-muted lead">Chia sẻ kinh nghiệm và bí kíp du lịch hấp dẫn nhất</p>
        </div>

        <div class="row">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): 
                    // Xử lý ảnh đại diện
                    $thumb = !empty($post['featured_image']) 
                        ? UPLOAD_URL . 'blog/' . $post['featured_image'] 
                        : SITE_URL . '/assets/images/hero-bg.jpg'; 
                    
                    // Link SEO thân thiện
                    $link = SITE_URL . '/blog/' . htmlspecialchars($post['slug']);
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <a href="<?php echo $link; ?>" class="overflow-hidden">
                            <img src="<?php echo $thumb; ?>" class="card-img-top hover-zoom" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                 style="height: 220px; object-fit: cover;">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <div class="small text-muted mb-2">
                                <i class="far fa-calendar-alt me-1"></i> <?php echo formatDate($post['created_at']); ?>
                                <span class="mx-2">&bull;</span>
                                <i class="far fa-user me-1"></i> <?php echo htmlspecialchars($post['author'] ?? 'Admin'); ?>
                            </div>
                            
                            <h5 class="card-title fw-bold">
                                <a href="<?php echo $link; ?>" class="text-dark text-decoration-none text-truncate-2">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h5>
                            
                            <p class="card-text text-muted small flex-grow-1">
                                <?php 
                                $summary = !empty($post['excerpt']) ? $post['excerpt'] : strip_tags($post['content']);
                                echo mb_strimwidth($summary, 0, 120, "..."); 
                                ?>
                            </p>
                            
                            <div class="mt-3">
                                <a href="<?php echo $link; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="far fa-newspaper fa-4x text-muted mb-3 opacity-50"></i>
                    <p class="text-muted">Chưa có bài viết nào được đăng.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<style>
    .hover-card:hover { transform: translateY(-5px); transition: 0.3s; }
    .hover-zoom:hover { transform: scale(1.05); transition: 0.5s; }
    .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>