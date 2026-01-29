<?php
// 1. TRUY VẤN TỐI ƯU (Giữ logic thông minh để lấy số liệu)
$user_id = $_SESSION['user_id'];
$sql = "SELECT s.*, COUNT(c.id) as total_chapters, MAX(c.created_at) as last_chapter_date 
        FROM stories s 
        LEFT JOIN chapters c ON s.id = c.story_id 
        WHERE s.user_id = ? 
        GROUP BY s.id 
        ORDER BY s.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$myStories = $stmt->fetchAll();

// Tính toán chỉ số cho Sidebar
$stat_total_stories = count($myStories);
$stat_total_chapters = 0;
foreach ($myStories as $s) {
    $stat_total_chapters += $s['total_chapters'];
}
?>

<main class="container" style="margin-top: 30px;">
    <div class="section-header">
        <h2 class="section-title">FStudio của tôi</h2>
        <div>
            <a href="page/creator/add" class="btn-write" style="background: var(--primary); color: white; margin: 0px 4px; border-radius: 50%; padding: 8px 13px 8px 13px;">
                <i class="fa-solid fa-plus"></i>
            </a>
            <a class="btn-write" id="themeToggle" style="background: var(--primary); color: white; margin: 0px 4px; border-radius: 50%; padding: 8px 13px 8px 13px;"><i class="fa-solid fa-moon"></i></a>
            <a href="/fstory/" class="btn-write" style="background: var(--primary); color: white; margin: 0px 0px; border-radius: 50%; padding: 8px 13px 8px 13px;">
                <i class="fa-solid fa-home"></i>
            </a>
        </div>
    </div>

    <div class="grid-layout">
        <section>
            <?php if (empty($myStories)): ?>
                <div class="sidebar-card shadow" style="text-align: center; padding: 50px;">
                    <i class="fa-solid fa-feather" style="font-size: 3rem; color: var(--border); margin-bottom: 20px;"></i>
                    <p style="color: var(--text-muted);">Chưa có câu chuyện nào được kể.</p>
                </div>
                <?php else: foreach ($myStories as $story): ?>

                    <div class="sidebar-card shadow" style="display: flex; gap: 20px; align-items: center; margin-bottom: 20px; transition: var(--transition);">

                        <div style="width: 80px; height: 110px; flex-shrink: 0; background: var(--bg-body); border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border);">
                            <img src="/fstory/src/<?php echo $story['cover_image'] ?: 'default-cover.jpg'; ?>"
                                alt="Cover" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                                <h3 style="font-size: 1.1rem; margin: 0;"><?php echo htmlspecialchars($story['title']); ?></h3>
                                <?php if ($story['status'] == 'ongoing'): ?>
                                    <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 10px; font-size: 0.65rem; font-weight: 700; border: 1px solid #bbf7d0;">ĐANG RA</span>
                                <?php else: ?>
                                    <span style="background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 10px; font-size: 0.65rem; font-weight: 700; border: 1px solid #bfdbfe;">HOÀN THÀNH</span>
                                <?php endif; ?>
                            </div>

                            <div style="display: flex; flex-wrap: wrap; gap: 15px; font-size: 0.85rem; color: var(--text-muted);">
                                <span><i class="fa-solid fa-list-ol"></i> <b><?php echo $story['total_chapters']; ?></b> Chương </span>
                                <span><i class="fa-regular fa-clock"></i>
                                    <?php
                                    // Hiển thị ngày cập nhật chương mới nhất, nếu ko có thì lấy ngày tạo
                                    $d = $story['last_chapter_date'] ? strtotime($story['last_chapter_date']) : strtotime($story['created_at']);
                                    echo date('d/m/Y', $d);
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <a href="page/creator/manage_story.php?id=<?php echo $story['id']; ?>"
                                class="btn-write" title="Cài đặt truyện">
                                <i class="fa-solid fa-gear"></i>
                                <span class="hide-on-mobile">Cài đặt</span>
                            </a>
                            <a href="page/creator/edit_chapter.php?story_id=<?php echo $story['id']; ?>"
                                class="btn-write" title="Viết chương tiếp theo" style="padding: 8px 15px; font-size: 0.9rem;">
                                <i class="fa-solid fa-pen"></i>
                                <span class="hide-on-mobile">Viết tiếp</span>
                            </a>
                        </div>
                    </div>
            <?php endforeach;
            endif; ?>
        </section>

        <aside>
            <div class="sidebar-card shadow">
                <h4 style="margin-bottom: 15px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                    <i class="fa-solid fa-chart-pie"></i> Thống kê
                </h4>

                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <span style="color: var(--text-muted);">Tác phẩm</span>
                    <strong style="font-size: 1.1rem;"><?php echo $stat_total_stories; ?></strong>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <span style="color: var(--text-muted);">Tổng số chương</span>
                    <strong style="font-size: 1.1rem; color: var(--primary);"><?php echo $stat_total_chapters; ?></strong>
                </div>

                <div style="margin-top: 20px; padding: 10px; background: var(--bg-body); border-radius: 8px; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                    <i class="fa-solid fa-lightbulb" style="color: #eab308;"></i>
                    Mẹo: Có thể kéo thả để sắp xếp lại thứ tự chương truyện.
                </div>
            </div>
        </aside>
    </div>
</main>

<style>
    @media (max-width: 768px) {
        .hide-on-mobile {
            display: none;
        }

        .sidebar-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar-card>div:first-child {
            width: 100% !important;
            height: 150px !important;
        }

        .sidebar-card>div:last-child {
            width: 100%;
            justify-content: center;
            margin-top: 10px;
        }
    }
</style>