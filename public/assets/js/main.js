/* ==========================================================================
   FILE: public/assets/js/main.js
   CHỨC NĂNG: Xử lý toàn bộ logic tương tác phía Khách hàng (Frontend)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // 0. HÀM TIỆN ÍCH (UTILITIES)
    // ==========================================
    
    // Định dạng tiền tệ VNĐ
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    };

    // Hệ thống thông báo Toast (Góc phải màn hình)
    const showToast = (message, type = 'success') => {
        // Tạo container nếu chưa có
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;';
            document.body.appendChild(toastContainer);
        }

        // Tạo thẻ toast
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? '#2ecc71' : '#e74c3c';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        toast.style.cssText = `background: ${bgColor}; color: white; padding: 15px 25px; border-radius: 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px; opacity: 0; transform: translateX(100%); transition: all 0.3s ease; font-weight: 500;`;
        toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
        
        toastContainer.appendChild(toast);

        // Hiệu ứng trượt vào
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        });

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    // ==========================================
    // 1. TƯƠNG TÁC GIAO DIỆN CƠ BẢN
    // ==========================================

    // Thanh điều hướng (Header) dính chặt khi cuộn trang
    const header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('sticky-header', 'shadow-sm');
            } else {
                header.classList.remove('sticky-header', 'shadow-sm');
            }
        });
    }

    // ==========================================
    // 2. TÍNH TOÁN GIÁ TIỀN ĐẶT TOUR (REAL-TIME)
    // ==========================================
    const adultInput = document.getElementById('qty_adult');
    const childInput = document.getElementById('qty_child');
    const totalPriceDisplay = document.getElementById('total-price-display');
    const basePriceInput = document.getElementById('base-order-amount'); // Input ẩn lưu tổng tiền gốc

    const calculateTotal = () => {
        if (!adultInput || !totalPriceDisplay) return;

        // Lấy giá trị từ thuộc tính data của thẻ input (được in ra từ PHP)
        const priceAdult = parseFloat(adultInput.dataset.price) || 0;
        const priceChild = childInput ? (parseFloat(childInput.dataset.price) || 0) : 0;

        const qtyAdult = parseInt(adultInput.value) || 0;
        const qtyChild = childInput ? (parseInt(childInput.value) || 0) : 0;

        const total = (qtyAdult * priceAdult) + (qtyChild * priceChild);
        
        // Hiển thị ra màn hình
        totalPriceDisplay.innerText = formatCurrency(total);
        
        // Cập nhật vào input ẩn để phần Coupon lấy dữ liệu
        if (basePriceInput) basePriceInput.value = total;

        // Reset lại coupon nếu đang có (vì tổng tiền thay đổi thì mã giảm giá phải tính lại)
        const couponMsg = document.getElementById('coupon-message');
        if (couponMsg) couponMsg.innerHTML = '';
    };

    // Gắn sự kiện thay đổi số lượng
    if (adultInput) adultInput.addEventListener('input', calculateTotal);
    if (childInput) childInput.addEventListener('input', calculateTotal);


    // ==========================================
    // 3. XỬ LÝ AJAX WISHLIST (THÊM/XÓA YÊU THÍCH)
    // ==========================================
    const wishlistButtons = document.querySelectorAll('.btn-wishlist');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const tourId = this.getAttribute('data-id');
            if (!tourId) return;

            const icon = this.querySelector('i');
            const originalClass = icon.className;
            icon.className = 'fas fa-spinner fa-spin'; // Đổi icon thành vòng xoay loading

            fetch('/ajax/wishlist/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `tour_id=${tourId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (data.success) {
                    if (data.action === 'added') {
                        icon.className = 'fas fa-heart text-danger';
                    } else {
                        icon.className = 'far fa-heart';
                    }
                    showToast(data.message, 'success');
                } else {
                    icon.className = originalClass;
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Wishlist Error:', error);
                icon.className = originalClass;
                showToast('Lỗi kết nối máy chủ!', 'error');
            });
        });
    });

    // ==========================================
    // 4. XỬ LÝ AJAX KIỂM TRA MÃ GIẢM GIÁ (COUPON)
    // ==========================================
    const applyCouponBtn = document.getElementById('btn-apply-coupon');
    
    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const couponCode = document.getElementById('coupon-code').value.trim();
            const orderAmount = document.getElementById('base-order-amount') ? document.getElementById('base-order-amount').value : 0;

            if (couponCode === '') {
                showToast('Vui lòng nhập mã giảm giá!', 'error');
                return;
            }

            if (orderAmount <= 0) {
                showToast('Vui lòng chọn số lượng khách trước khi áp mã!', 'error');
                return;
            }

            const btnText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';
            this.disabled = true;

            fetch('/api/coupon/check', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `code=${couponCode}&amount=${orderAmount}`
            })
            .then(response => response.json())
            .then(data => {
                this.innerHTML = btnText;
                this.disabled = false;

                const messageBox = document.getElementById('coupon-message');

                if (data.success) {
                    messageBox.innerHTML = `<div class="mt-2 text-success small fw-bold"><i class="fas fa-check-circle"></i> Đã áp dụng mã ${data.code}. Giảm: ${data.discount_formatted}</div>`;
                    
                    // Cập nhật lại tổng tiền thanh toán cuối cùng
                    const finalAmountDisplay = document.getElementById('total-price-display');
                    if (finalAmountDisplay) {
                        finalAmountDisplay.innerHTML = `<span class="text-muted text-decoration-line-through me-2" style="font-size: 0.8em;">${formatCurrency(orderAmount)}</span> <span class="text-danger">${data.final_amount_formatted}</span>`;
                    }
                    showToast('Áp dụng mã giảm giá thành công!', 'success');
                } else {
                    messageBox.innerHTML = `<div class="mt-2 text-danger small"><i class="fas fa-exclamation-triangle"></i> ${data.message}</div>`;
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Coupon Error:', error);
                this.innerHTML = btnText;
                this.disabled = false;
                showToast('Không thể kiểm tra mã lúc này!', 'error');
            });
        });
    }

});