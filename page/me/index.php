<?php
require_once "../../assets/db.php"; // Đã bao gồm session_start

// 1. Lấy handle từ URL (do .htaccess truyền vào qua tham số ?handle=)
$handle = $_GET['handle'] ?? '';

// 2. Truy vấn thông tin người dùng
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_handle = ?");
$stmt->execute([$handle]);
$profileUser = $stmt->fetch();

// Nếu không tìm thấy user, quay về trang chủ
if (!$profileUser) {
    header("Location: /fstory/");
    exit();
}

// 3. Kiểm tra xem người đang xem có phải chủ sở hữu không
$isOwner = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $profileUser['id']) {
    $isOwner = true;
}
?>

<!DOCTYPE html>
<html lang="vi" data-theme="light">

<head>
    <!--Meta-->
    <?php
    // Đặt tiêu đề riêng cho trang này
    $page_title = "FMember | @" . $profileUser['user_handle'];
    $page_desc = "Trang cá nhân của người dùng @" . $profileUser['user_handle'] . " tại FMember thuộc nền tảng đọc số FStory.";
    // Sau đó mới include
    include "../../view/meta_tag.php";
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/fstory/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="page/me/func/index.css">
</head>

<body>
    <header>
        <div class="container header-content">
            <div style="display: flex; align-items: center; gap: 40px;">
                <a href="javascript:void(0)" class="logo">FMember</a>
            </div>
            <div class="nav-actions">
                <button class="icon-btn" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
                <a href="./" class="btn-write" style="background: var(--primary); color: white;">Trang chủ</a>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="profile-container">
            <div class="profile-cover shadow">
            </div>
            <div class="profile-header-main shadow">
                <div style="position: relative;">
                    <div class="avatar-holder shadow">
                        <img src="src/avt/<?php echo $profileUser['avatar'] ?? 'default_avt.png'; ?>" alt="Avatar">
                    </div>

                    <div class="header-actions">
                        <?php if ($isOwner): ?>
                            <button class="btn-write" style="background: var(--primary); color: white;">
                                <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa
                            </button>
                        <?php else: ?>
                            <button class="btn-write" style="background: var(--primary); color: white;">
                                <i class="fa-solid fa-user-plus"></i> Theo dõi
                            </button>
                            <button class="icon-btn"><i class="fa-solid fa-paper-plane"></i></button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-name"><?php echo $profileUser['nickname']; ?></div>
                <div class="profile-handle">@<?php echo $profileUser['user_handle']; ?></div>

                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value">0</span>
                        <span class="stat-label">Truyện đăng</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">124</span>
                        <span class="stat-label">Đang theo dõi</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">5.2K</span>
                        <span class="stat-label">Người theo dõi</span>
                    </div>
                </div>
            </div>

            <div class="content-tabs">
                <div class="tab-link" data-target="bookshelf">Giới thiệu</div>
                <div class="tab-link active" data-target="activities">Bài đăng</div>
                <div class="tab-link" data-target="favorites">Yêu thích</div>
            </div>

            <div class="grid-layout">
                <section id="profile-content">


                    <?php include "func/post/ui.php"; ?>

                    <div id="bookshelf" class="tab-content" style="display: none;">
                        <?php if ($isOwner): ?>
                            <div class="sidebar-card" style="text-align: center; padding: 40px;">
                                <i class="fa-solid fa-book-open" style="font-size: 3rem; color: var(--border); margin-bottom: 20px;"></i>
                                <p style="color: var(--text-muted);">Bạn chưa đăng truyện nào. Bắt đầu sáng tác ngay thôi!</p>
                                <a href="/fstory/creator" class="btn-write" style="margin-top: 15px; display: inline-block;">
                                    <i class="fa-solid fa-plus"></i> Tạo truyện mới
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="sidebar-card">
                                <p style="text-align: center; color: var(--text-muted);">Người dùng này chưa có truyện công khai.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="favorites" class="tab-content" style="display: none;">
                        <div class="sidebar-card">
                            <p style="text-align: center; color: var(--text-muted);">Danh sách yêu thích đang trống.</p>
                        </div>
                    </div>
                </section>


                <aside>
                    <div class="sidebar-card">
                        <h4 style="margin-bottom: 15px;">Giới thiệu</h4>
                        <p style="font-size: 0.9rem; color: var(--text-muted);">
                            Hội viên của FStory từ <?php echo date('m/Y', strtotime($profileUser['created_at'])); ?>.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </main>
    <script src="page/me/func/index.js"></script>
    <script src="/fstory/assets/js/system_display.js"></script>
</body>

</html>