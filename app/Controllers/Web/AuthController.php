<?php
namespace App\Controllers\Web;

use App\Core\Controller; // <-- ĐÃ SỬA LẠI ĐƯỜNG DẪN CHUẨN
use App\Models\User;

class AuthController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ FORM ĐĂNG NHẬP (GET)
    // ==========================================
    public function showLoginForm()
    {
        // Chặn nếu đã đăng nhập
        if (isLoggedIn()) {
            redirect(isAdmin() ? SITE_URL . '/admin' : SITE_URL . '/user/dashboard');
        }

        // Tạo CSRF Token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Lấy lỗi từ Session (nếu bị Redirect từ hàm POST login về)
        $error = $_SESSION['auth_error'] ?? null;
        $oldEmail = $_SESSION['old_email'] ?? '';
        
        // Xóa lỗi ngay sau khi lấy ra (Flash Data)
        unset($_SESSION['auth_error'], $_SESSION['old_email']);

        return $this->view('pages/auth/login', [
            'pageTitle' => 'Đăng nhập - TravelVN',
            'error' => $error,
            'oldEmail' => $oldEmail
        ]);
    }

    // ==========================================
    // 2. XỬ LÝ LOGIC ĐĂNG NHẬP (POST)
    // ==========================================
    public function login()
    {
        // Kiểm tra Token bảo mật
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật: Token không hợp lệ!");
        }

        // Lấy và dọn dẹp dữ liệu
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Hàm ẩn xử lý lỗi: Lưu thông báo vào Session và Redirect về Form
        $backWithError = function($message) use ($email) {
            $_SESSION['auth_error'] = $message;
            $_SESSION['old_email'] = $email;
            redirect(SITE_URL . '/login');
        };

        // Kiểm tra Rate Limit
        if (!checkLoginRateLimit()) {
            $backWithError('Tài khoản tạm khóa do nhập sai nhiều lần. Thử lại sau 5 phút.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $backWithError('Định dạng email không hợp lệ!');
        }

        // Tương tác Model
        $userModel = new User();
        $user = $userModel->getUserByEmail($email);

        // Xác thực mật khẩu
        if ($user && password_verify($password, $user['password'])) {
            
            // --- ĐĂNG NHẬP THÀNH CÔNG ---
            resetLoginRateLimit();
            $this->createSession($user); // Đẩy việc set Session ra một hàm riêng
            
            showMessage('Xin chào, ' . htmlspecialchars($user['full_name']));

            // Điều hướng
            if ($user['role'] === 'admin') {
                redirect(SITE_URL . '/admin');
            } else {
                $redirectUrl = $_SESSION['redirect_url'] ?? (SITE_URL . '/user/dashboard');
                unset($_SESSION['redirect_url']);
                redirect($redirectUrl);
            }
        } else {
            // --- ĐĂNG NHẬP THẤT BẠI ---
            recordFailedLoginAttempt();
            $backWithError('Email hoặc mật khẩu không chính xác!');
        }
    }

    // ==========================================
    // 3. XỬ LÝ ĐĂNG XUẤT (LOGOUT)
    // ==========================================
    public function logout()
    {
        // 1. Xóa tất cả các biến trong Session hiện tại
        $_SESSION = array();

        // 2. Hủy Cookie của Session trên trình duyệt
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 3. Hủy Session trên Server
        session_destroy();

        // 4. Khởi động lại một Session mới tinh để lưu thông báo mượt mà
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            session_start();
        }

        // 5. Bắn thông báo và chuyển hướng về trang chủ hoặc trang đăng nhập
        showMessage('Bạn đã đăng xuất thành công.', 'success');
        redirect(SITE_URL . '/login');
    }

    // ==========================================
    // 4. HIỂN THỊ FORM ĐĂNG KÝ (GET)
    // ==========================================
    public function showRegisterForm()
    {
        if (isLoggedIn()) {
            redirect(SITE_URL . '/');
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $error = $_SESSION['auth_error'] ?? null;
        $oldData = $_SESSION['old_data'] ?? [];
        unset($_SESSION['auth_error'], $_SESSION['old_data']);

        return $this->view('pages/auth/register', [
            'pageTitle' => 'Đăng ký tài khoản - TravelVN',
            'error' => $error,
            'oldData' => $oldData
        ]);
    }

    // ==========================================
    // 5. XỬ LÝ LOGIC ĐĂNG KÝ (POST)
    // ==========================================
    public function register()
    {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật: Token không hợp lệ!");
        }

        // Lấy và dọn dẹp dữ liệu
        $fullName = sanitize($_POST['full_name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $phone = sanitize($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $backWithError = function($message) use ($fullName, $email, $phone) {
            $_SESSION['auth_error'] = $message;
            $_SESSION['old_data'] = [
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone
            ];
            redirect(SITE_URL . '/register');
        };

        // Validate cơ bản
        if (!$fullName || !$email || !$password) {
            $backWithError('Vui lòng điền đầy đủ các thông পুরা bắt buộc!');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $backWithError('Định dạng email không hợp lệ!');
        }
        if (strlen($password) < 6) {
            $backWithError('Mật khẩu phải có ít nhất 6 ký tự!');
        }
        if ($password !== $confirmPassword) {
            $backWithError('Mật khẩu xác nhận không khớp!');
        }

        $userModel = new User();

        // Kiểm tra Email đã tồn tại chưa
        if ($userModel->getUserByEmail($email)) {
            $backWithError('Email này đã được sử dụng. Vui lòng chọn email khác!');
        }

        // Mã hóa mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Tạo User mới
        $data = [
            'full_name' => $fullName,
            'email' => $email,
            'password' => $hashedPassword,
            'phone' => $phone
        ];

        $userId = $userModel->createUser($data);

        if ($userId) {
            // Đăng ký thành công -> Tự động đăng nhập cho user
            $newUser = $userModel->getUserByEmail($email);
            $this->createSession($newUser);
            
            showMessage('Đăng ký tài khoản thành công! Chào mừng ' . htmlspecialchars($fullName));
            redirect(SITE_URL . '/user/dashboard');
        } else {
            $backWithError('Có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại!');
        }
    }

    // ==========================================
    // 6. HIỂN THỊ FORM QUÊN MẬT KHẨU (GET)
    // ==========================================
    public function showForgotPasswordForm()
    {
        if (isLoggedIn()) redirect(SITE_URL);
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $error = $_SESSION['error'] ?? '';
        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['error'], $_SESSION['message']);

        return $this->view('pages/auth/forgot-password', [
            'pageTitle' => 'Quên mật khẩu - TravelVN',
            'error' => $error,
            'message' => $message
        ]);
    }

    // ==========================================
    // 7. XỬ LÝ GỬI EMAIL KHÔI PHỤC (POST)
    // ==========================================
    public function processForgotPassword()
    {
        if (isLoggedIn()) redirect(SITE_URL);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(SITE_URL . '/forgot-password');

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật: Token không hợp lệ!");
        }

        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ.';
            redirect(SITE_URL . '/forgot-password');
        }

        $db = \App\Core\Database::getInstance();
        $stmt = $db->query("SELECT id, full_name FROM users WHERE email = ?", [$email]);
        $user = $stmt ? $stmt->fetch() : null;

        if ($user) {
            $token = bin2hex(random_bytes(32)); 
            $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 tiếng

            if ($db->execute("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?", [$token, $expiry, $user['id']])) {
                
                // Chuẩn bị nội dung Email
                $resetLink = SITE_URL . "/reset-password?email=" . urlencode($email) . "&token=" . $token;
                $subject = "Khôi phục mật khẩu - TravelVN";
                $content = "
                    <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
                        <h3>Xin chào {$user['full_name']},</h3>
                        <p>Bạn vừa yêu cầu đặt lại mật khẩu tại TravelVN.</p>
                        <p>Vui lòng nhấn vào nút dưới đây để tạo mật khẩu mới (Liên kết có hiệu lực trong 1 giờ):</p>
                        <p><a href='{$resetLink}' style='background:#0d6efd; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>ĐẶT LẠI MẬT KHẨU</a></p>
                        <p>Hoặc copy link này dán vào trình duyệt: <br><a href='{$resetLink}'>{$resetLink}</a></p>
                        <p style='color: #888; font-size: 0.9em;'>Nếu bạn không yêu cầu, vui lòng bỏ qua email này.</p>
                    </div>
                ";

                // Gọi Helper gửi Email
                if (\App\Helpers\MailHelper::send($email, $subject, $content)) {
                    $_SESSION['message'] = "Đã gửi email khôi phục thành công! Vui lòng kiểm tra hộp thư đến (và mục Spam).";
                } else {
                    $_SESSION['error'] = "Không thể gửi email do lỗi cấu hình máy chủ SMTP. Vui lòng thử lại sau.";
                }
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi tạo mã khôi phục.";
            }
        } else {
            $_SESSION['error'] = "Email này chưa được đăng ký trong hệ thống.";
        }

        redirect(SITE_URL . '/forgot-password');
    }

    // ==========================================
    // 8. HIỂN THỊ FORM ĐẶT LẠI MẬT KHẨU (GET)
    // ==========================================
    public function showResetPasswordForm()
    {
        if (isLoggedIn()) redirect(SITE_URL);

        $email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);
        $token = sanitize($_GET['token'] ?? '');

        if (!$email || !$token) {
            redirect(SITE_URL . '/login');
        }

        $db = \App\Core\Database::getInstance();
        
        // Kiểm tra Token còn hạn không
        $stmt = $db->query("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()", [$email, $token]);
        $isValidLink = $stmt && $stmt->rowCount() > 0;
        
        $error = $_SESSION['error'] ?? ($isValidLink ? '' : 'Liên kết khôi phục không hợp lệ hoặc đã hết hạn!');
        $success = $_SESSION['success'] ?? '';
        unset($_SESSION['error'], $_SESSION['success']);

        return $this->view('pages/auth/reset-password', [
            'pageTitle' => 'Đặt lại mật khẩu - TravelVN',
            'email' => $email,
            'token' => $token,
            'isValidLink' => $isValidLink,
            'error' => $error,
            'success' => $success
        ]);
    }

    // ==========================================
    // 9. XỬ LÝ ĐỔI MẬT KHẨU MỚI (POST)
    // ==========================================
    public function processResetPassword()
    {
        if (isLoggedIn()) redirect(SITE_URL);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(SITE_URL . '/login');

        // Nhận dữ liệu ẩn và dữ liệu form
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $token = sanitize($_POST['token'] ?? '');
        $pass = $_POST['password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        $redirectUrl = SITE_URL . "/reset-password?email=" . urlencode($email) . "&token=" . $token;

        $db = \App\Core\Database::getInstance();
        
        // KIỂM TRA LẠI LẦN NỮA ĐỂ CHỐNG HACK MŨ ĐEN (Luôn check DB ở POST)
        $stmt = $db->query("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()", [$email, $token]);
        
        if (!$stmt || $stmt->rowCount() === 0) {
            $_SESSION['error'] = "Phiên giao dịch không hợp lệ hoặc đã hết hạn!";
            redirect($redirectUrl);
        }

        if (strlen($pass) < 6) {
            $_SESSION['error'] = "Mật khẩu phải có ít nhất 6 ký tự.";
            redirect($redirectUrl);
        } elseif ($pass !== $confirmPass) {
            $_SESSION['error'] = "Mật khẩu xác nhận không khớp.";
            redirect($redirectUrl);
        } else {
            $newHash = password_hash($pass, PASSWORD_DEFAULT);
            
            // Xóa token và cập nhật mật khẩu mới
            $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE email = ?";
            if ($db->execute($sql, [$newHash, $email])) {
                $_SESSION['success'] = "Đổi mật khẩu thành công! Bạn có thể đăng nhập ngay bây giờ.";
            } else {
                $_SESSION['error'] = "Lỗi hệ thống, vui lòng thử lại.";
            }
            redirect($redirectUrl);
        }
    }

    // ==========================================
    // CÁC HÀM HỖ TRỢ (PRIVATE)
    // ==========================================
    
    // Gói gọn việc tạo Session để tái sử dụng (cho cả Đăng nhập & Đăng ký)
    private function createSession($user)
    {
        session_regenerate_id(true); // Ngăn chặn Session Fixation Attack
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'] ?? '';
    }
}