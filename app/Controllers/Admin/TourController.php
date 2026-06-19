<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class TourController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ DANH SÁCH TOUR (GET)
    // ==========================================
    public function index()
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        // Tạo Token dùng cho các nút Xóa và Toggle
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();

        // Xử lý Tìm kiếm và Phân trang
        $search = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $sqlWhere = "WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sqlWhere .= " AND t.title LIKE ?";
            $params[] = "%$search%";
        }

        // Đếm tổng số để phân trang
        $stmtCount = $db->query("SELECT COUNT(*) as total FROM tours t $sqlWhere", $params);
        $total = $stmtCount ? $stmtCount->fetch()['total'] : 0;
        $totalPages = ceil($total / $limit);

        // Lấy dữ liệu (Dùng mảng tham số riêng biệt cho LIMIT để tránh lỗi)
        $sql = "SELECT t.*, c.name as cat_name 
                FROM tours t 
                LEFT JOIN categories c ON t.category_id = c.id 
                $sqlWhere 
                ORDER BY t.id DESC 
                LIMIT $limit OFFSET $offset";
        
        $stmt = $db->query($sql, $params);
        $tours = $stmt ? $stmt->fetchAll() : [];

        return $this->view('admin/tours', [
            'pageTitle' => 'Quản lý Tour - Admin Panel',
            'tours' => $tours,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    // ==========================================
    // 2. BẬT/TẮT NỔI BẬT & FLASH SALE (POST)
    // ==========================================
    public function toggle()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/tours');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $id = filter_input(INPUT_POST, 'tour_id', FILTER_VALIDATE_INT);
        $action = $_POST['action'] ?? '';

        if ($id && in_array($action, ['featured', 'flash_sale'])) {
            $db = Database::getInstance();
            // Lệnh SQL đảo ngược trạng thái boolean (!featured hoặc !flash_sale)
            $sql = "UPDATE tours SET $action = NOT $action WHERE id = ?";
            if ($db->execute($sql, [$id])) {
                $_SESSION['success'] = "Đã cập nhật trạng thái thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật.";
            }
        }
        
        redirect(SITE_URL . '/admin/tours');
    }

    // ==========================================
    // 3. XÓA TOUR (POST)
    // ==========================================
    public function destroy()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/tours');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
        if ($id) {
            $db = Database::getInstance();
            
            // LƯU Ý: Nếu tour đã có người đặt, bạn nên cân nhắc đổi status='inactive' thay vì DELETE cứng.
            // Ở đây tạm giữ logic Xóa cứng của bạn
            if ($db->execute("DELETE FROM tours WHERE id = ?", [$id])) {
                $_SESSION['success'] = 'Đã xóa tour thành công!';
            } else {
                $_SESSION['error'] = 'Không thể xóa do ràng buộc dữ liệu (Tour có thể đã có đơn đặt).';
            }
        }

        redirect(SITE_URL . '/admin/tours');
    }
// ==========================================
    // 4. HIỂN THỊ FORM SỬA TOUR (GET)
    // ==========================================
    public function edit($id)
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();
        
        // Lấy thông tin tour
        $stmtTour = $db->query("SELECT * FROM tours WHERE id = ?", [$id]);
        $tour = $stmtTour ? $stmtTour->fetch() : null;

        if (!$tour) {
            $_SESSION['error'] = 'Tour không tồn tại trên hệ thống!';
            redirect(SITE_URL . '/admin/tours');
        }

        // Lấy danh sách danh mục để đổ vào thẻ <select>
        $stmtCats = $db->query("SELECT id, name FROM categories ORDER BY name ASC");
        $categories = $stmtCats ? $stmtCats->fetchAll() : [];

        return $this->view('admin/tour-edit', [
            'pageTitle' => 'Sửa Tour: ' . $tour['title'] . ' - Admin Panel',
            'tour' => $tour,
            'categories' => $categories
        ]);
    }

    // ==========================================
    // 5. XỬ LÝ CẬP NHẬT TOUR (POST)
    // ==========================================
    public function update($id)
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/tours');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $db = Database::getInstance();
        
        // 1. Lấy thông tin tour cũ (để xử lý mảng ảnh)
        $stmtOld = $db->query("SELECT images FROM tours WHERE id = ?", [$id]);
        $oldTour = $stmtOld ? $stmtOld->fetch() : null;
        if (!$oldTour) redirect(SITE_URL . '/admin/tours');

        // 2. Nhận và làm sạch dữ liệu
        $title = sanitize($_POST['title'] ?? '');
        $slug = !empty($_POST['slug']) ? generateSlug($_POST['slug']) : generateSlug($title);
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $price_adult = filter_input(INPUT_POST, 'price_adult', FILTER_VALIDATE_FLOAT) ?: 0;
        $price_child = filter_input(INPUT_POST, 'price_child', FILTER_VALIDATE_FLOAT) ?: 0;
        
        $duration = sanitize($_POST['duration'] ?? '');
        $destination = sanitize($_POST['destination'] ?? '');
        $description = $_POST['description'] ?? ''; // Nên giữ lại format HTML cơ bản nếu dùng trình soạn thảo
        $itinerary = $_POST['itinerary'] ?? '';
        $includes = $_POST['includes'] ?? '';
        $excludes = $_POST['excludes'] ?? '';
        $status = sanitize($_POST['status'] ?? 'active');

        // 3. XỬ LÝ ẢNH (Upload nhiều ảnh)
       // 3. XỬ LÝ ẢNH (Xóa từng ảnh chọn lọc & Upload thêm ảnh mới)
        $currentImages = json_decode($oldTour['images'], true) ?: [];

        // LÀM MỚI: Xử lý xóa từng ảnh riêng biệt được Admin tích chọn
        if (!empty($_POST['delete_images']) && is_array($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $imgToDelete) {
                // Kiểm tra xem tên ảnh có thực sự nằm trong danh sách ảnh của Tour không (tránh xóa bậy)
                if (($key = array_search($imgToDelete, $currentImages)) !== false) {
                    // 1. Xóa file vật lý khỏi ổ cứng server
                    $filePath = UPLOAD_PATH . 'tours/' . $imgToDelete;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    // 2. Xóa tên file ra khỏi mảng quản lý của Tour
                    unset($currentImages[$key]);
                }
            }
            // Reset lại chỉ số mảng (0, 1, 2...) sau khi dùng lệnh unset
            $currentImages = array_values($currentImages);
        }

        // Nếu có upload thêm ảnh mới -> Tiến hành lưu đè nối tiếp vào mảng
        if (!empty($_FILES['tour_images']['name'][0])) {
            $fileCount = count($_FILES['tour_images']['name']);
            if (!is_dir(UPLOAD_PATH . 'tours/')) mkdir(UPLOAD_PATH . 'tours/', 0777, true);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['tour_images']['error'][$i] === 0) {
                    $ext = pathinfo($_FILES['tour_images']['name'][$i], PATHINFO_EXTENSION);
                    $newName = time() . '_' . uniqid() . '.' . $ext;
                    $dest = UPLOAD_PATH . 'tours/' . $newName;
                    
                    if (move_uploaded_file($_FILES['tour_images']['tmp_name'][$i], $dest)) {
                        $currentImages[] = $newName; // Nối thêm ảnh mới vào kho ảnh hiện tại
                    }
                }
            }
        }

        $jsonImages = json_encode($currentImages); // Đóng gói lại thành chuỗi JSON sạch sẽ

        // 4. Thực thi Update
        $sql = "UPDATE tours SET category_id=?, title=?, slug=?, description=?, itinerary=?, 
                duration=?, destination=?, price_adult=?, price_child=?, includes=?, excludes=?, 
                images=?, status=? WHERE id=?";
                
        try {
            $db->execute($sql, [
                $category_id, $title, $slug, $description, $itinerary, 
                $duration, $destination, $price_adult, $price_child, 
                $includes, $excludes, $jsonImages, $status, $id
            ]);
            
            $_SESSION['success'] = 'Cập nhật tour thành công!';
            redirect(SITE_URL . '/admin/tours');
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
            redirect(SITE_URL . '/admin/tours/edit/' . $id);
        }
    }

