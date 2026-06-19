<?php
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Database;

class TourController extends Controller
{
    // ==========================================
    // 1. TRANG DANH SÁCH TOUR (Có Lọc & Phân trang)
    // ==========================================
    public function index()
    {
        $db = Database::getInstance();

        // 1. Nhận các tham số từ URL (GET)
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = 9; // Hiển thị 9 tour 1 trang
        $offset = ($page - 1) * $limit;

        $catSlug = sanitize($_GET['cat'] ?? '');
        $search = sanitize($_GET['q'] ?? '');
        $priceRange = sanitize($_GET['price'] ?? '');
        $sort = sanitize($_GET['sort'] ?? 'newest');

        // 2. Xây dựng câu truy vấn động (Dynamic Query Builder)
        $where = ["t.status = 'active'"];
        $params = [];

        // Lọc theo danh mục
        if ($catSlug) {
            $where[] = "c.slug = ?";
            $params[] = $catSlug;
        }

        // Lọc theo từ khóa (Tìm ở Tiêu đề hoặc Điểm đến)
        if ($search) {
            $where[] = "(t.title LIKE ? OR t.destination LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Lọc theo giá (Lưu ý: Tính giá đã giảm)
        if ($priceRange) {
            $parts = explode('-', $priceRange);
            if (count($parts) == 2) {
                $where[] = "(t.price_adult * (100 - t.discount_percent) / 100) BETWEEN ? AND ?";
                $params[] = $parts[0];
                $params[] = $parts[1];
            }
        }

        $whereClause = "WHERE " . implode(' AND ', $where);

        // Sắp xếp (Sorting)
        $orderBy = "ORDER BY t.created_at DESC"; // Mặc định mới nhất
        if ($sort === 'price_asc') {
            $orderBy = "ORDER BY final_price ASC";
        } elseif ($sort === 'price_desc') {
            $orderBy = "ORDER BY final_price DESC";
        } elseif ($sort === 'name_asc') {
            $orderBy = "ORDER BY t.title ASC";
        }

        // 3. Đếm tổng số Record để chia trang
        $sqlCount = "SELECT COUNT(t.id) as total 
                     FROM tours t 
                     LEFT JOIN categories c ON t.category_id = c.id 
                     $whereClause";
        $stmtCount = $db->query($sqlCount, $params);
        $totalRecords = $stmtCount ? $stmtCount->fetch()['total'] : 0;
        $totalPages = ceil($totalRecords / $limit);

        // 4. Lấy danh sách Tour theo trang hiện tại
        $sqlTours = "SELECT t.*, c.name as cat_name, c.slug as cat_slug,
                     (t.price_adult * (100 - t.discount_percent) / 100) as final_price
                     FROM tours t 
                     LEFT JOIN categories c ON t.category_id = c.id 
                     $whereClause 
                     $orderBy 
                     LIMIT $limit OFFSET $offset";
        $stmtTours = $db->query($sqlTours, $params);
        $tours = $stmtTours ? $stmtTours->fetchAll() : [];

        // 5. Lấy danh mục cho Sidebar (Kèm số lượng tour)
        $sqlCats = "SELECT c.name, c.slug, COUNT(t.id) as count 
                    FROM categories c 
                    LEFT JOIN tours t ON c.id = t.category_id AND t.status = 'active'
                    GROUP BY c.id HAVING count > 0";
        $stmtCats = $db->query($sqlCats);
        $sidebarCategories = $stmtCats ? $stmtCats->fetchAll() : [];

        // 6. Xử lý tên danh mục hiển thị trên Tiêu đề
        $catName = 'Tất cả Tour';
        if ($catSlug) {
            $key = array_search($catSlug, array_column($sidebarCategories, 'slug'));
            if ($key !== false) {
                $catName = $sidebarCategories[$key]['name'];
            }
        }

        // Trả dữ liệu về cho View (tours.blade.php)
        return $this->view('pages/tours', [
            'pageTitle' => $catName . ' - TravelVN',
            'tours' => $tours,
            'catSlug' => $catSlug,
            'catName' => $catName,
            'search' => $search,
            'priceRange' => $priceRange,
            'sort' => $sort,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'sidebarCategories' => $sidebarCategories
        ]);
    }

    // ==========================================
    // 2. TRANG CHI TIẾT TOUR
    // ==========================================
    public function detail($slug)
    {
        $slug = sanitize($slug);
        if (!$slug) {
            redirect(SITE_URL . '/tours');
        }

        $db = Database::getInstance();

        // 1. Lấy thông tin Tour (Dùng LEFT JOIN để bảo vệ dữ liệu nếu mất danh mục)
        $sql = "SELECT t.*, c.name as cat_name, c.slug as cat_slug 
                FROM tours t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE t.slug = ? AND t.status = 'active' LIMIT 1";
        $stmt = $db->query($sql, [$slug]);
        $tour = $stmt ? $stmt->fetch() : null;

        if (!$tour) {
            redirect(SITE_URL . '/tours');
        }

        // 2. Lấy Lịch khởi hành (Chỉ lấy ngày tương lai & còn chỗ)
        $sqlDates = "SELECT id, departure_date, available_slots, price_adult, price_child
                     FROM departure_dates 
                     WHERE tour_id = ? 
                     AND departure_date >= CURDATE() 
                     AND status = 'available' 
                     AND available_slots > 0
                     ORDER BY departure_date ASC LIMIT 10";
        $stmtDates = $db->query($sqlDates, [$tour['id']]);
        $dates = $stmtDates ? $stmtDates->fetchAll() : [];

        // 3. Lấy Đánh giá (Reviews) đã được duyệt
        $sqlReviews = "SELECT r.*, u.full_name 
                       FROM reviews r 
                       LEFT JOIN users u ON r.user_id = u.id 
                       WHERE r.tour_id = ? AND r.status = 'approved' 
                       ORDER BY r.created_at DESC";
        $stmtReviews = $db->query($sqlReviews, [$tour['id']]);
        $reviews = $stmtReviews ? $stmtReviews->fetchAll() : [];

        // 4. Xử lý logic hiển thị ảnh mượt mà
        $images = json_decode($tour['images'], true) ?: [];
        $mainImg = !empty($images) ? UPLOAD_URL . 'tours/' . $images[0] : SITE_URL . '/assets/images/no-image.png';

        // Trả dữ liệu về cho View (tour-detail.blade.php)
        return $this->view('pages/tour-detail', [
            'pageTitle' => $tour['title'] . ' - TravelVN',
            'tour' => $tour,
            'dates' => $dates,
            'reviews' => $reviews,
            'images' => $images,
            'mainImg' => $mainImg
        ]);
    }
}