<?php
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Database;
use Exception;

class BookingController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ FORM ĐẶT TOUR (GET)
    // ==========================================
    public function showBookingForm()
    {
        $tourId = filter_input(INPUT_GET, 'tour_id', FILTER_VALIDATE_INT);
        $departureDateId = filter_input(INPUT_GET, 'departure_date_id', FILTER_VALIDATE_INT);
        $adults = filter_input(INPUT_GET, 'adults', FILTER_VALIDATE_INT);
        $children = filter_input(INPUT_GET, 'children', FILTER_VALIDATE_INT) ?? 0;

        if (!$tourId || !$departureDateId || !$adults) {
            showMessage('Thông tin đặt tour không hợp lệ!', 'danger');
            redirect(SITE_URL . '/tours');
        }

        $db = Database::getInstance();

        // Lấy thông tin Tour và Ngày khởi hành
        $sql = "SELECT t.id as tour_id, t.title, t.slug, t.discount_percent, t.images,
                       dd.id as date_id, dd.departure_date, dd.available_slots,
                       COALESCE(NULLIF(dd.price_adult, 0), t.price_adult) as final_price_adult,
                       COALESCE(NULLIF(dd.price_child, 0), t.price_child) as final_price_child
                FROM tours t
                JOIN departure_dates dd ON dd.tour_id = t.id 
                WHERE t.id = ? AND dd.id = ? AND t.status = 'active' AND dd.status = 'available'";

        $stmt = $db->query($sql, [$tourId, $departureDateId]);
        $tour = $stmt ? $stmt->fetch() : null;

        if (!$tour) {
            showMessage('Tour hoặc ngày khởi hành không còn khả dụng.', 'danger');
            redirect(SITE_URL . '/tours');
        }

        // Tính toán tiền cơ bản
        $discount = $tour['discount_percent'];
        $priceAdult = $tour['final_price_adult'] * (100 - $discount) / 100;
        $priceChild = $tour['final_price_child'] * (100 - $discount) / 100;
        $totalAmount = ($priceAdult * $adults) + ($priceChild * $children);

        // Xử lý ảnh thumbnail
        $images = json_decode($tour['images'], true);
        $tourThumb = (!empty($images) && is_array($images)) ? UPLOAD_URL . 'tours/' . $images[0] : SITE_URL . '/assets/images/no-image.png';

        // Tạo CSRF Token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $error = $_SESSION['booking_error'] ?? '';
        unset($_SESSION['booking_error']);

        return $this->view('pages/booking', [
            'pageTitle' => 'Xác nhận đặt tour',
            'tour' => $tour,
            'tourThumb' => $tourThumb,
            'adults' => $adults,
            'children' => $children,
            'priceAdult' => $priceAdult,
            'priceChild' => $priceChild,
            'discount' => $discount,
            'totalAmount' => $totalAmount,
            'error' => $error
        ]);
    }

    // ==========================================
    // 2. XỬ LÝ CHỐT ĐƠN VÀ THANH TOÁN (POST)
    // ==========================================
    public function checkout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(SITE_URL . '/tours');

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            die("Lỗi bảo mật: Token không hợp lệ. Vui lòng tải lại trang.");
        }

        // Nhận dữ liệu ẩn từ form
        $tourId = filter_input(INPUT_POST, 'tour_id', FILTER_VALIDATE_INT);
        $departureDateId = filter_input(INPUT_POST, 'departure_date_id', FILTER_VALIDATE_INT);
        $adults = filter_input(INPUT_POST, 'adults', FILTER_VALIDATE_INT);
        $children = filter_input(INPUT_POST, 'children', FILTER_VALIDATE_INT) ?? 0;
        
        // Hỗ trợ cả name mới (base_amount) và cũ (total_amount)
        $totalAmount = filter_input(INPUT_POST, 'base_amount', FILTER_VALIDATE_FLOAT) ?: filter_input(INPUT_POST, 'total_amount', FILTER_VALIDATE_FLOAT);

        // Nhận dữ liệu người dùng nhập
        $fullName = sanitize($_POST['full_name']);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = sanitize($_POST['phone']);
        $notes = sanitize($_POST['notes'] ?? '');
        $paymentMethod = sanitize($_POST['payment_method']); 
        $couponCode = isset($_POST['coupon_code']) ? strtoupper(sanitize($_POST['coupon_code'])) : '';

        $backWithError = function($msg) use ($tourId, $departureDateId, $adults, $children) {
            $_SESSION['booking_error'] = $msg;
            $url = SITE_URL . "/booking?tour_id=$tourId&departure_date_id=$departureDateId&adults=$adults&children=$children";
            redirect($url);
        };

        if (!$email || !$fullName || !$phone) {
            $backWithError("Vui lòng điền đầy đủ thông tin bắt buộc.");
        }

        $db = Database::getInstance();
        $totalGuests = $adults + $children;

        try {
            // 🚀 BẮT ĐẦU TRANSACTION AN TOÀN TUYỆT ĐỐI
            $db->beginTransaction();

            // A. LOCK & TRỪ SỐ LƯỢNG CHỖ
            $sqlLock = "UPDATE departure_dates SET available_slots = available_slots - ? WHERE id = ? AND available_slots >= ?";
            $stmtLock = $db->query($sqlLock, [$totalGuests, $departureDateId, $totalGuests]);
            
            if ($stmtLock->rowCount() === 0) {
                throw new Exception("Rất tiếc, tour này vừa hết chỗ trong giây lát. Vui lòng chọn ngày khác!");
            }

            // B. XỬ LÝ MÃ GIẢM GIÁ
            $discountAmount = 0;
            if ($couponCode) {
                $sqlCoupon = "SELECT * FROM coupons WHERE code = ? AND status = 'active' AND valid_to >= CURDATE() AND usage_limit > used_count FOR UPDATE";
                $stmtCoupon = $db->query($sqlCoupon, [$couponCode]);
                $coupon = $stmtCoupon ? $stmtCoupon->fetch() : null;

                if ($coupon && $totalAmount >= $coupon['min_order']) {
                    $discountAmount = ($coupon['type'] == 'percent') ? ($totalAmount * ($coupon['value'] / 100)) : $coupon['value'];
                    $db->execute("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?", [$coupon['id']]);
                }
            }

            $finalAmount = max(0, $totalAmount - $discountAmount);

            // ==========================================
            // C. LƯU ĐƠN HÀNG (BOOKING) - ĐÃ SỬA LỖI LẤY ID
            // ==========================================
            $bookingCode = 'BK' . date('ymd') . rand(1000, 9999);
            $userId = isLoggedIn() ? $_SESSION['user_id'] : null;

            $sqlInsert = "INSERT INTO bookings (user_id, tour_id, departure_date_id, booking_code, 
                          full_name, email, phone, adults, children, total_amount, 
                          discount_amount, final_amount, payment_method, notes, booking_status, payment_status, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new', 'pending', NOW())";
            
            $pdo = $db->getConnection();
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([
                $userId, $tourId, $departureDateId, $bookingCode,
                $fullName, $email, $phone, $adults, $children, $totalAmount,
                $discountAmount, $finalAmount, $paymentMethod, $notes
            ]);

            // Bắt chính xác ID vừa sinh ra bằng mã booking_code
            $stmtGetId = $pdo->prepare("SELECT id FROM bookings WHERE booking_code = ?");
            $stmtGetId->execute([$bookingCode]);
            $bookingRow = $stmtGetId->fetch();
            $bookingId = $bookingRow ? (int)$bookingRow['id'] : 0;

            if ($bookingId === 0) {
                throw new Exception("Lỗi hệ thống: Không thể tạo đơn hàng mới.");
            }

            // D. LƯU LỊCH SỬ GIAO DỊCH
            $txnRef = 'TXN_' . $bookingCode . '_' . time();
            $sqlTxn = "INSERT INTO transactions (booking_id, payment_method, transaction_ref, amount, status, created_at) 
                       VALUES (?, ?, ?, ?, 'pending', NOW())";
            $db->execute($sqlTxn, [$bookingId, $paymentMethod, $txnRef, $finalAmount]);

            // 🚀 HOÀN TẤT GIAO DỊCH
            $db->commit();

            // ==========================================
            // F. CHUYỂN HƯỚNG THANH TOÁN - ĐÃ SỬA DÙNG SESSION
            // ==========================================
            if ($paymentMethod === 'vnpay') {
                $_SESSION['vnpay_booking_id'] = $bookingId;
                redirect(SITE_URL . "/payment/vnpay_create");
            } else {
                showMessage('Đặt tour thành công! Cảm ơn bạn đã tin tưởng TravelVN.', 'success');
                redirect(SITE_URL . "/user/dashboard"); 
            }

        } catch (Exception $e) {
            $db->rollBack(); // HỦY TOÀN BỘ NẾU CÓ LỖI
            $backWithError($e->getMessage());
        }
    }

    // ==========================================
    // 3. HIỂN THỊ TRANG ĐẶT TOUR THÀNH CÔNG (GET)
    // ==========================================
    public function success()
    {
        $code = isset($_GET['code']) ? sanitize($_GET['code']) : '';

        if (empty($code)) {
            redirect(SITE_URL . '/');
        }

        $db = Database::getInstance();
        
        $sql = "SELECT b.*, t.title as tour_title, t.slug as tour_slug, t.images, t.duration
                FROM bookings b
                JOIN tours t ON b.tour_id = t.id
                WHERE b.booking_code = ?";

        $stmt = $db->query($sql, [$code]);
        $booking = $stmt ? $stmt->fetch() : null;

        // Xử lý ảnh nếu tìm thấy đơn hàng
        $tourImage = SITE_URL . '/assets/images/no-image.png';
        if ($booking) {
            $images = json_decode($booking['images'], true);
            if (!empty($images) && is_array($images)) {
                $tourImage = UPLOAD_URL . 'tours/' . $images[0];
            }
        }

        return $this->view('pages/booking-success', [
            'pageTitle' => $booking ? 'Đặt tour thành công' : 'Không tìm thấy đơn hàng',
            'booking' => $booking,
            'tourImage' => $tourImage
        ]);
    }
}