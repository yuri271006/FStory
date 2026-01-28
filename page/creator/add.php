<?php 
require_once "../../assets/db.php"; 
if (!isset($_SESSION['user_id'])) { header("Location: /fstory/page/user/account"); exit(); }
?>
<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Sáng tác tác phẩm mới | FStory Studio</title>
    <link rel="stylesheet" href="/fstory/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .creator-card { max-width: 900px; margin: 40px auto; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border); overflow: hidden; }
        .creator-header { padding: 30px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, rgba(99, 102, 241, 0.05), transparent); }
        .creator-body { padding: 40px; display: grid; grid-template-columns: 280px 1fr; gap: 40px; }
        
        /* Upload Area */
        .upload-wrapper { position: relative; }
        .upload-label { 
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            width: 100%; height: 380px; border: 2px dashed var(--border); border-radius: var(--radius-md);
            cursor: pointer; transition: var(--transition); background: var(--bg-body); overflow: hidden;
        }
        .upload-label:hover { border-color: var(--primary); background: rgba(99, 102, 241, 0.02); }
        #cover-preview { width: 100%; height: 100%; object-fit: cover; display: none; }
        .upload-placeholder { text-align: center; padding: 20px; color: var(--text-muted); }
        
        /* Form Styling */
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; font-weight: 800; font-size: 0.85rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px; }
        .form-control { 
            width: 100%; padding: 14px; border-radius: var(--radius-md); border: 1.5px solid var(--border);
            background: var(--bg-body); color: var(--text-main); font-family: inherit; transition: var(--transition);
        }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); background: var(--bg-card); }
        
        @media (max-width: 768px) { .creator-body { grid-template-columns: 1fr; } .upload-label { height: 300px; } }
    </style>
</head>
<body>
    <?php include "../../view/header.php"; ?>

    <main class="container">
        <div class="creator-card shadow">
            <div class="creator-header">
                <h2 class="section-title">Thông tin tác phẩm</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Hãy điền đầy đủ thông tin để độc giả dễ dàng tìm thấy truyện của bạn.</p>
            </div>

            <form id="storyForm" class="creator-body" enctype="multipart/form-data">
                <div class="upload-wrapper">
                    <label class="form-label" style="display:block; margin-bottom:10px; font-weight:800; font-size:0.85rem; color:var(--text-muted);">ẢNH BÌA TRUYỆN</label>
                    <label class="upload-label" for="cover_file">
                        <img id="cover-preview" src="#" alt="Preview">
                        <div id="placeholder-content" class="upload-placeholder">
                            <i class="fa-solid fa-image" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                            <p style="font-weight: 700;">Tải ảnh từ máy</p>
                            <p style="font-size: 0.75rem;">Dưới 2MB (JPG, PNG)</p>
                        </div>
                    </label>
                    <input type="file" id="cover_file" name="cover_file" hidden accept="image/*" onchange="previewCover(this)">
                </div>

                <div class="fields-wrapper">
                    <div class="form-group">
                        <label>Tên tác phẩm</label>
                        <input type="text" name="title" class="form-control" required placeholder="Ví dụ: Kiếm Lai">
                    </div>
                    
                    <div class="form-group">
                        <label>Mô tả / Tóm tắt nội dung</label>
                        <textarea name="description" class="form-control" rows="10" placeholder="Viết vài dòng giới thiệu về câu chuyện của bạn..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit" style="width:100%; padding:16px; background:var(--primary); color:white; border-radius:var(--radius-md); font-weight:800; margin-top:10px;">
                        <i class="fa-solid fa-feather-pointed"></i> BẮT ĐẦU XUẤT BẢN
                    </button>
                    <div id="statusMsg" style="margin-top:15px; display:none; padding:12px; border-radius:8px; font-weight:600; text-align:center;"></div>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewCover(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('cover-preview');
                    const placeholder = document.getElementById('placeholder-content');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('storyForm').onsubmit = async (e) => {
            e.preventDefault();
            const msg = document.getElementById('statusMsg');
            const btn = e.target.querySelector('button');
            const formData = new FormData(e.target);

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ĐANG XỬ LÝ...';

            try {
                const res = await fetch('api_add_story', { method: 'POST', body: formData });
                const data = await res.json();
                
                msg.style.display = 'block';
                msg.style.background = data.success ? '#dcfce7' : '#fee2e2';
                msg.style.color = data.success ? '#15803d' : '#b91c1c';
                msg.innerHTML = data.message;

                if(data.success) setTimeout(() => window.location.href = '/fstory/creator', 1500);
            } catch (err) {
                msg.innerHTML = "Lỗi kết nối!";
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-feather-pointed"></i> BẮT ĐẦU XUẤT BẢN';
            }
        };
    </script>
</body>
</html>