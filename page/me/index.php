<?php
require_once "../../assets/db.php"; // Kết nối DB và session_start

$handle = $_GET['handle'] ?? '';

// 1. Tìm thông tin người dùng theo handle từ URL
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_handle = ?");
$stmt->execute([$handle]);
$profileUser = $stmt->fetch();

if (!$profileUser) {
    header("Location: ../../home"); // Không tìm thấy user thì đá về home
    exit();
}

// 2. Kiểm tra quyền hạn (Phân biệt chủ sở hữu và khách)
$isOwner = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $profileUser['id']) {
    $isOwner = true;
}
?>

<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

    <main class="container">
        <div class="profile-header sidebar-card">
            <div class="profile-avatar">
                <img src="<?php echo $profileUser['avatar']; ?>" alt="Avatar">
                <?php if ($isOwner): ?>
                    <button class="edit-btn"><i class="fa-solid fa-camera"></i> Đổi ảnh</button>
                <?php endif; ?>
            </div>
            
            <div class="profile-info">
                <h1><?php echo $profileUser['nickname']; ?></h1>
                <p>@<?php echo $profileUser['user_handle']; ?></p>
                
                <?php if ($isOwner): ?>
                    <button class="btn-write"><i class="fa-solid fa-pen"></i> Chỉnh sửa trang cá nhân</button>
                <?php else: ?>
                    <button class="btn-write" style="background: var(--secondary);"><i class="fa-solid fa-user-plus"></i> Theo dõi</button>
                <?php endif; ?>
            </div>
        </div>

        <section class="user-content">
            <?php if ($isOwner): ?>
                <div class="create-post sidebar-card">
                    <textarea placeholder="Bạn đang nghĩ gì thế?"></textarea>
                    <button class="btn-write">Đăng post</button>
                </div>
            <?php endif; ?>
            
            </section>
    </main>

</body>
</html>