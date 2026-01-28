<?php
// Kết nối Database và khởi tạo Session
require_once "../../assets/db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// 1. Xác minh dữ liệu đầu vào
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ tài khoản và mật khẩu!']);
    exit();
}

try {
    // 2. Kiểm tra người dùng trong Database (Hỗ trợ cả email và nickname)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$username]);

    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        // 3. Đăng nhập thành công, thiết lập Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_handle'] = $user['user_handle']; // Lưu handle vào session

        echo json_encode([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'handle' => $user['user_handle'] // Trả về handle cho AJAX
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tài khoản hoặc mật khẩu không chính xác!']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại sau!']);
}
