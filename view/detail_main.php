<?php
// 1. LOGIC PHP: LẤY DỮ LIỆU
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    echo "<script>window.location.href = './';</script>";
    exit();
}

// Lấy thông tin truyện + Tác giả
$stmt = $pdo->prepare("
    SELECT s.*, u.nickname, u.user_handle, u.avatar as author_avatar 
    FROM stories s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.slug = ?
");
$stmt->execute([$slug]);
$story = $stmt->fetch();

if (!$story) {
    echo "<div class='container' style='padding: 50px; text-align:center;'><h2>:(( <br> Tác phẩm không tồn tại!</h2></div>";
    return; // Dừng render phần sau
}

// Lấy danh sách chương
$chapStmt = $pdo->prepare("SELECT * FROM chapters WHERE story_id = ? ORDER BY chapter_number ASC");
$chapStmt->execute([$story['id']]);
$chapters = $chapStmt->fetchAll();

// Lấy truyện cùng tác giả
$authStmt = $pdo->prepare("SELECT * FROM stories WHERE user_id = ? AND id != ? LIMIT 5");
$authStmt->execute([$story['user_id'], $story['id']]);
$otherStories = $authStmt->fetchAll();
?>

<style>
    /* CSS Riêng cho trang Detail để không ảnh hưởng style chung */
    .detail-cover-box {
        width: 240px;
        height: 340px;
        border-radius: var(--radius-md);
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        position: relative;
    }

    .detail-title {
        font-family: 'Merriweather', serif;
        font-size: 2.2rem;
        line-height: 1.3;
        margin-bottom: 15px;
        color: var(--text-main);
    }

    .detail-desc {
        font-family: 'Lora', serif;
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--text-muted);
        margin-bottom: 25px;
    }

    .chapter-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 15px;
        border-bottom: 1px solid var(--border);
        transition: 0.2s;
        text-decoration: none;
        color: var(--text-main);
        font-size: 0.95rem;
    }

    .chapter-item:hover {
        background: var(--bg-body);
        color: var(--primary);
        padding-left: 20px;
    }

    .chapter-list-box {
        max-height: 600px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .detail-header {
            flex-direction: column;
        }

        .detail-cover-box {
            width: 100%;
            height: auto;
            aspect-ratio: 2/3;
            margin-bottom: 20px;
        }

        .detail-title {
            font-size: 1.8rem;
        }
    }
</style>
<header>
    <div class="container header-content">
        <div style="display: flex; align-items: center; gap: 40px;">
            <a href="./" class="logo">FStory</a>
        </div>

        <div class="nav-actions">
            <button class="icon-btn" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
             <a href="./" class="btn-write" style="background: var(--primary); color: white;">Trang chủ</a>
        </div>
    </div>
