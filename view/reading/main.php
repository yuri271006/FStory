<?php
// 1. LẤY ID CHƯƠNG TỪ URL
$chapter_id = $_GET['id'] ?? 0;

if (!$chapter_id) {
    echo "<script>window.location.href = './';</script>";
    exit();
}

// 2. LẤY DỮ LIỆU CHƯƠNG HIỆN TẠI
$stmt = $pdo->prepare("SELECT * FROM chapters WHERE id = ?");
$stmt->execute([$chapter_id]);
$chapter = $stmt->fetch();

if (!$chapter) {
    die("<div class='container' style='padding:50px; text-align:center;'><h2>:((( <br> Bạn ơi, dữ liệu về chương này đã bị lạc mất!</h2>");
}

// 3. LẤY THÔNG TIN TRUYỆN (Để làm Breadcrumb)
$storyStmt = $pdo->prepare("SELECT id, title, slug FROM stories WHERE id = ?");
$storyStmt->execute([$chapter['story_id']]);
$story = $storyStmt->fetch();

// 4. TÌM CHƯƠNG TRƯỚC & CHƯƠNG SAU
// Chương trước: Cùng truyện, số chương nhỏ hơn hiện tại, lấy cái lớn nhất trong đám nhỏ hơn
$prevStmt = $pdo->prepare("SELECT id FROM chapters WHERE story_id = ? AND chapter_number < ? ORDER BY chapter_number DESC LIMIT 1");
$prevStmt->execute([$story['id'], $chapter['chapter_number']]);
$prevChap = $prevStmt->fetch();

// Chương sau: Cùng truyện, số chương lớn hơn hiện tại, lấy cái nhỏ nhất trong đám lớn hơn
$nextStmt = $pdo->prepare("SELECT id FROM chapters WHERE story_id = ? AND chapter_number > ? ORDER BY chapter_number ASC LIMIT 1");
$nextStmt->execute([$story['id'], $chapter['chapter_number']]);
$nextChap = $nextStmt->fetch();

// 5. TÍNH SỐ TỪ (Ước lượng)
$wordCount = str_word_count(strip_tags($chapter['content']));
?>

<style>
    /* CSS RIÊNG CHO TRANG ĐỌC */
    .reading-container {
        max-width: 800px; margin: 0 auto; padding: 40px 20px;
        background: var(--bg-body); /* Nền theo theme */
    }
    
    .chapter-header {
        text-align: center; margin-bottom: 50px;
        padding-bottom: 30px; border-bottom: 1px solid var(--border);
    }
    .chapter-header h1 {
        font-family: 'Merriweather', serif; font-size: 2rem; line-height: 1.4; margin-bottom: 15px; color: var(--text-main);
    }

    /* Nội dung truyện - QUAN TRỌNG NHẤT */
    .chapter-content {
        font-family: 'Lora', serif; font-size: 1.25rem; line-height: 1.8;
        color: var(--text-main); text-align: justify;
    }
    .chapter-content p { margin-bottom: 25px; }
    
    /* Navigation Footer */
    .reading-footer-nav {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 60px; padding-top: 30px; border-top: 1px solid var(--border);
    }
    .nav-btn {
        padding: 12px 25px; border-radius: 30px; text-decoration: none;
        background: var(--bg-card); color: var(--text-main); border: 1px solid var(--border);
        font-weight: 600; font-size: 0.9rem; transition: 0.2s; display: flex; align-items: center; gap: 8px;
    }
    .nav-btn:hover:not(.disabled) {
        background: var(--primary); color: white; border-color: var(--primary);
    }
    .nav-btn.disabled {
        opacity: 0.5; cursor: not-allowed; pointer-events: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .chapter-header h1 { font-size: 1.6rem; }
        .chapter-content { font-size: 1.1rem; }
        .reading-footer-nav { flex-direction: column; gap: 15px; }
        .nav-btn { width: 100%; justify-content: center; }
    }
</style>

<main class="reading-container">
    
    <nav style="margin-bottom: 30px; font-size: 0.9rem; color: var(--text-muted);">
        <a href="javascript:void(0)" style="color: var(--text-muted); text-decoration: none;">Tác phẩm</a> 
        <span style="margin: 0 8px;">/</span> 
        <a href="detail.php?slug=<?php echo $story['slug']; ?>" style="color: var(--text-muted); text-decoration: none; font-weight: 600;">
            <?php echo htmlspecialchars($story['title']); ?>
        </a> 
        <span style="margin: 0 8px;">/</span> 
        <span style="color: var(--primary); font-weight: 700;">Chương <?php echo $chapter['chapter_number']; ?></span>
    </nav>

    <div class="chapter-header">
        <h1>Chương <?php echo $chapter['chapter_number']; ?>: <?php echo htmlspecialchars($chapter['title']); ?></h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            <i class="fa-regular fa-clock" style="margin-right: 5px;"></i> 
            Cập nhật: <?php echo date('H:i d/m/Y', strtotime($chapter['created_at'])); ?> 
            <span style="margin: 0 10px;">•</span> 
            <i class="fa-solid fa-align-left"></i> <?php echo number_format($wordCount); ?> chữ
        </p>
    </div>

    <article class="chapter-content" id="chapterBody">
        <?php 
            // Nếu nội dung lưu từ TinyMCE đã có thẻ P rồi thì echo thẳng
            // Nếu lưu dạng text thuần thì dùng nl2br
            echo $chapter['content']; 
        ?>
    </article>

    <nav class="reading-footer-nav">
        <?php if($prevChap): ?>
            <a href="reading.php?id=<?php echo $prevChap['id']; ?>" class="nav-btn">
                <i class="fa-solid fa-chevron-left"></i> Chương trước
            </a>
        <?php else: ?>
            <a href="#" class="nav-btn disabled"><i class="fa-solid fa-chevron-left"></i>HẾT CHƯƠNG</a>
        <?php endif; ?>

        <a href="detail.php?slug=<?php echo $story['slug']; ?>" class="nav-btn" style="border-color: var(--text-muted);">
            <i class="fa-solid fa-list-ul"></i> <span class="hide-mobile">Mục lục</span>
        </a>

        <?php if($nextChap): ?>
            <a href="reading.php?id=<?php echo $nextChap['id']; ?>" class="nav-btn">
                Chương sau <i class="fa-solid fa-chevron-right"></i>
            </a>
        <?php else: ?>
            <a href="#" class="nav-btn disabled">HẾT CHƯƠNG <i class="fa-solid fa-ban"></i></a>
        <?php endif; ?>
    </nav>

</main>