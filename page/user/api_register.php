<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ!']);
    exit();
}

// Nhận dữ liệu từ form
$nickname = $_POST['nickname'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Kiểm tra dữ liệu trống
if (empty($nickname) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng không để trống thông tin!']);
    exit();
}

// Kiểm tra độ dài mật khẩu
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu phải từ 6 ký tự trở lên!']);
    exit();
}

/** * LOGIC LƯU VÀO DATABASE (Giả lập)
 * Bạn sẽ thực hiện câu lệnh INSERT INTO users... tại đây
 */

// Giả định email chưa tồn tại và đăng ký thành công
echo json_encode([
    'success' => true, 
    'message' => 'Đăng ký thành công! Hãy đăng nhập để bắt đầu.'
]);