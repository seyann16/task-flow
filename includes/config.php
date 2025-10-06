<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Database configuration
define('DB_HOST', "localhost");
define('DB_NAME', 'task_manager');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            $this->sendError('Database connection failed');
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }

    private function sendError($message) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
}

// Global database instance
$database = new Database();
$pdo = $database->getConnection();
?>