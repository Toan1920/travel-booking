<?php
// public/index.php
// 🚀 ENTRY POINT - Nơi tiếp nhận mọi request của ứng dụng

// 1. Nạp autoloader của Composer
 require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
use App\Core\Router;

// 2. Load biến môi trường từ .env
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
} catch (Exception $e) {
    die("Lỗi: Không tìm thấy file cấu hình .env. Vui lòng kiểm tra lại!");
}

// 3. Cấu hình hệ thống
define('SITE_URL', $_ENV['APP_URL'] ?? 'http://localhost:8000');
define('IS_DEBUG', ($_ENV['APP_DEBUG'] ?? 'false') === 'true');
define('ADMIN_URL', SITE_URL . '/admin');

define('UPLOAD_PATH', __DIR__ . '/uploads/'); 
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('LOG_PATH', __DIR__ . '/../storage/logs/');

// Bật/tắt hiển thị lỗi PHP toàn cầu dựa vào cấu hình .env (Cực kỳ quan trọng)
if (IS_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Headers bảo mật chống tấn công
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Content-Type: text/html; charset=utf-8');
header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' data: https:;");

// Session Setup
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.gc_maxlifetime', 604800);
    session_start();
}
date_default_timezone_set('Asia/Ho_Chi_Minh');

// BẮT BUỘC NẠP HELPERS ĐỂ ỨNG DỤNG CÓ THỂ GỌI CÁC HÀM TIỆN ÍCH
require_once __DIR__ . '/../app/Helpers/functions.php';

// 4. Khởi tạo Router và Phân luồng (Routing)
$router = new Router();

// Nạp bản đồ đường dẫn
require_once __DIR__ . '/../routes/web.php';

// Nạp đường dẫn API nếu file tồn tại
if (file_exists(__DIR__ . '/../routes/api.php')) {
    require_once __DIR__ . '/../routes/api.php';
}

// Bóc tách URI hiện tại để Router xử lý
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Xử lý Base Path nếu project chạy trong subfolder (VD: localhost/my_project/public)
$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$route = str_replace($basePath, '', $requestUri);

if ($route === '' || $route === '/') {
    $route = '/';
}

// Bắt đầu định tuyến
$router->dispatch($route, $requestMethod);