</header>
<main class="container" style="margin-top: 30px;">
    <div class="grid-layout">

        <section>
            <div class="sidebar-card shadow detail-header" style="display: flex; gap: 30px; align-items: flex-start;">
                <div class="detail-cover-box">
                    <img src="src/<?php echo $story['cover_image']; ?>" alt="Cover" style="width:100%; height:100%; object-fit:cover;">
                </div>

                <div style="flex: 1;">
                    <h1 class="detail-title"><?php echo htmlspecialchars($story['title']); ?></h1>

                    <div style="margin-bottom: 20px; font-size: 0.9rem; color: var(--text-muted);">
                        <p style="margin-bottom: 8px;">
                            <i class="fa-solid fa-user-pen" style="width:20px; color:var(--primary);"></i> Tác giả:
                            <a href="me/@<?php echo $story['user_handle']; ?>" style="font-weight:700; color:var(--text-main);">
                                <?php echo htmlspecialchars($story['nickname']); ?>
                            </a>
                        </p>
                        <p style="margin-bottom: 8px;">
                            <i class="fa-solid fa-layer-group" style="width:20px; color:var(--primary);"></i> Độ dài:
                            <?php echo count($chapters); ?> chương
                        </p>
                        <p>
                            <i class="fa-solid fa-rotate" style="width:20px; color:var(--primary);"></i> Trạng thái:
                            <span style="color: <?php echo $story['status'] == 'ongoing' ? '#16a34a' : '#2563eb'; ?>; font-weight:700;">
                                <?php echo $story['status'] == 'ongoing' ? 'Đang tiến hành' : 'Đã hoàn thành'; ?>
                            </span>
                        </p>
                    </div>

                    <div class="detail-desc">
                        <?php echo nl2br(htmlspecialchars($story['description'])); ?>
                    </div>

                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <?php if (!empty($chapters)): ?>
                            <a href="reading.php?id=<?php echo $chapters[0]['id']; ?>" class="btn-write" style="background: var(--primary); color: white; border-radius: 30px; padding: 12px 30px; font-weight:700;">
                                <i class="fa-solid fa-book-open"></i> ĐỌC TỪ ĐẦU
                            </a>
                        <?php else: ?>
                            <button class="btn-write" disabled style="background: var(--border); color: var(--text-muted);">SẮP RA MẮT</button>
                        <?php endif; ?>

                        <button onclick="toggleFav(this)" class="btn-write" style="background: var(--bg-body); color: var(--text-main); border: 1px solid var(--border); border-radius: 30px;">
                            <i class="fa-regular fa-heart"></i> Yêu thích
                        </button>
                    </div>
                </div>
            </div>

            <div class="section-header" style="margin-top: 40px; border-bottom: 2px solid var(--border); padding-bottom: 10px; margin-bottom: 20px;">
                <h2 class="section-title" style="margin:0;">Danh sách chương</h2>
            </div>

            <div class="sidebar-card shadow" style="padding: 0;">
                <div class="chapter-list-box">
                    <?php if (empty($chapters)): ?>
                        <div style="padding:30px; text-align:center; color:var(--text-muted);">Tác giả chưa đăng tải chương nào cả.</div>
                        <?php else: foreach ($chapters as $chap): ?>
                            <a href="reading.php?id=<?php echo $chap['id']; ?>" class="chapter-item">
                                <span>Chương <?php echo $chap['chapter_number']; ?>: <?php echo htmlspecialchars($chap['title']); ?></span>
                                <span style="font-size:0.8rem; color:var(--text-muted);"><?php echo date('d/m/Y', strtotime($chap['created_at'])); ?></span>
                            </a>
                    <?php endforeach;
                    endif; ?>
                </div>
            </div>
        </section>

        <aside>
            <div class="sidebar-card shadow" style="text-align: center;">
                <img src="src/avt/<?php echo $story['author_avatar'] ?: 'default_avt.png'; ?>"
                    style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 3px solid var(--bg-body);">
                <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($story['nickname']); ?></h4>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 15px;">@<?php echo $story['user_handle']; ?></p>
                <a href="@<?php echo $story['user_handle']; ?>" class="btn-write" style="width: 100%; display: block; font-size: 0.85rem;">Xem trang cá nhân</a>
            </div>

            <div class="sidebar-card shadow" style="margin-top: 20px;">
                <h4 style="margin-bottom: 15px; font-size: 1rem;">Cùng tác giả</h4>
                <?php if (empty($otherStories)): ?>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">Không có tác phẩm nào khác.</p>
                    <?php else: foreach ($otherStories as $os): ?>
                        <div style="display: flex; gap: 10px; margin-bottom: 15px; align-items: center;">
                            <img src="src/<?php echo $os['cover_image']; ?>" style="width: 45px; height: 60px; border-radius: 4px; object-fit: cover;">
                            <div>
                                <a href="detail.php?slug=<?php echo $os['slug']; ?>" style="color: var(--text-main); font-weight: 600; font-size: 0.9rem; display: block; line-height: 1.2; margin-bottom: 2px;">
                                    <?php echo htmlspecialchars($os['title']); ?>
                                </a>
                                <span style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $os['status'] == 'ongoing' ? 'Đang ra' : 'Full'; ?></span>
                            </div>
                        </div>
                <?php endforeach;
                endif; ?>
            </div>
        </aside>

    </div>
</main>