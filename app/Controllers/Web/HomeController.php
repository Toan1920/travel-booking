<?php
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Models\Tour;
use App\Models\Review;

class HomeController extends Controller
{
    // ==========================================
    // 1. TRANG CHỦ (INDEX)
    // ==========================================
    public function index()
    {
        try {
            // 1. Khởi tạo các Model
            $tourModel = new Tour();
            $reviewModel = new Review();

            // 2. Lấy dữ liệu thông qua các hàm của Model
            $featuredTours = $tourModel->getFeaturedTours(6);
            $flashSaleTours = $tourModel->getFlashSaleTours(4);
            $reviews = $reviewModel->getLatestReviews(6);

            $pageTitle = 'Trang chủ - Vi vu khắp chốn';

            // 3. Gọi View và truyền dữ liệu
            return $this->view('pages/home', [
                'pageTitle' => $pageTitle,
                'featuredTours' => $featuredTours,
                'flashSaleTours' => $flashSaleTours,
                'reviews' => $reviews
            ]);

        } catch (\Exception $e) {
            // Ghi log lỗi vào file storage/logs/error_log.txt để dev kiểm tra
            error_log("Lỗi tải trang chủ: " . $e->getMessage());

            // Có thể trả về một view báo lỗi chung hoặc view trang chủ với dữ liệu rỗng
            return $this->view('pages/home', [
                'pageTitle' => 'Trang chủ - Đang cập nhật',
                'featuredTours' => [],
                'flashSaleTours' => [],
                'reviews' => []
            ]);
        }
    }

    // ==========================================
    // 2. TRANG GIỚI THIỆU (ABOUT)
    // ==========================================
    public function about()
    {
        $db = \App\Core\Database::getInstance();
        $stats = [
            'tours' => 0,
            'customers' => 0,
            'reviews' => 0
        ];

        try {
            // Đếm số tour đang hoạt động
            $stmtTour = $db->query("SELECT COUNT(id) as total FROM tours WHERE status = 'active'");
            $stats['tours'] = $stmtTour ? $stmtTour->fetch()['total'] : 0;

            // Đếm số khách hàng
            $stmtUser = $db->query("SELECT COUNT(id) as total FROM users WHERE role = 'customer'");
            $stats['customers'] = $stmtUser ? $stmtUser->fetch()['total'] : 0;

            // Đếm số đánh giá tích cực
            $stmtReview = $db->query("SELECT COUNT(id) as total FROM reviews WHERE status = 'approved'");
            $stats['reviews'] = $stmtReview ? $stmtReview->fetch()['total'] : 0;
        } catch (\Exception $e) {
            // Bỏ qua lỗi DB để trang không bị sập, giữ nguyên các số 0
        }

        return $this->view('pages/about', [
            'pageTitle' => 'Về chúng tôi - Câu chuyện thương hiệu',
            'stats' => $stats
        ]);
    }

    // ==========================================
    // 3. TRANG LIÊN HỆ (GET)
    // ==========================================
    public function contact()
    {
        // Tạo CSRF Token nếu chưa có
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Tự động điền thông tin nếu đã đăng nhập
        $preFillName = isLoggedIn() ? $_SESSION['full_name'] : '';
        $preFillEmail = isLoggedIn() ? $_SESSION['email'] : '';

        return $this->view('pages/contact', [
            'pageTitle' => 'Liên hệ - TravelVN',
            'preFillName' => $preFillName,
            'preFillEmail' => $preFillEmail
        ]);
    }

    // ==========================================
    // 4. XỬ LÝ GỬI FORM LIÊN HỆ (POST)
    // ==========================================
    public function submitContact()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/contact');
        }

        // Validate CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật: Token không hợp lệ. Vui lòng tải lại trang!");
        }

        $fullName = sanitize($_POST['full_name']);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $message = sanitize($_POST['message']);
        
        $userId = isLoggedIn() ? $_SESSION['user_id'] : null;

        if (!$email || empty($fullName) || empty($message)) {
            showMessage('Vui lòng điền đầy đủ thông tin bắt buộc!', 'danger');
        } else {
            $db = \App\Core\Database::getInstance();
            $sql = "INSERT INTO contact_messages (user_id, full_name, email, message) VALUES (?, ?, ?, ?)";
            
            // Dùng hàm execute() để thực thi lệnh INSERT
            $result = $db->execute($sql, [$userId, $fullName, $email, $message]);

            if ($result) {
                showMessage('Tin nhắn đã được gửi thành công! Chúng tôi sẽ phản hồi sớm nhất.', 'success');
            } else {
                showMessage('Có lỗi xảy ra từ máy chủ, vui lòng thử lại sau.', 'danger');
            }
        }

        // Redirect về lại trang liên hệ để xóa sạch data POST, tránh resubmit khi F5
        redirect(SITE_URL . '/contact');
    }
}