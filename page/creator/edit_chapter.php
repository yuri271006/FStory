<?php
require_once "../../assets/db.php";
$user_id = $_SESSION['user_id'] ?? 0;

// ==========================================
// 1. XỬ LÝ API (LƯU TRUYỆN)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Hết phiên đăng nhập!']);
        exit();
    }

    $story_id = $_POST['story_id'] ?? 0;
    $chapter_id = $_POST['chapter_id'] ?? 0;
    $chap_num = $_POST['chapter_number'] ?? 0;
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';

    try {
        if ($chapter_id > 0) {
            $sql = "UPDATE chapters SET chapter_number = ?, title = ?, content = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$chap_num, $title, $content, $chapter_id]);
            echo json_encode(['success' => true, 'id' => $chapter_id, 'message' => 'Đã cập nhật chương thành công!']);
        } else {
            $sql = "INSERT INTO chapters (story_id, chapter_number, title, content) VALUES (?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$story_id, $chap_num, $title, $content]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId(), 'message' => 'Đã tạo chương mới thành công!']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
    exit();
}

// ==========================================
// 2. LẤY DỮ LIỆU GIAO DIỆN
// ==========================================
$story_id = $_GET['story_id'] ?? 0;
$chapter_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT id, title FROM stories WHERE id = ? AND user_id = ?");
$stmt->execute([$story_id, $user_id]);
$story = $stmt->fetch();
if (!$story) {
    header("Location: /fstory/creator");
    exit();
}

$chapter = ['chapter_number' => '', 'title' => '', 'content' => ''];
if ($chapter_id) {
    $cStmt = $pdo->prepare("SELECT * FROM chapters WHERE id = ?");
    $cStmt->execute([$chapter_id]);
    $chapter = $cStmt->fetch() ?: $chapter;
} else {
    $maxStmt = $pdo->prepare("SELECT MAX(chapter_number) FROM chapters WHERE story_id = ?");
    $maxStmt->execute([$story_id]);
    $chapter['chapter_number'] = ($maxStmt->fetchColumn() ?: 0) + 1;
}
?>

<!DOCTYPE html>
<html lang="vi" data-theme="light">

<head>
    <meta charset="UTF-8">
    <title>FStudio | Editor_<?php echo htmlspecialchars($story['title']); ?></title>
    <link rel="stylesheet" href="/fstory/assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/img/fstory_logo.png" type="image/x-icon">

    <style>
        /* CSS RESET & LAYOUT */
        body {
            background: var(--bg-card);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin: 0;
        }

        /* ẨN BRANDING EDITOR */
        .tox-statusbar__branding,
        .tox-promotion {
            display: none !important;
        }

        .tox-tinymce {
            border: none !important;
            flex: 1;
        }

        /* HEADER CHUYÊN NGHIỆP */
        .editor-header {
            height: 60px;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            z-index: 100;
        }

        /* INPUT AREA */
        .editor-meta {
            padding: 0px 15px;
            background: var(--bg-body);
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .input-clean {
            border: 1px solid transparent;
            background: transparent;
            padding: 10px;
            font-family: inherit;
            color: var(--text-main);
            border-radius: 6px;
            transition: 0.2s;
        }

        /* .input-clean:focus {
            background: var(--bg-card);
            border-color: var(--primary);
            outline: none;
        } */

        .num-inp {
            width: 90px;
            font-weight: 800;
            color: var(--primary);
            font-size: 1.1rem;
            text-align: center;
        }

        .title-inp {
            flex: 1;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* CUSTOM TOAST (Thay alert) */
        #fToast {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #1e293b;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            transform: translateY(150px);
            transition: 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        #fToast.show {
            transform: translateY(0);
        }

        /* CUSTOM MODAL (Thay confirm) */
        .f-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
        }

        .f-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .f-modal {
            background: var(--bg-card);
            width: 350px;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            transform: scale(0.9);
            transition: 0.3s;
        }

        .f-modal-overlay.active .f-modal {
            transform: scale(1);
        }

        .f-modal-btns {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-m {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="editor-header">
        <div style="display: flex; justify-content: center; align-items: center;">
            <button onclick="requestExit()" class="icon-btn" title="Quay lại">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <!-- <button class="icon-btn" id="themeToggle" style="margin-left: 8px;"><i class="fa-solid fa-moon"></i></button> -->
        </div>
        <div style="text-align: center;">
            <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; font-weight: 800;">
                EDITOR_<?php echo htmlspecialchars($story['title']); ?>
            </div>
            <div id="saveState" style="font-size: 0.75rem; color: #22c55e; font-weight: 700;">
                Sẵn sàng biên tập
            </div>
        </div>

        <button onclick="saveChapter()" id="saveBtn" class="btn-write" style="background: var(--primary); color: white; border-radius: 8px;">
            <i class="fa-solid fa-save"></i>
        </button>
    </div>

    <form id="editorForm" style="display: flex; flex-direction: column; flex: 1;">
        <input type="hidden" name="story_id" value="<?php echo $story_id; ?>">
        <input type="hidden" name="chapter_id" id="chapterId" value="<?php echo $chapter_id; ?>">

        <div class="editor-meta">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:0.75rem; font-weight:800; color:var(--text-muted);">CHAPTER</span>
                <input type="number" name="chapter_number" class="input-clean num-inp" value="<?php echo $chapter['chapter_number']; ?>" required>
            </div>
            <div style="width:1px; height:20px; background:var(--border);"></div>
            <input type="text" name="title" class="input-clean title-inp" value="<?php echo htmlspecialchars($chapter['title']); ?>" placeholder="YOUR TITLE">
        </div>

        <textarea id="mainEditor" name="content"><?php echo htmlspecialchars($chapter['content']); ?></textarea>
    </form>

    <div id="fToast"><i class="fa-solid fa-circle-info"></i> <span id="toastMsg"></span></div>

    <div class="f-modal-overlay" id="exitModal">
        <div class="f-modal">
            <h3 style="margin:0 0 10px 0;">Bạn rời đi sao?</h3>
            <p style="font-size:0.9rem; color:var(--text-muted);">Hãy chắc chắn đã lưu các thay đổi trước khi rời đi bạn nhé!</p>
            <div class="f-modal-btns">
                <button class="btn-m" onclick="closeModal()" style="background:var(--bg-body); color:var(--text-muted);">Ở LẠI</button>
                <button class="btn-m" onclick="goBack()" style="background:#ef4444; color:white;">THOÁT</button>
            </div>
        </div>
    </div>

    <script>
        // Cấu hình TinyMCE gọn gàng
        tinymce.init({
            selector: '#mainEditor',
            branding: false,
            promotion: false,
            menubar: false,
            statusbar: true,
            plugins: 'autolink lists link charmap fullscreen wordcount',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | fullscreen',
            content_style: `
                body { font-family: 'Lora', serif; font-size: 18px; line-height: 1.8; color: #334155; padding: 0 40px 0px 40px; margin: 0 auto; }
                p { margin-bottom: 20px; }
            `,
            setup: function(editor) {
                editor.on('input change', () => {
                    document.getElementById('saveState').innerHTML = 'Đang soạn thảo';
                });
                editor.on('keydown', (e) => {
                    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                        e.preventDefault();
                        saveChapter();
                    }
                });
            }
        });

        // Hàm thông báo Toast
        function toast(text) {
            const t = document.getElementById('fToast');
            document.getElementById('toastMsg').innerText = text;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        // Xử lý Lưu chương
        async function saveChapter() {
            tinymce.triggerSave();
            const btn = document.getElementById('saveBtn');
            const state = document.getElementById('saveState');

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

            try {
                const formData = new FormData(document.getElementById('editorForm'));
                const res = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    toast(data.message);
                    state.innerHTML = 'Đã lưu hệ thống';
                    if (data.id && !document.getElementById('chapterId').value) {
                        document.getElementById('chapterId').value = data.id;
                        const url = new URL(window.location);
                        url.searchParams.set('id', data.id);
                        window.history.pushState({}, '', url);
                    }
                } else {
                    toast(data.message);
                }
            } catch (e) {
                toast('Lỗi kết nối!');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i>';
            }
        }

        // Logic Modal thoát
        function requestExit() {
            document.getElementById('exitModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('exitModal').classList.remove('active');
        }

        function goBack() {
            window.location.href = 'manage_story.php?id=<?php echo $story_id; ?>';
        }
    </script>
</body>

</html>