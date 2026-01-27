<?php
session_start();
header('Content-Type: application/json');

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ!']);
    exit();
}

// Nhận dữ liệu từ form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Kiểm tra dữ liệu đầu vào
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin!']);
    exit();
}

/** * LOGIC KIỂM TRA DATABASE (Giả lập)
 * Sau này bạn sẽ thay phần này bằng truy vấn SQL thực tế
 */
$mock_user = [
    'id' => 1,
    'username' => 'admin',
    'password' => '123456', // Trong thực tế phải dùng password_verify()
    'nickname' => 'Lục Châu'
];

if ($username === $mock_user['username'] && $password === $mock_user['password']) {
    // Lưu thông tin vào Session
    $_SESSION['user_id'] = $mock_user['id'];
    $_SESSION['user_name'] = $mock_user['nickname'];
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đăng nhập thành công! Đang chuyển hướng...'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Tài khoản hoặc mật khẩu không chính xác!'
    ]);
}