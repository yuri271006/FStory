<?php
require_once "../../assets/db.php";

// Tắt hiển thị lỗi ra màn hình để tránh làm hỏng JSON
error_reporting(0); 
ini_set('display_errors', 0);

header('Content-Type: application/json');

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
    exit();
}

$user_id = $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Tên truyện không được để trống!']);
    exit();
}

// 2. Hàm tạo Slug chuẩn tiếng Việt (Thay thế iconv bị lỗi)
function createSlug($str) {
    if (!$str) return '';
    $str = mb_strtolower($str, 'UTF-8');
    // Thay thế thủ công các ký tự tiếng Việt
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    // Xóa ký tự đặc biệt
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    return trim($str, '-');
}

// Tạo slug và thêm random số để tránh trùng
$slug = createSlug($title) . '-' . time(); 

// 3. Xử lý Upload Ảnh bìa
$cover_image = 'fstory_logo.png'; // Ảnh mặc định

if (isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] === 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $filename = $_FILES['cover_file']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        // Tên ảnh: cover_userid_timestamp.ext
        $new_name = "cover_" . $user_id . "_" . time() . "." . $ext;
        $upload_path = "../../src/" . $new_name;

        if (move_uploaded_file($_FILES['cover_file']['tmp_name'], $upload_path)) {
            $cover_image = $new_name;
        }
    }
}

// 4. Lưu vào Database
try {
    $sql = "INSERT INTO stories (user_id, title, slug, description, cover_image, status, created_at) 
            VALUES (?, ?, ?, ?, ?, 'ongoing', NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $title, $slug, $description, $cover_image]);

    echo json_encode([
        'success' => true,
        'message' => 'Tạo thành công, đang chuyển hướng...',
        'redirect' => '/fstory/creator' // Đường dẫn để JS chuyển hướng
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi Database: ' . $e->getMessage()]);
}
?>