<div class="bg-light py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-md-6">
                <h1 class="mb-4 fw-bold text-primary">Liên hệ với chúng tôi</h1>
                <p class="mb-4 lead text-muted">Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn 24/7. Đừng ngần ngại để lại lời nhắn.</p>
                
                <div class="contact-info mb-5">
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0 btn-square bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-map-marker-alt fa-lg"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-1 fw-bold">Địa chỉ</h5>
                            <p class="text-muted mb-0">Trung Mỹ Tây, Quận 12, Việt Nam</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0 btn-square bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-phone-alt fa-lg"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-1 fw-bold">Hotline</h5>
                            <p class="text-muted mb-0">0777735504 (Hỗ trợ 24/7)</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0 btn-square bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-envelope fa-lg"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-1 fw-bold">Email</h5>
                            <p class="text-muted mb-0">contact@travelvn.com</p>
                        </div>
                    </div>
                </div>
                
                <div class="ratio ratio-16x9 bg-white border rounded shadow-sm overflow-hidden">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.42059489658!2d106.62766461462332!3d10.855574792267865!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752b1df4000001%3A0x10b776092008ab54!2sTrung%20M%E1%BB%B9%20T%C3%A2y%2C%20Qu%E1%BA%ADn%2012%2C%20H%E1%BB%93%20Ch%C3%AD%20Minh%2C%20Vi%E1%BB%87t%20Nam!5e0!3m2!1svi!2s!4v1620000000000!5m2!1svi!2s" 
                        style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow border-0 h-100">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="card-title mb-4 fw-bold text-center">Gửi tin nhắn</h3>
                        
                        <form method="POST" action="<?php echo SITE_URL; ?>/contact/submit">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">HỌ VÀ TÊN</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-user text-primary"></i></span>
                                    <input type="text" name="full_name" class="form-control" required 
                                           placeholder="Nhập họ tên của bạn"
                                           value="<?php echo htmlspecialchars($preFillName); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">EMAIL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-envelope text-primary"></i></span>
                                    <input type="email" name="email" class="form-control" required 
                                           placeholder="Nhập email của bạn"
                                           value="<?php echo htmlspecialchars($preFillEmail); ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">NỘI DUNG CẦN HỖ TRỢ</label>
                                <textarea name="message" class="form-control" rows="6" required placeholder="Bạn cần tư vấn về tour nào?"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase shadow-sm hover-top">
                                <i class="fas fa-paper-plane me-2"></i> Gửi ngay
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-top:hover { transform: translateY(-2px); transition: 0.3s; }
    .btn-square { width: 50px; height: 50px; }
</style>