<?php
require_once "../../assets/db.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
    exit();
}

$user_id = $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$desc = trim($_POST['description'] ?? '');
$cover_name = 'default-cover.jpg'; // Ảnh mặc định nếu không upload

// 1. Xử lý Upload ảnh vào thư mục root/src
if (isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] === 0) {
    $upload_dir = "../../src/"; // Từ page/creator/ nhảy ra root/src
    
    // Kiểm tra định dạng (Chỉ cho phép ảnh)
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $filename = $_FILES['cover_file']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Định dạng ảnh không hỗ trợ!']);
        exit();
    }

    // Kiểm tra dung lượng (Max 2MB)
    if ($_FILES['cover_file']['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Ảnh quá lớn (Tối đa 2MB)!']);
        exit();
    }

    // Tạo tên file bảo mật theo yêu cầu: u{id}_{time}_{random}.ext
    $cover_name = "u" . $user_id . "_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
    
    if (!move_uploaded_file($_FILES['cover_file']['tmp_name'], $upload_dir . $cover_name)) {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu ảnh vào src/!']);
        exit();
    }
}

// 2. Tạo Slug URL sạch (Ví dụ: "Kiếm Lai" -> "kiem-lai-123")
function generateSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    return strtolower(trim($text, '-')) . '-' . rand(100, 999);
}

$slug = generateSlug($title);

try {
    // 3. Lưu vào Database (Đảm bảo cột cover_image đã tồn tại)
    $sql = "INSERT INTO stories (user_id, title, slug, description, cover_image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$user_id, $title, $slug, $desc, $cover_name])) {
        echo json_encode(['success' => true, 'message' => 'Truyện đã được tạo thành công!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể lưu truyện vào database!']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi Database: ' . $e->getMessage()]);
}