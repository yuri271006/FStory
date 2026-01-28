<?php
require_once "../../assets/db.php";
$story_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Check quyền
$stmt = $pdo->prepare("SELECT * FROM stories WHERE id = ? AND user_id = ?");
$stmt->execute([$story_id, $user_id]);
$story = $stmt->fetch();
if (!$story) { header("Location: /fstory/creator"); exit(); }

// Lấy danh sách chương (Sắp xếp tăng dần để đọc đúng thứ tự)
$chapStmt = $pdo->prepare("SELECT * FROM chapters WHERE story_id = ? ORDER BY chapter_number ASC");
$chapStmt->execute([$story_id]);
$chapters = $chapStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <title>Quản lý: <?php echo htmlspecialchars($story['title']); ?></title>
    <link rel="stylesheet" href="/fstory/assets/css/style.css">
    <link rel="stylesheet" href="/fstory/assets/css/studio.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
</head>
<body>
    <?php include "../../view/header.php"; ?>
    <main class="container">
        <div class="studio-grid">
            <aside class="studio-sidebar">
                <div class="sidebar-card shadow" style="text-align: center;">
                    <div style="width: 140px; height: 200px; margin: 0 auto 15px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                        <img src="/fstory/src/<?php echo $story['cover_image']; ?>" id="coverPreview" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 5px;"><?php echo htmlspecialchars($story['title']); ?></h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 20px;">
                        <?php echo count($chapters); ?> Chương • <?php echo ucfirst($story['status']); ?>
                    </p>
                    <a href="edit_chapter.php?story_id=<?php echo $story['id']; ?>" class="btn-write" style="width: 100%; background: var(--primary); color: white; display: block; margin-bottom: 10px;">
                        <i class="fa-solid fa-plus"></i> Viết Chương Mới
                    </a>
                    <button onclick="deleteStory(<?php echo $story['id']; ?>)" class="btn-write" style="width: 100%; color: #ef4444; border-color: #ef4444;">
                        <i class="fa-solid fa-trash"></i> Xóa Truyện
                    </button>
                </div>
            </aside>

            <section>
                <div class="studio-tabs">
                    <div class="tab-btn active" onclick="openTab('chapters')">Danh sách chương</div>
                    <div class="tab-btn" onclick="openTab('settings')">Thiết lập truyện</div>
                </div>

                <div id="tab-chapters">
                    <?php if(empty($chapters)): ?>
                        <div class="sidebar-card shadow" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            Chưa có chương nào. Hãy bắt đầu viết ngay!
                        </div>
                    <?php else: ?>
                        <div style="margin-bottom: 10px; color: var(--text-muted); font-size: 0.85rem;">
                            <i class="fa-solid fa-arrows-up-down"></i> Kéo thả để sắp xếp lại thứ tự chương.
                        </div>
                        <div id="chapterList">
                            <?php foreach($chapters as $c): ?>
                                <div class="chapter-item shadow" data-id="<?php echo $c['id']; ?>">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <i class="fa-solid fa-grip-vertical" style="color: var(--text-muted);"></i>
                                        <div>
                                            <h4 style="font-size: 1rem;">
                                                <span class="chap-num">Chương <?php echo $c['chapter_number']; ?></span>: 
                                                <?php echo htmlspecialchars($c['title']); ?>
                                            </h4>
                                            <small style="color: var(--text-muted);"><?php echo date('H:i d/m/Y', strtotime($c['created_at'])); ?></small>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="edit_chapter.php?id=<?php echo $c['id']; ?>&story_id=<?php echo $story['id']; ?>" class="icon-btn"><i class="fa-solid fa-pen"></i></a>
                                        <button onclick="reqDelChapter(<?php echo $c['id']; ?>)" class="icon-btn" style="color: #ef4444;"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="tab-settings" style="display: none;">
                    <form id="updateStoryForm" class="sidebar-card shadow" onsubmit="updateStory(event)">
                        <input type="hidden" name="id" value="<?php echo $story['id']; ?>">
                        
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: 700; font-size: 0.9rem;">Tên tác phẩm</label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($story['title']); ?>" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; margin-top: 8px;">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: 700; font-size: 0.9rem;">Mô tả / Giới thiệu</label>
                            <textarea name="description" rows="6" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; margin-top: 8px; font-family: inherit;"><?php echo htmlspecialchars($story['description']); ?></textarea>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: 700; font-size: 0.9rem;">Thay đổi ảnh bìa</label>
                            <div class="file-upload-wrapper">
                                <span class="btn-upload">Click để chọn ảnh mới</span>
                                <input type="file" name="cover_file" accept="image/*" onchange="previewImage(this)">
                            </div>
                        </div>

                        <button type="submit" class="btn-write" style="width: 100%; background: var(--primary); color: white; padding: 12px;">Lưu thay đổi</button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <script src="/fstory/assets/js/studio_ui.js"></script>
    <script>
        function openTab(name) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-btn')[name === 'chapters' ? 0 : 1].classList.add('active');
            document.getElementById('tab-chapters').style.display = name === 'chapters' ? 'block' : 'none';
            document.getElementById('tab-settings').style.display = name === 'settings' ? 'block' : 'none';
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const r = new FileReader();
                r.onload = e => document.getElementById('coverPreview').src = e.target.result;
                r.readAsDataURL(input.files[0]);
            }
        }

        // Kéo thả sắp xếp
        const el = document.getElementById('chapterList');
        if(el) {
            new Sortable(el, {
                animation: 150, ghostClass: 'sortable-ghost',
                onEnd: async () => {
                    const ids = Array.from(document.querySelectorAll('.chapter-item')).map(i => i.getAttribute('data-id'));
                    document.querySelectorAll('.chapter-item').forEach((item, idx) => {
                        item.querySelector('.chap-num').innerText = "Chương " + (idx + 1);
                    });
                    try {
                        const res = await fetch('api_reorder_chapters.php', {
                            method: 'POST', headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({ story_id: <?php echo $story_id; ?>, order: ids })
                        });
                        const d = await res.json();
                        d.success ? showToast('Đã cập nhật thứ tự!') : showToast(d.message, 'error');
                    } catch(e) { showToast('Lỗi kết nối!', 'error'); }
                }
            });
        }

        async function updateStory(e) {
            e.preventDefault();
            const fd = new FormData(e.target);
            try {
                const res = await fetch('api_update_story.php', { method:'POST', body:fd });
                const d = await res.json();
                if(d.success) { showToast('Cập nhật thành công!'); setTimeout(() => location.reload(), 1000); }
                else showToast(d.message, 'error');
            } catch(e) { showToast('Lỗi server', 'error'); }
        }

        function reqDelChapter(id) {
            confirmModal('Xóa chương?', 'Bạn chắc chắn muốn xóa chương này?', async () => {
                const res = await fetch('api_delete_chapter.php', { method:'POST', body: new URLSearchParams({'id':id}) });
                const d = await res.json();
                if(d.success) { showToast('Đã xóa chương!'); setTimeout(() => location.reload(), 1000); }
            }, true);
        }

        function deleteStory(id) {
            confirmModal('Xóa Truyện?', 'Tất cả chương và ảnh bìa sẽ bị xóa vĩnh viễn!', async () => {
                const res = await fetch('api_delete_story.php', { method:'POST', body: new URLSearchParams({'id':id}) });
                const d = await res.json();
                if(d.success) window.location.href = '/fstory/creator';
            }, true);
        }
    </script>
</body>
</html>