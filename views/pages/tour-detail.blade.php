<div class="bg-light py-3 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Trang chủ</a></li>
                <li class="breadcrumb-item">
                    <a href="<?php echo SITE_URL; ?>/tours?cat=<?php echo htmlspecialchars($tour['cat_slug'] ?? ''); ?>">
                        <?php echo htmlspecialchars($tour['cat_name'] ?? 'Tour du lịch'); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active text-truncate" style="max-width: 300px;" aria-current="page">
                    <?php echo htmlspecialchars($tour['title']); ?>
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-3 fw-bold text-primary h2"><?php echo htmlspecialchars($tour['title']); ?></h1>
            <div class="mb-4 text-muted small d-flex flex-wrap gap-3 align-items-center">
                <span><i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo htmlspecialchars($tour['destination']); ?></span>
                <span class="d-none d-md-inline">|</span>
                <span><i class="fas fa-clock text-primary me-1"></i> <?php echo htmlspecialchars($tour['duration']); ?></span>
                <span class="d-none d-md-inline">|</span>
                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($tour['cat_name'] ?? 'Chung'); ?></span>
                <span class="d-none d-md-inline">|</span>
                <span class="text-warning fw-bold">
                    <?php echo round((float)$tour['rating'], 1); ?> <i class="fas fa-star"></i>
                </span> 
                <span>(<?php echo $tour['total_reviews']; ?> đánh giá)</span>
            </div>

            <div class="mb-5">
                <div class="position-relative overflow-hidden rounded shadow-sm border">
                    <img src="<?php echo $mainImg; ?>" id="mainImage" class="img-fluid w-100" style="height: 450px; object-fit: cover;">
                    <?php if ($tour['discount_percent'] > 0): ?>
                        <div class="badge bg-danger position-absolute top-0 start-0 m-3 p-2 fs-6 shadow">
                            Giảm <?php echo $tour['discount_percent']; ?>%
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if(count($images) > 1): ?>
                <div class="row g-2 mt-2">
                    <?php foreach ($images as $img): 
                        $imgUrl = UPLOAD_URL .'tours/'. $img;
                    ?>
                    <div class="col-2">
                        <img src="<?php echo $imgUrl; ?>" 
                             class="img-fluid rounded cursor-pointer border hover-opacity" 
                             style="height: 70px; width: 100%; object-fit: cover;" 
                             onclick="changeImage('<?php echo $imgUrl; ?>')">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <ul class="nav nav-tabs nav-fill mb-4 fw-bold" id="tourTab">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#desc">Giới thiệu</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#itinerary">Lịch trình</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#policy">Chính sách</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">Đánh giá</button></li>
            </ul>
            
            <div class="tab-content p-4 border border-top-0 rounded-bottom bg-white shadow-sm mb-5">
                <div class="tab-pane fade show active" id="desc" style="line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars_decode($tour['description'])); ?>
                </div>
                
                <div class="tab-pane fade" id="itinerary" style="line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($tour['itinerary'])); ?>
                </div>
                
                <div class="tab-pane fade" id="policy">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5 class="text-success fw-bold"><i class="fas fa-check-circle"></i> Giá bao gồm:</h5>
                            <ul class="list-unstyled">
                                <?php 
                                $incs = explode("\n", $tour['includes']);
                                foreach($incs as $item) if(trim($item)) echo "<li class='mb-1'><i class='fas fa-check text-success small me-2'></i> " . htmlspecialchars($item) . "</li>";
                                ?>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-danger fw-bold"><i class="fas fa-times-circle"></i> Không bao gồm:</h5>
                            <ul class="list-unstyled">
                                <?php 
                                $excs = explode("\n", $tour['excludes']);
                                foreach($excs as $item) if(trim($item)) echo "<li class='mb-1'><i class='fas fa-times text-danger small me-2'></i> " . htmlspecialchars($item) . "</li>";
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="reviews">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="mb-0">Khách hàng đánh giá</h5>
                        <?php if(isLoggedIn()): ?>
                            <button class="btn btn-outline-primary btn-sm" onclick="showToast('Tính năng đang phát triển!', false)">Viết đánh giá</button>
                        <?php endif; ?>
                    </div>

                    <?php if (count($reviews) > 0): ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="d-flex mb-4 border-bottom pb-3">
                            <div class="flex-shrink-0">
                                <div class="avatar bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold border" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                    <?php echo strtoupper(substr($review['full_name'], 0, 1)); ?>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($review['full_name']); ?></h6>
                                <div class="text-warning small mb-2">
                                    <?php for($i=1; $i<=5; $i++) echo ($i <= $review['rating']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; ?>
                                </div>
                                <p class="mb-1 text-secondary"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                <small class="text-muted fst-italic" style="font-size: 0.8rem;">Đăng ngày <?php echo formatDate($review['created_at']); ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có đánh giá nào cho tour này.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow border-0 sticky-top" style="top: 100px; z-index: 10;">
                <div class="card-header bg-gradient-primary text-white text-center py-3" style="background: linear-gradient(45deg, #3498db, #2980b9);">
                    <h5 class="mb-0 fw-bold text-uppercase">Đặt Tour Ngay</h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <?php if ($tour['discount_percent'] > 0): ?>
                            <small class="text-muted text-decoration-line-through">
                                <?php echo formatCurrency($tour['price_adult']); ?>
                            </small>
                        <?php endif; ?>
                        
                        <h3 class="text-danger fw-bold mb-0">
                            <?php 
                                $priceNow = $tour['price_adult'] * (100 - $tour['discount_percent']) / 100;
                                echo formatCurrency($priceNow); 
                            ?>
                        </h3>
                        <small class="text-success fw-bold">/ khách</small>
                    </div>
                    
                    <form action="<?php echo SITE_URL; ?>/booking" method="GET" onsubmit="return validateBooking()">
                        <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted"><i class="far fa-calendar-alt"></i> NGÀY KHỞI HÀNH</label>
                            <select name="departure_date_id" id="departure_date" class="form-select border-primary" required onchange="updatePriceInfo()">
                                <option value="" data-price="0">-- Chọn ngày đi --</option>
                                <?php foreach ($dates as $d): 
                                    $pAdult = $d['price_adult'] > 0 ? $d['price_adult'] : $priceNow;
                                    $pChild = $d['price_child'] > 0 ? $d['price_child'] : ($tour['price_child'] > 0 ? $tour['price_child'] : $priceNow * 0.7);
                                ?>
                                    <option value="<?php echo $d['id']; ?>" 
                                            data-price-adult="<?php echo $pAdult; ?>"
                                            data-price-child="<?php echo $pChild; ?>"
                                            data-slots="<?php echo $d['available_slots']; ?>">
                                        <?php echo formatDate($d['departure_date']); ?> 
                                        (Còn <?php echo $d['available_slots']; ?> chỗ)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Người lớn</label>
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-outline-secondary" type="button" onclick="adjustQty('adults', -1)">-</button>
                                    <input type="number" name="adults" id="adults" value="1" min="1" class="form-control text-center bg-white" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="adjustQty('adults', 1)">+</button>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Trẻ em</label>
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-outline-secondary" type="button" onclick="adjustQty('children', -1)">-</button>
                                    <input type="number" name="children" id="children" value="0" min="0" class="form-control text-center bg-white" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="adjustQty('children', 1)">+</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded border">
                            <span class="small fw-bold">Tạm tính:</span>
                            <span class="fw-bold text-primary" id="total-preview">0 ₫</span>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold mb-3 shadow-sm btn-lg">
                            <i class="fas fa-paper-plane me-2"></i> ĐẶT GIỮ CHỖ
                        </button>
                        
                        <?php if(isLoggedIn()): ?>
                        <button type="button" class="btn btn-outline-secondary w-100 btn-sm" onclick="addToWishlist(<?php echo $tour['id']; ?>)">
                            <i class="far fa-heart text-danger"></i> Lưu vào yêu thích
                        </button>
                        <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/login" class="btn btn-outline-secondary w-100 btn-sm">
                            Đăng nhập để lưu tin
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .hover-opacity:hover { opacity: 0.8; transition: 0.2s; border-color: #3498db !important; }
    .nav-tabs .nav-link.active { border-top: 3px solid #3498db; color: #3498db; }
    .nav-tabs .nav-link { color: #555; }
</style>

<script>
function changeImage(src) { document.getElementById('mainImage').src = src; }

function adjustQty(id, amount) {
    const input = document.getElementById(id);
    let val = parseInt(input.value) + amount;
    if (val < parseInt(input.min)) val = parseInt(input.min);
    input.value = val;
    updatePriceInfo();
}

function updatePriceInfo() {
    const select = document.getElementById('departure_date');
    const option = select.options[select.selectedIndex];
    if (!option.value) { document.getElementById('total-preview').innerText = '0 ₫'; return; }

    const priceAdult = parseFloat(option.getAttribute('data-price-adult')) || 0;
    const priceChild = parseFloat(option.getAttribute('data-price-child')) || 0;
    const qtyAdult = parseInt(document.getElementById('adults').value) || 0;
    const qtyChild = parseInt(document.getElementById('children').value) || 0;
    
    const total = (priceAdult * qtyAdult) + (priceChild * qtyChild);
    
    // Gọi hàm formatCurrency toàn cục từ footer.blade.php
    document.getElementById('total-preview').innerText = formatCurrency(total);
}

function validateBooking() {
    if(!document.getElementById('departure_date').value) {
        showToast('Vui lòng chọn ngày khởi hành!', false);
        return false;
    }
    return true;
}
</script>