<?php
// app/Helpers/functions.php

/**
 * -------------------------------------------------------------------
 * CÁC HÀM XỬ LÝ BẢO MẬT & ĐỊNH DẠNG DỮ LIỆU
 * -------------------------------------------------------------------
 */

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function formatCurrency($amount) {
    return number_format((float)$amount, 0, ',', '.') . ' ₫'; 
}

function formatDate($date, $format = 'd/m/Y') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

// ĐÃ FIX: Bổ sung bộ lọc tiếng Việt chuẩn 100%
function generateSlug($str) {
    $str = trim(mb_strtolower($str));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str); // Xóa ký tự đặc biệt
    $str = preg_replace('/([\s]+)/', '-', $str); // Thay khoảng trắng bằng gạch ngang
    $str = preg_replace('/(-+)/', '-', $str); // Xóa các dấu gạch ngang liên tiếp
    return trim($str, '-'); // Xóa gạch ngang ở đầu và cuối
}

/**
 * -------------------------------------------------------------------
 * CÁC HÀM XỬ LÝ AUTH & PHÂN QUYỀN
 * -------------------------------------------------------------------
 */

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Lưu ý tương lai: Nên chuyển sang track bằng IP lưu vào Database/Cache thay vì Session
function checkLoginRateLimit($max_attempts = 5, $lockout_time = 300) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = time();
    }
    if (isset($_SESSION['is_locked']) && $_SESSION['is_locked'] === true) {
        if (time() - $_SESSION['last_attempt_time'] > $lockout_time) {
            resetLoginRateLimit();
            return true;
        }
        return false;
    }
    return true;
}

function recordFailedLoginAttempt() {
    if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();
    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['is_locked'] = true;
    }
}

function resetLoginRateLimit() {
    unset($_SESSION['login_attempts']);
    unset($_SESSION['last_attempt_time']);
    unset($_SESSION['is_locked']);
}

/**
 * -------------------------------------------------------------------
 * CÁC HÀM ĐIỀU HƯỚNG & THÔNG BÁO (FLASH MESSAGE)
 * -------------------------------------------------------------------
 */

function redirect($url) {
    if (!headers_sent()) {
        header("Location: $url");
    } else {
        echo "<script>window.location.href='$url';</script>";
    }
    exit();
}

function showMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

function getMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

/**
 * -------------------------------------------------------------------
 * HÀM UPLOAD FILE CHUẨN BẢO MẬT
 * -------------------------------------------------------------------
 */

function uploadImage($file, $folder = 'tours') {
    $target_dir = rtrim(UPLOAD_PATH, '/') . '/' . $folder . '/';
    if (!file_exists($target_dir)) { 
        mkdir($target_dir, 0755, true); 
    }
    
    // 1. Kiểm tra lỗi cơ bản & Kích thước (Max 5MB)
    if ($file['error'] !== UPLOAD_ERR_OK || $file["size"] > 5242880) return false;

    // 2. Bảo mật nâng cao: Kiểm tra Mime Type thực tế thay vì chỉ đuôi file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
    if (!in_array($mimeType, $allowedMimeTypes)) return false;

    // 3. Đảm bảo tên file an toàn tuyệt đối
    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '_' . time() . '.' . $ext;
    
    if (move_uploaded_file($file["tmp_name"], $target_dir . $newFileName)) {
        return $newFileName;
    }
    return false;
}