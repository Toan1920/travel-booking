<div class="bg-light py-5" style="min-height: 80vh;">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 fw-bold text-primary"><i class="fas fa-heart text-danger me-2"></i> Danh sách yêu thích</h1>
            <span class="badge bg-secondary rounded-pill" id="page-wishlist-count"><?php echo count($wishlistItems); ?> tour</span>
        </div>
        
        <div class="row">
            <?php if (count($wishlistItems) > 0): ?>
                <?php foreach ($wishlistItems as $tour): 
                    $images = json_decode($tour['images'], true);
                    $thumb = (!empty($images) && is_array($images)) 
                        ? UPLOAD_URL . 'tours/' . $images[0] 
                        : SITE_URL . '/assets/images/no-image.png'; 
                    
                    $link = SITE_URL . "/tour/" . $tour['slug'];
                ?>
                <div class="col-md-3 mb-4" id="wishlist-card-<?php echo $tour['id']; ?>">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <div class="position-relative">
                            <a href="<?php echo $link; ?>">
                                <img src="<?php echo $thumb; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo htmlspecialchars($tour['title']); ?>">
                            </a>
                            <button onclick="removeFromWishlist(<?php echo $tour['id']; ?>)" 
                                    class="btn btn-sm btn-light text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm" 
                                    title="Xóa khỏi danh sách">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title text-truncate mb-2">
                                <a href="<?php echo $link; ?>" class="text-dark text-decoration-none fw-bold">
                                    <?php echo htmlspecialchars($tour['title']); ?>
                                </a>
                            </h6>
                            
                            <div class="small text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i> <?php echo htmlspecialchars($tour['destination']); ?>
                            </div>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="text-danger fw-bold"><?php echo formatCurrency($tour['price_adult']); ?></span>
                                <a href="<?php echo $link; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Xem ngay</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded shadow-sm">
                        <i class="far fa-heart fa-4x text-muted mb-3 opacity-25"></i>
                        <h4 class="mt-2 text-muted">Danh sách yêu thích trống</h4>
                        <p class="text-muted">Bạn chưa lưu tour nào. Hãy khám phá ngay!</p>
                        <a href="<?php echo SITE_URL; ?>/tours" class="btn btn-primary px-4 py-2 mt-2 rounded-pill shadow-sm">
                            <i class="fas fa-search me-2"></i> Khám phá Tour
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .hover-card:hover { transform: translateY(-5px); transition: 0.3s; box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>

<script>
function removeFromWishlist(tourId) {
    if(!confirm('Bạn có chắc chắn muốn xóa tour này khỏi danh sách?')) return;

    const formData = new FormData();
    formData.append('tour_id', tourId);
    
    fetch(SITE_URL + '/api/wishlist/add', { 
        method: 'POST', 
        body: formData 
    })
    .then(res => res.json())
    .then(data => { 
        if(data.success) {
            // 1. Hiện thông báo mượt mà
            showToast(data.message, true);
            
            // 2. Xóa thẻ card khỏi màn hình ngay lập tức bằng hiệu ứng mờ dần
            const card = document.getElementById('wishlist-card-' + tourId);
            if(card) {
                card.style.transition = "opacity 0.3s ease";
                card.style.opacity = "0";
                setTimeout(() => card.remove(), 300);
            }

            // 3. Cập nhật số lượng trên icon Header (ID đã thiết lập ở header.blade.php)
            const headerBadge = document.getElementById('wishlist-count');
            if(headerBadge) {
                let currentCount = parseInt(headerBadge.innerText) || 0;
                headerBadge.innerText = Math.max(0, currentCount - 1);
                if((currentCount - 1) <= 0) headerBadge.style.display = 'none';
            }

            // 4. Cập nhật số lượng trên Tiêu đề trang này
            const pageBadge = document.getElementById('page-wishlist-count');
            if(pageBadge) {
                let pageCount = parseInt(pageBadge.innerText.replace(' tour', '')) || 0;
                pageCount = Math.max(0, pageCount - 1);
                pageBadge.innerText = pageCount + ' tour';
                
                // Nếu người dùng xóa sạch tour cuối cùng, reload để hiện giao diện trống
                if(pageCount === 0) {
                    setTimeout(() => location.reload(), 1000);
                }
            }
        } else {
            showToast(data.message, false);
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Lỗi kết nối server!', false);
    });
}
</script>