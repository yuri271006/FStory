<?php
// Kết nối Database và Session
require_once "../../assets/db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
    exit();
}

// 1. Nhận và làm sạch dữ liệu
$nickname = trim($_POST['nickname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// 2. Xác minh dữ liệu cơ bản
if (empty($nickname) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ tất cả các trường!']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Định dạng Email không hợp lệ!']);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu phải chứa ít nhất 6 ký tự!']);
    exit();
}

// 3. Hàm tạo Handle tự động (ví dụ: "Chu Minh Thụy" -> "chu.minh.thuy.123")
function createHandle($nickname)
{
    $search = array(
        'à',
        'á',
        'ạ',
        'ả',
        'ã',
        'â',
        'ầ',
        'ấ',
        'ậ',
        'ẩ',
        'ẫ',
        'ă',
        'ằ',
        'ắ',
        'ặ',
        'ẳ',
        'ẵ',
        'è',
        'é',
        'ẹ',
        'ẻ',
        'ẽ',
        'ê',
        'ề',
        'ế',
        'ệ',
        'ể',
        'ễ',
        'ì',
        'í',
        'ị',
        'ỉ',
        'ĩ',
        'ò',
        'ó',
        'ọ',
        'ỏ',
        'õ',
        'ô',
        'ồ',
        'ố',
        'ộ',
        'ổ',
        'ỗ',
        'ơ',
        'ờ',
        'ớ',
        'ợ',
        'ở',
        'ỡ',
        'ù',
        'ú',
        'ụ',
        'ủ',
        'ũ',
        'ư',
        'ừ',
        'ứ',
        'ự',
        'ử',
        'ữ',
        'ỳ',
        'ý',
        'ỵ',
        'ỷ',
        'ỹ',
        'đ',
        'À',
        'Á',
        'Ạ',
        'Ả',
        'Ã',
        'Â',
        'Ầ',
        'Ấ',
        'Ậ',
        'Ẩ',
        'Ẫ',
        'Ă',
        'Ằ',
        'Ắ',
        'Ặ',
        'Ẳ',
        'Ẵ',
        'È',
        'É',
        'Ẹ',
        'Ẻ',
        'Ẽ',
        'Ê',
        'Ề',
        'Ế',
        'Ệ',
        'Ể',
        'Ễ',
        'Ì',
        'Í',
        'Ị',
        'Ỉ',
        'Ĩ',
        'Ò',
        'Ó',
        'Ọ',
        'Ỏ',
        'Õ',
        'Ô',
        'Ồ',
        'Ố',
        'Ộ',
        'Ổ',
        'Ỗ',
        'Ơ',
        'Ờ',
        'Ớ',
        'Ợ',
        'Ở',
        'Ỡ',
        'Ù',
        'Ú',
        'Ụ',
        'Ủ',
        'Ũ',
        'Ư',
        'Ừ',
        'Ứ',
        'Ự',
        'Ử',
        'Ữ',
        'Ỳ',
        'Ý',
        'Ạ',
        'Ỷ',
        'Ỹ',
        'Đ'
    );
    $replace = array(
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'i',
        'i',
        'i',
        'i',
        'i',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'y',
        'y',
        'y',
        'y',
        'y',
        'd',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'E',
        'E',
        'E',
        'E',
        'E',
        'E',
        'E',
        'E',
        'E',
        'E',
        'E',
        'I',
        'I',
        'I',
        'I',
        'I',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'O',
        'U',
        'U',
        'U',
        'U',
        'U',
        'U',
        'U',
        'U',
        'U',
        'U',
        'U',
        'Y',
        'Y',
        'A',
        'Y',
        'Y',
        'D'
    );
    $handle = str_replace($search, $replace, $nickname);
    $handle = strtolower(str_replace(' ', '.', $handle)); // Thay khoảng trắng bằng dấu chấm
    $handle = preg_replace('/[^a-z0-9.]/', '', $handle); // Xóa ký tự đặc biệt khác
    return $handle . rand(100, 999); // Thêm số ngẫu nhiên để tránh trùng lặp ban đầu
}

$user_handle = createHandle($nickname);

try {
    // 4. Kiểm tra Email, Nickname hoặc Handle đã tồn tại chưa
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR user_handle = ? LIMIT 1");
    $check->execute([$email, $user_handle]);

    if ($check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email, tên hiển thị hoặc định danh đã tồn tại!']);
        exit();
    }

    // 5. Mã hóa mật khẩu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 6. Lưu vào Database
    $sql = "INSERT INTO users (nickname, user_handle, email, password, avatar, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    // $defaultAvatar = '' . rand(1, 70);
    $defaultAvatar = 'default_avt.png';

    if ($stmt->execute([$nickname, $user_handle, $email, $hashedPassword, $defaultAvatar])) {

        // --- TỰ ĐỘNG ĐĂNG NHẬP SAU KHI ĐĂNG KÝ THÀNH CÔNG ---
        // Lấy ID vừa mới tạo trong Database
        $new_user_id = $pdo->lastInsertId();

        // Thiết lập Session giống hệt như khi đăng nhập
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['user_handle'] = $user_handle;
        $_SESSION['user_avatar'] = $defaultAvatar; // LƯU AVATAR MẶC ĐỊNH
        echo json_encode([
            'success' => true,
            'message' => 'Đăng kí thành công! Đang xử lí...',
            'handle' => $user_handle // Trả về handle để JavaScript điều hướng
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể tạo tài khoản, vui lòng thử lại!']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối dữ liệu: ' . $e->getMessage()]);
}
