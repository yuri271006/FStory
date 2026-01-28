<?php
/**
 * FStory Database Connection
 * Sử dụng PDO để tối ưu bảo mật và hiệu suất
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 1. Cấu hình thông số kết nối
$host = 'localhost';
$dbname = 'fstory_db'; // Tên database của bạn
$username = 'root';    // Tài khoản database (mặc định XAMPP là root)
$password = '';        // Mật khẩu database (mặc định XAMPP là để trống)
$charset = 'utf8mb4';  // Hỗ trợ đầy đủ tiếng Việt và emoji

// 2. Thiết lập DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// 3. Các tùy chọn cấu hình PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Đẩy lỗi vào Try-Catch
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Trả về dữ liệu dạng mảng kết hợp
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Tắt giả lập prepared statements để tăng bảo mật
];

try {
    // Khởi tạo kết nối
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Đảm bảo mọi truy vấn sau này đều dùng đúng encoding tiếng Việt
    $pdo->exec("SET NAMES 'utf8mb4'");
    $pdo->exec("SET CHARACTER SET utf8mb4");

} catch (\PDOException $e) {
    // 4. Xử lý lỗi kết nối
    // Trong môi trường phát triển: Hiện lỗi chi tiết
    // Trong môi trường thực tế: Nên ghi vào file log và ẩn lỗi với người dùng
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}