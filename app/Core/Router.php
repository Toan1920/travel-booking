<?php
namespace App\Core;

class Router
{
    protected $routes = [];

    // Trả về $this để cho phép Method Chaining (Nối chuỗi hàm)
    public function get($uri, $action)
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        return $this->addRoute('POST', $uri, $action);
    }

    private function addRoute($method, $uri, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'middleware' => null // Mặc định không có bộ lọc
        ];
        
        return $this; // Trả về chính đối tượng Router
    }

    // Khai báo Middleware cho route vừa tạo
    public function middleware($key)
    {
        // Gắn middleware vào route cuối cùng vừa được đưa vào mảng
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;
        return $this;
    }

    // Thực thi điều hướng
    public function dispatch($uri, $method)
    {
        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route['uri']);
            $pattern = "@^" . $pattern . "$@D";

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches); 
                
                // --- BƯỚC CHẶN MIDDLEWARE ---
                // Xử lý kiểm tra quyền TRƯỚC KHI cho phép gọi Controller
                if ($route['middleware']) {
                    $this->handleMiddleware($route['middleware']);
                }

                $action = $route['action'];

                if (is_callable($action)) {
                    return call_user_func_array($action, $matches);
                }

                if (is_array($action)) {
                    $controllerName = $action[0];
                    $methodName = $action[1];

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                        if (method_exists($controller, $methodName)) {
                            return call_user_func_array([$controller, $methodName], $matches);
                        }
                    }
                }
            }
        }

        $this->abort();
    }

    // ==========================================
    // CÁC HÀM HỖ TRỢ LÕI
    // ==========================================

    private function handleMiddleware($middleware)
    {
        // 1. Chặn người dùng chưa đăng nhập
        if ($middleware === 'auth') {
            if (!isLoggedIn()) {
                // Lưu lại URL hiện tại để sau khi đăng nhập xong thì trả về đúng trang đó
                $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
                redirect(SITE_URL . '/login');
            }
        }
        
        // 2. Chặn người dùng ĐÃ đăng nhập (Dùng cho trang Login/Register)
        if ($middleware === 'guest') {
            if (isLoggedIn()) {
                redirect(SITE_URL . '/');
            }
        }
    }

    private function abort($code = 404)
    {
        http_response_code($code);
        
        // Tự động tìm kiếm View 404 chuyên nghiệp nếu có
        $viewPath = __DIR__ . '/../../views/pages/404.blade.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // Hiển thị dự phòng nếu chưa code giao diện 404
            echo "<div style='text-align:center; margin-top:100px; font-family:sans-serif;'>";
            echo "<h1 style='color:#e74c3c; font-size: 50px;'>404</h1>";
            echo "<h2>Oops! Trang bạn tìm kiếm không tồn tại.</h2>";
            echo "<a href='" . SITE_URL . "' style='text-decoration:none; padding:10px 20px; background:#3498db; color:#fff; border-radius:5px;'>Về Trang Chủ</a>";
            echo "</div>";
        }
        exit();
    }
}