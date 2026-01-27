<?php 
session_start(); 
if (isset($_SESSION['user_id'])) {
    header("Location: ../../home");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gia nhập FStory</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <style>
        /* --- Layout Thích nghi (Responsive Grid) --- */
        .auth-wrapper {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
            margin: 40px auto;
            max-width: 1100px;
            align-items: center; /* Căn giữa theo chiều dọc */
        }

        @media (min-width: 1024px) {
            .auth-wrapper { 
                grid-template-columns: 1.1fr 0.9fr; 
                gap: 80px; 
                margin: 80px auto; 
            }
        }

        /* --- Cột Lợi ích (Benefits) --- */
        .benefits-side h2 {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            color: var(--text-main);
        }

        .benefit-list {
            display: grid;
            gap: 15px;
            margin-top: 35px;
        }

        .benefit-card {
            background: var(--bg-card);
            padding: 24px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            display: flex;
            gap: 20px;
            transition: var(--transition);
        }

        .benefit-card:hover { 
            transform: translateX(10px); 
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .benefit-icon {
            width: 52px; height: 52px; 
            background: rgba(99, 102, 241, 0.1); /* Màu primary nhẹ */
            color: var(--primary); 
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.4rem; flex-shrink: 0;
        }

        .benefit-info h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .benefit-info p {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* --- Cột Form (Auth Box) --- */
        .auth-box {
            background: var(--bg-card);
            padding: 40px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .auth-tabs {
            display: flex;
            margin-bottom: 25px;
            background: var(--bg-body);
            padding: 5px;
            border-radius: var(--radius-md);
        }

        .tab-btn {
            flex: 1; padding: 10px; 
            text-align: center; 
            font-weight: 700;
            color: var(--text-muted); 
            cursor: pointer;
            border-radius: calc(var(--radius-md) - 4px);
            transition: var(--transition);
        }

        .tab-btn.active { 
            background: var(--bg-card);
            color: var(--primary); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .form-group { margin-bottom: 24px; }
        .form-group label { 
            display: block; 
            margin-bottom: 10px; 
            font-weight: 700; 
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .form-group input {
            width: 100%; 
            padding: 14px 18px; 
            border-radius: var(--radius-md);
            border: 1.5px solid var(--border); 
            background: var(--bg-body); 
            color: var(--text-main);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus {
            border-color: var(--primary);
            background: var(--bg-card);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* Thông báo AJAX */
        .msg-box {
            padding: 14px; border-radius: var(--radius-md); margin-bottom: 25px;
            font-size: 0.95rem; display: none; font-weight: 600; text-align: center;
        }
        .msg-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .msg-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

        .btn-submit {
            width: 100%; 
            padding: 16px; 
            background: var(--primary); 
            color: white;
            border-radius: var(--radius-md);
            font-weight: 800;
            font-size: 1rem;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
            transition: var(--transition);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 20px -5px rgba(99, 102, 241, 0.4);
            filter: brightness(1.1);
        }

        .hidden { display: none; }

        @media (max-width: 768px) {
            .auth-box { padding: 30px 20px; }
            .benefits-side h2 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
<header>
    <div class="container header-content">
        <div style="display: flex; align-items: center; gap: 40px;">
            <a href="./" class="logo">FStory</a>
        </div>
        <div class="nav-actions">
            <button class="icon-btn" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
            <a href="../../" class="btn-write" style="background: var(--primary); color: white;">Trang chủ</a>
        </div>
    </div>
</header>

    <main class="container">
        <div class="auth-wrapper">
            
            <div class="benefits-side">
                <h2>Bắt đầu hành trình <br> tại FStory ngay hôm nay!</h2>
                <p style="color: var(--text-muted); font-size: 1.1rem;">Trở thành một phần của cộng đồng yêu truyện để trải nghiệm những tính năng đặc quyền.</p>
                
                <div class="benefit-list">
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-bookmark"></i></div>
                        <div class="benefit-info">
                            <h4>Tủ truyện cá nhân</h4>
                            <p>Lưu lại những bộ truyện yêu thích và đồng bộ trên mọi thiết bị.</p>
                        </div>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div class="benefit-info">
                            <h4>Lịch sử đọc thông minh</h4>
                            <p>Tự động ghi nhớ chương bạn đang đọc dở để tiếp tục bất cứ lúc nào.</p>
                        </div>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-bell"></i></div>
                        <div class="benefit-info">
                            <h4>Thông báo chương mới</h4>
                            <p>Nhận thông báo ngay lập tức khi tác giả bạn theo dõi ra chương mới.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-box">
                <div class="auth-tabs">
                    <div class="tab-btn active" id="tabLogin" onclick="toggleAuth('login')">Đăng nhập</div>
                    <div class="tab-btn" id="tabRegister" onclick="toggleAuth('register')">Đăng ký</div>
                </div>

                <div id="msgBox" class="msg-box"></div>

                <form id="loginForm" onsubmit="handleAuth(event, 'login')">
                    <div class="form-group">
                        <label>Tài khoản</label>
                        <input type="text" name="username" required placeholder="Email hoặc tên đăng nhập">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" name="password" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn-submit">
                        Vào thế giới truyện
                    </button>
                </form>

                <form id="registerForm" class="hidden" onsubmit="handleAuth(event, 'register')">
                    <div class="form-group">
                        <label>Tên hiển thị</label>
                        <input type="text" name="nickname" required placeholder="Ví dụ: Chu Minh Thụy">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="email@fstory.vn">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" name="password" required placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <button type="submit" class="btn-submit">
                        Tạo tài khoản mới
                    </button>
                </form>
            </div>
        </div>
    </main>


    <script src="../../assets/js/system_display.js"></script>
    <script>
        function toggleAuth(type) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const tabLogin = document.getElementById('tabLogin');
            const tabRegister = document.getElementById('tabRegister');
            const msgBox = document.getElementById('msgBox');

            msgBox.style.display = 'none';
            if(type === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                tabLogin.classList.add('active');
                tabRegister.classList.remove('active');
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                tabLogin.classList.remove('active');
                tabRegister.classList.add('active');
            }
        }

        async function handleAuth(event, type) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const msgBox = document.getElementById('msgBox');
            const apiEndpoint = type === 'login' ? 'api_login.php' : 'api_register.php';

            try {
                const response = await fetch(apiEndpoint, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                msgBox.style.display = 'block';
                if(result.success) {
                    msgBox.className = 'msg-box msg-success';
                    msgBox.innerHTML = result.message;
                    setTimeout(() => window.location.href = '../../home', 1500);
                } else {
                    msgBox.className = 'msg-box msg-error';
                    msgBox.innerHTML = result.message;
                }
            } catch (error) {
                msgBox.style.display = 'block';
                msgBox.className = 'msg-box msg-error';
                msgBox.innerHTML = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
    </script>
</body>
</html>