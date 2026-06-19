<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;

    // Private constructor ngăn chặn khởi tạo bằng từ khóa 'new'
    private function __construct()
    {
        $db_host = $_ENV['DB_HOST'] ?? 'localhost';
        $db_name = $_ENV['DB_NAME'] ?? 'travel_website';
        $db_user = $_ENV['DB_USER'] ?? 'root';
        $db_pass = $_ENV['DB_PASS'] ?? '';
        $is_debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $db_user, $db_pass, $options);
        } catch (PDOException $e) {
            $this->logError("DB Connection Error: " . $e->getMessage());
            
            if ($is_debug) {
                die("Lỗi kết nối DB: " . $e->getMessage());
            } else {
                die("Hệ thống đang bảo trì. Vui lòng quay lại sau.");
            }
        }
    }

    // Lấy instance duy nhất của Database
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // NGĂN CHẶN CLONE VÀ UNSERIALIZE (Bảo vệ tuyệt đối Singleton)
    private function __clone() {}
    public function __wakeup() {}

    // Trả về đối tượng PDO
    public function getConnection()
    {
        return $this->pdo;
    }

    // ==========================================
    // CÁC HÀM XỬ LÝ GIAO DỊCH (TRANSACTIONS)
    // ==========================================
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    // ==========================================
    // CÁC HÀM THỰC THI SQL
    // ==========================================

    // Tương đương db_query() cũ - Dùng cho SELECT
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->logError("SQL Query Error: " . $e->getMessage() . " | SQL: $sql");
            if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') die("SQL Error: " . $e->getMessage());
            return false;
        }
    }

    // Tương đương db_execute() cũ - Dùng cho INSERT/UPDATE/DELETE
    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute($params);
            
            if (stripos(trim($sql), 'INSERT') === 0) {
                return $this->pdo->lastInsertId();
            }
            return $success;
        } catch (PDOException $e) {
            $this->logError("SQL Execute Error: " . $e->getMessage() . " | SQL: $sql");
            return false;
        }
    }

    // Ghi log lỗi (Đã sửa lỗi tự động tạo thư mục)
    private function logError($message)
    {
        $logDir = __DIR__ . '/../../storage/logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logPath = $logDir . '/error_log.txt';
        $error_msg = "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;
        file_put_contents($logPath, $error_msg, FILE_APPEND);
    }
}