// ==========================================
    // 6. HIỂN THỊ FORM THÊM TOUR MỚI (GET)
    // ==========================================
    public function create()
    {
        if (!isAdmin()) redirect(SITE_URL . '/login');

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $db = Database::getInstance();
        
        // Lấy danh sách danh mục để đổ vào thẻ <select>
        $stmtCats = $db->query("SELECT id, name FROM categories ORDER BY name ASC");
        $categories = $stmtCats ? $stmtCats->fetchAll() : [];

        return $this->view('admin/tour-create', [
            'pageTitle' => 'Thêm Tour Mới - Admin Panel',
            'categories' => $categories
        ]);
    }

    // ==========================================
    // 7. XỬ LÝ LƯU TOUR MỚI (POST)
    // ==========================================
    public function store()
    {
        if (!isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/tours');
        }

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật CSRF!");
        }

        $title = sanitize($_POST['title'] ?? '');
        $slug = !empty($_POST['slug']) ? generateSlug($_POST['slug']) : generateSlug($title);
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $price_adult = filter_input(INPUT_POST, 'price_adult', FILTER_VALIDATE_FLOAT) ?: 0;
        $price_child = filter_input(INPUT_POST, 'price_child', FILTER_VALIDATE_FLOAT) ?: 0;
        
        $duration = sanitize($_POST['duration'] ?? '');
        $destination = sanitize($_POST['destination'] ?? '');
        $description = $_POST['description'] ?? '';
        $itinerary = $_POST['itinerary'] ?? '';
        $includes = $_POST['includes'] ?? '';
        $excludes = $_POST['excludes'] ?? '';

        // XỬ LÝ UPLOAD NHIỀU ẢNH
        $imagePaths = [];
        if (!empty($_FILES['tour_images']['name'][0])) {
            $fileCount = count($_FILES['tour_images']['name']);
            if (!is_dir(UPLOAD_PATH . 'tours/')) mkdir(UPLOAD_PATH . 'tours/', 0777, true);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['tour_images']['error'][$i] === 0) {
                    $ext = pathinfo($_FILES['tour_images']['name'][$i], PATHINFO_EXTENSION);
                    $newName = time() . '_' . uniqid() . '.' . $ext;
                    $dest = UPLOAD_PATH . 'tours/' . $newName;
                    
                    if (move_uploaded_file($_FILES['tour_images']['tmp_name'][$i], $dest)) {
                        $imagePaths[] = $newName;
                    }
                }
            }
        }
        
        $jsonImages = json_encode($imagePaths);

        $db = Database::getInstance();
        $sql = "INSERT INTO tours (category_id, title, slug, description, itinerary, duration, destination, price_adult, price_child, includes, excludes, images, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
                
        try {
            $db->execute($sql, [
                $category_id, $title, $slug, $description, $itinerary, 
                $duration, $destination, $price_adult, $price_child, 
                $includes, $excludes, $jsonImages
            ]);
            
            $_SESSION['success'] = 'Thêm tour mới thành công!';
            redirect(SITE_URL . '/admin/tours');
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
            redirect(SITE_URL . '/admin/tours/create');
        }
    }
}