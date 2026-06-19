</div> </main> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. Xử lý Toggle Sidebar trên màn hình nhỏ (Mobile/Tablet)
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if(toggleBtn && sidebar){
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active'); // CSS của cụ sẽ lo việc ẩn/hiện
        });
    }

    // 2. Tự động tắt các thông báo (Flash messages) một cách mượt mà sau 4 giây
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(el => {
                el.style.transition = "opacity 0.5s ease"; // Thêm hiệu ứng mờ dần
                el.style.opacity = "0";
                setTimeout(() => el.style.display = 'none', 500); // Đợi mờ xong thì ẩn hẳn khỏi DOM
            });
        }, 4000);
    }
</script>

</body>
</html>