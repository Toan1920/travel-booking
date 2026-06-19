<?php
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Database;

class CouponController extends Controller
{
    // ==========================================
    // API XỬ LÝ KIỂM TRA MÃ GIẢM GIÁ (AJAX)
    // ==========================================
    public function check()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ!']);
            exit;
        }

        // 1. Lọc và làm sạch dữ liệu đầu vào
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT) ?: 0;

        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá.']);
            exit;
        }

        try {
            $db = Database::getInstance();
            
            // 2. Truy vấn Database lấy thông tin mã
            $sql = "SELECT * FROM coupons 
                    WHERE code = ? 
                    AND status = 'active'
                    AND (valid_from IS NULL OR valid_from <= CURDATE())
                    AND (valid_to IS NULL OR valid_to >= CURDATE())
                    AND (usage_limit IS NULL OR usage_limit = 0 OR used_count < usage_limit)";

            $stmt = $db->query($sql, [$code]);
            $coupon = $stmt ? $stmt->fetch() : null;

            if (!$coupon) {
                echo json_encode(['success' => false, 'message' => 'Mã không hợp lệ, đã hết hạn hoặc hết lượt dùng!']);
                exit;
            }

            // 3. Kiểm tra điều kiện đơn tối thiểu
            if ($amount < $coupon['min_order']) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Đơn hàng cần tối thiểu ' . formatCurrency($coupon['min_order']) . ' để áp dụng mã này.'
                ]);
                exit;
            }

            // 4. Thuật toán tính toán số tiền giảm
            $discount = 0;
            if ($coupon['type'] === 'percent') {
                $discount = $amount * ($coupon['value'] / 100);
                
                // Kiểm tra giới hạn giảm tối đa (nếu có)
                if (!empty($coupon['max_discount']) && $discount > $coupon['max_discount']) {
                    $discount = $coupon['max_discount'];
                }
            } else {
                // Giảm số tiền cố định (fixed)
                $discount = $coupon['value'];
            }

            // Ràng buộc: Tiền giảm không được vượt quá tổng tiền đơn hàng
            if ($discount > $amount) {
                $discount = $amount;
            }

            $finalAmount = $amount - $discount;

            // 5. Trả về kết quả JSON cho Frontend
            echo json_encode([
                'success' => true,
                'discount' => $discount,
                'discount_formatted' => formatCurrency($discount),
                'final_amount' => $finalAmount,
                'final_amount_formatted' => formatCurrency($finalAmount),
                'code' => $coupon['code']
            ]);

        } catch (\Exception $e) {
            error_log("Lỗi Kiểm tra Coupon API: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi xử lý mã giảm giá.']);
        }
        
        exit; // Dừng kịch bản ngay sau khi xuất JSON
    }
}
