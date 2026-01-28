<?php
require_once "assets/db.php";

// 1. Lấy dữ liệu cho Hero Bento (3 truyện mới nhất hoặc có tag Trending)
$heroStmt = $pdo->query("SELECT * FROM stories ORDER BY created_at DESC LIMIT 3");
$heroStories = $heroStmt->fetchAll();

// 2. Lấy danh sách truyện mới cập nhật (8 truyện)
// Join với chapters để lấy số chương mới nhất
$newStoriesStmt = $pdo->query("
    SELECT s.*, COUNT(c.id) as total_chapters 
    FROM stories s 
    LEFT JOIN chapters c ON s.id = c.story_id 
    GROUP BY s.id 
    ORDER BY s.created_at DESC 
    LIMIT 8
");
$newStories = $newStoriesStmt->fetchAll();

// 3. Lấy Top lượt đọc (Top 5 truyện có nhiều chương nhất - giả định là độ hot)
$topStoriesStmt = $pdo->query("
    SELECT s.title, s.slug, COUNT(c.id) as total_chapters 
    FROM stories s 
    JOIN chapters c ON s.id = c.story_id 
    GROUP BY s.id 
    ORDER BY total_chapters DESC 
    LIMIT 5
");
$topStories = $topStoriesStmt->fetchAll();
?>

<main class="container">
    <div class="hero-bento">
        <?php if (!empty($heroStories)): ?>
            <div class="bento-item bento-1 shadow">
                <img src="/fstory/src/<?php echo $heroStories[0]['cover_image']; ?>" alt="Cover">
                <div class="content">
                    <span class="tag-trending">TRENDING</span>
                    <a href="detail/<?php echo $heroStories[0]['slug']; ?>" style="text-decoration: none; color: white;">
                        <h2 style="font-family: 'Merriweather', serif; font-size: 2rem; margin-top: 10px;">
                            <?php echo htmlspecialchars($heroStories[0]['title']); ?>
                        </h2>
                    </a>
                    <p><?php echo mb_substr(htmlspecialchars($heroStories[0]['description']), 0, 100) . '...'; ?></p>
                </div>
            </div>

            <?php for($i=1; $i<count($heroStories); $i++): ?>
            <div class="bento-item shadow">
                <a href="detail/<?php echo $heroStories[$i]['slug']; ?>">
                    <img src="/fstory/src/<?php echo $heroStories[$i]['cover_image']; ?>" alt="Cover">
                </a>
            </div>
            <?php endfor; ?>
        <?php endif; ?>
    </div>

    <div class="grid-layout">
        <section>
            <div class="section-header">
                <h2 class="section-title">Truyện mới cập nhật</h2>
                <a href="all-stories" class="view-all">Xem tất cả <i class="fa-solid fa-angles-right"></i></a>
            </div>
            
            <div class="story-grid">
                <?php foreach($newStories as $story): ?>
                <div class="story-card shadow">
                    <div class="card-cover">
                        <img src="/fstory/src/<?php echo $story['cover_image']; ?>" alt="Cover">
                        <div class="chapter-badge">C.<?php echo $story['total_chapters']; ?></div>
                    </div>
                    <div class="card-info">
                        <a href="detail/<?php echo $story['slug']; ?>">
                            <h4><?php echo htmlspecialchars($story['title']); ?></h4>
                        </a>
                        <p>Sáng tác • <?php echo date('d/m', strtotime($story['created_at'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <aside>
            <div class="sidebar-card shadow">
                <h4 style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-crown" style="color: #f59e0b;"></i> Top lượt đọc
                </h4>
                <div class="top-list">
                    <?php 
                    $rank = 1;
                    foreach($topStories as $top): 
                    ?>
                    <div class="top-item">
                        <span class="rank-num rank-<?php echo $rank; ?>">
                            <?php echo str_pad($rank, 2, '0', STR_PAD_LEFT); ?>
                        </span>
                        <div class="top-info">
                            <a href="detail/<?php echo $top['slug']; ?>"><h5><?php echo htmlspecialchars($top['title']); ?></h5></a>
                            <small><?php echo $top['total_chapters']; ?> Chương</small>
                        </div>
                    </div>
                    <?php 
                    $rank++;
                    endforeach; 
                    ?>
                </div>
            </div>

            <div class="sidebar-card shadow" style="background: var(--primary); color: white; margin-top: 20px;">
                <h4>FStory Studio</h4>
                <p style="font-size: 0.8rem; opacity: 0.9; margin-top: 10px;">Bạn có một câu chuyện muốn kể? Hãy tham gia cùng đội ngũ sáng tác của chúng tôi.</p>
                <a href="creator" class="btn-write" style="background: white; color: var(--primary); margin-top: 15px; display: block; text-align: center;">Bắt đầu viết</a>
            </div>
        </aside>
    </div>
</main>

<style>
/* CSS BỔ SUNG ĐỂ HOÀN THIỆN GIAO DIỆN */
.tag-trending { background: var(--secondary); padding: 4px 10px; border-radius: 4px; font-size: 0.7rem; font-weight: 800; }
.view-all { color: var(--primary); font-weight: 700; font-size: 0.9rem; text-decoration: none; transition: 0.3s; }
.view-all:hover { gap: 10px; opacity: 0.8; }

/* Chapter Badge trên ảnh bìa */
.card-cover { position: relative; overflow: hidden; border-radius: var(--radius-md); }
.chapter-badge {
    position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7);
    color: white; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; backdrop-filter: blur(4px);
}

/* Rank Styling */
.top-item { display: flex; gap: 15px; align-items: center; padding-bottom: 15px; border-bottom: 1px solid var(--border); }
.top-item:last-child { border: none; padding: 0; }
.rank-num { font-size: 1.4rem; font-weight: 900; color: var(--text-muted); opacity: 0.3; width: 35px; }
.rank-1 { color: var(--secondary); opacity: 1; }
.rank-2 { color: #f59e0b; opacity: 1; }
.rank-3 { color: #10b981; opacity: 1; }
.top-info h5 { margin: 0; font-size: 0.95rem; line-height: 1.4; transition: 0.2s; }
.top-info a { text-decoration: none; color: var(--text-main); }
.top-info a:hover h5 { color: var(--primary); }

/* Bento content shadow */
.hero-bento .bento-1 .content {
    position: absolute; bottom: 0; left: 0; right: 0; padding: 40px;
    background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
    color: white; border-radius: 0 0 var(--radius-lg) var(--radius-lg);
}
</style>