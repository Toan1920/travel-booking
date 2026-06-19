<div class="bg-white">
    <div class="bg-light py-3 border-bottom mb-5">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/blog">Cẩm nang</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($post['title']); ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h1 class="fw-bold mb-3 display-6"><?php echo htmlspecialchars($post['title']); ?></h1>
                
                <div class="d-flex align-items-center text-muted mb-4 small border-bottom pb-3">
                    <div class="me-4"><i class="far fa-user me-1"></i> <?php echo htmlspecialchars($post['full_name'] ?? 'Admin'); ?></div>
                    <div class="me-4"><i class="far fa-calendar-alt me-1"></i> <?php echo formatDate($post['created_at']); ?></div>
                    <div><i class="far fa-eye me-1"></i> <?php echo $post['views']; ?> lượt xem</div>
                </div>

                <?php if ($imgUrl): ?>
                    <img src="<?php echo $imgUrl; ?>" class="img-fluid rounded mb-5 w-100 shadow-sm" alt="<?php echo htmlspecialchars($post['title']); ?>">
                <?php endif; ?>

                <div class="blog-content mb-5" style="line-height: 1.8; font-size: 1.1rem; color: #333;">
                    <?php echo $post['content']; // Output HTML ?>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-5">
                    <div class="fw-bold">Chia sẻ bài viết này:</div>
                    <div>
                        <button class="btn btn-primary btn-sm rounded-circle me-1"><i class="fab fa-facebook-f"></i></button>
                        <button class="btn btn-info text-white btn-sm rounded-circle me-1"><i class="fab fa-twitter"></i></button>
                        <button class="btn btn-danger btn-sm rounded-circle"><i class="fab fa-pinterest"></i></button>
                    </div>
                </div>

                <?php if (count($relatedPosts) > 0): ?>
                <div class="mt-5">
                    <h4 class="fw-bold mb-4 border-start border-4 border-primary ps-3">Bài viết mới nhất</h4>
                    <div class="row">
                        <?php foreach ($relatedPosts as $rel): 
                             $relThumb = !empty($rel['featured_image']) 
                                ? UPLOAD_URL . 'blog/' . $rel['featured_image'] 
                                : SITE_URL . '/assets/images/hero-bg.jpg';
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm hover-card">
                                <a href="<?php echo SITE_URL; ?>/blog/<?php echo $rel['slug']; ?>">
                                    <img src="<?php echo $relThumb; ?>" class="card-img-top" style="height: 160px; object-fit: cover;" alt="<?php echo htmlspecialchars($rel['title']); ?>">
                                </a>
                                <div class="card-body">
                                    <h6 class="card-title mb-0">
                                        <a href="<?php echo SITE_URL; ?>/blog/<?php echo $rel['slug']; ?>" class="text-dark text-decoration-none">
                                            <?php echo htmlspecialchars($rel['title']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted mt-2 d-block"><?php echo formatDate($rel['created_at']); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<style>
    .blog-content img { max-width: 100%; height: auto; border-radius: 5px; margin: 15px 0; }
    .hover-card:hover { transform: translateY(-5px); transition: 0.3s; }
</style>