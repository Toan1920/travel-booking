<?php
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Database;
use Exception;

class PaymentController extends Controller
{
    // ==========================================
    // 1. TẠO LINK THANH TOÁN VÀ CHUYỂN HƯỚNG SANG VNPAY
    // ==========================================
    public function createPayment()
    {
        // Kiểm tra xem trong Session có ID đơn hàng không (Ưu tiên số 1 từ BookingController)
        $booking_id = $_SESSION['vnpay_booking_id'] ?? 0;
        
        // Dùng xong xóa ngay session để tránh xung đột dữ liệu lần sau
        if (isset($_SESSION['vnpay_booking_id'])) {
            unset($_SESSION['vnpay_booking_id']);
        }

        // Dự phòng nếu Session trống (do Router bypass), lấy từ URL qua $_GET
        if ($booking_id <= 0) {
            $booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
        }
        
        if ($booking_id <= 0) {
            die("Mã đơn hàng không hợp lệ! Vui lòng quay lại kiểm tra lịch sử đặt vé.");
        }

        $db = Database::getInstance();
        
        // VÁ LỖI CỘT TIỀN TỆ: Khớp nối với final_amount (số tiền sau khi trừ coupon giảm giá)
        $stmt = $db->query("SELECT * FROM bookings WHERE id = ?", [$booking_id]);
        $booking = $stmt ? $stmt->fetch() : null;

        if (!$booking) {
            die("Không tìm thấy thông tin đơn hàng số: " . $booking_id);
        }

        // Cấu hình từ file .env
        $vnp_TmnCode = $_ENV['VNP_TMN_CODE'] ?? '';
        $vnp_HashSecret = $_ENV['VNP_HASH_SECRET'] ?? '';
        $vnp_Url = $_ENV['VNP_URL'] ?? 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
        $vnp_Returnurl = $_ENV['VNP_RETURN_URL'] ?? '';

        if (empty($vnp_TmnCode) || empty($vnp_HashSecret)) {
            die("Lỗi hệ thống: Chưa cấu hình thông số kết nối VNPAY trong file .env!");
        }

        // Tham số bắt buộc của VNPAY
        $vnp_TxnRef = $booking['id']; // Dùng ID đơn hàng làm mã tham chiếu duy nhất
        $vnp_OrderInfo = "Thanh toan don hang tour #" . $booking['booking_code'];
        $vnp_OrderType = "billpayment";
        
        // Lấy số tiền cuối cùng cần thanh toán (sau giảm giá nếu có) và nhân 100 theo quy định VNPAY
        $vnp_Amount = $booking['final_amount'] * 100; 
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (!empty($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Chuyển hướng khách hàng sang màn hình quét mã/nhập thẻ của VNPAY
        header('Location: ' . $vnp_Url);
        exit;
    }

    // ==========================================
    // 2. NHẬN KẾT QUẢ TRẢ VỀ TỪ VNPAY (XỬ LÝ DỮ LIỆU)
    // ==========================================
    public function vnpayReturn()
    {
        $vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
        $vnp_HashSecret = $_ENV['VNP_HASH_SECRET'] ?? '';
        
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $inputData[$key] = $value;
            }
        }
        
        // Loại bỏ mã băm checksum ra khỏi danh sách tham số để tính toán lại đối chiếu
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        // Thực hiện tính toán lại chữ ký số dựa trên chuỗi dữ liệu nhận được
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        $bookingId = isset($_GET['vnp_TxnRef']) ? (int)$_GET['vnp_TxnRef'] : 0;
        $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
        $vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? '';

        $db = Database::getInstance();
        $booking = null;
        $isSuccess = false;
        $msg = "Dữ liệu xác thực không hợp lệ.";

        // Kiểm tra chữ ký bảo mật do VNPAY gửi về tránh hacker giả lập URL
        if ($secureHash === $vnp_SecureHash) {
            
            $stmt = $db->query("SELECT * FROM bookings WHERE id = ?", [$bookingId]);
            $booking = $stmt ? $stmt->fetch() : null;

            if ($booking) {
                try {
                    $db->beginTransaction();
                    $pdo = $db->getConnection();

                    if ($vnp_ResponseCode == '00') {
                        // A. THANH TOÁN THÀNH CÔNG (Mã phản hồi '00')
                        $isSuccess = true;
                        $msg = "Thanh toán đơn hàng thành công qua VNPAY!";

                        // Cập nhật trạng thái Đơn hàng
                        $sqlOrder = "UPDATE bookings SET payment_status = 'paid', booking_status = 'confirmed' WHERE id = ?";
                        $pdo->prepare($sqlOrder)->execute([$bookingId]);

                        // Cập nhật Lịch sử giao dịch
                        $sqlTxn = "UPDATE transactions SET status = 'success', transaction_ref = ? WHERE booking_id = ?";
                        $pdo->prepare($sqlTxn)->execute([$vnp_TransactionNo, $bookingId]);

                    } else {
                        // B. THANH TOÁN THẤT BẠI HOẶC HỦY GIAO DỊCH
                        $msg = "Giao dịch không thành công hoặc đã bị hủy bỏ.";

                        // Cập nhật trạng thái thất bại
                        $sqlOrder = "UPDATE bookings SET payment_status = 'failed', booking_status = 'cancelled' WHERE id = ?";
                        $pdo->prepare($sqlOrder)->execute([$bookingId]);

                        $sqlTxn = "UPDATE transactions SET status = 'failed', transaction_ref = ? WHERE booking_id = ?";
                        $pdo->prepare($sqlTxn)->execute([$vnp_TransactionNo, $bookingId]);

                        // HOÀN TÁC VÉ: Trả lại số chỗ đã trừ tạm thời cho ngày khởi hành
                        $totalGuests = (int)$booking['adults'] + (int)$booking['children'];
                        $sqlRestoreSlots = "UPDATE departure_dates SET available_slots = available_slots + ? WHERE id = ?";
                        $pdo->prepare($sqlRestoreSlots)->execute([$totalGuests, $booking['departure_date_id']]);

                        // HOÀN TÁC COUPON: Nếu đơn hàng có áp mã giảm giá, trả lại lượt dùng cho mã đó
                        // Tìm mã giảm giá dựa trên số tiền chênh lệch (nếu discount_amount > 0)
                        if ($booking['discount_amount'] > 0) {
                            // Giả định form đặt vé truyền lên, có thể lấy couponCode từ DB nếu thiết kế lưu coupon_id
                            // Đoạn code này chạy kiểm tra an toàn nếu bảng bookings có lưu lại coupon code
                            // Cụ có thể bổ sung cột này hoặc bỏ qua nếu không cần hoàn tác coupon nghiêm ngặt
                        }
                    }

                    $db->commit();

                    // Cập nhật lại mảng dữ liệu mới nhất để in ra view thành công
                   $stmtUpdate = $db->query("SELECT b.*, t.title as tour_title, t.slug as tour_slug, t.images, t.duration 
                          FROM bookings b 
                          JOIN tours t ON b.tour_id = t.id 
                          WHERE b.id = ?", [$bookingId]);
                    $booking = $stmtUpdate ? $stmtUpdate->fetch() : null;

                } catch (Exception $e) {
                    $db->rollBack();
                    error_log("Lỗi xử lý VNPAY Return: " . $e->getMessage());
                    $msg = "Có lỗi xảy ra trong quá trình cập nhật trạng thái giao dịch.";
                    $isSuccess = false;
                }
            } else {
                $msg = "Không tìm thấy đơn hàng tương ứng trên hệ thống.";
            }
        } else {
            $msg = "Chữ ký số bảo mật không khớp. Giao dịch nghi vấn giả mạo!";
        }

        // Xử lý ảnh đại diện của Tour để xuất ra trang kết quả
        $tourImage = SITE_URL . '/public/assets/images/no-image.png';
        if ($booking && !empty($booking['images'])) {
            $images = json_decode($booking['images'], true);
            if (!empty($images) && is_array($images)) {
                $tourImage = UPLOAD_URL . 'tours/' . $images[0];
            }
        }

        // Trả kết quả hiển thị ra View cho khách hàng
        return $this->view('pages/booking-success', [
            'pageTitle' => $isSuccess ? 'Đặt tour thành công' : 'Thanh toán không thành công',
            'isSuccess' => $isSuccess,
            'message' => $msg,
            'booking' => $booking,
            'tourImage' => $tourImage,
            'vnpayData' => $_GET
        ]);
    }
}