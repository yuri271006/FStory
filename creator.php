<?php
require_once "assets/db.php";
?>
<!DOCTYPE html>
<html lang="vi" data-theme="light">

<head>
    <!--Meta-->
    <?php
    // Đặt tiêu đề riêng cho trang này
    $page_title = "FStudio";
    $page_desc = "Không gian sáng tạo chuyên nghiệp dành cho tác giả FStory. Nâng tầm bản thảo với bộ công cụ quản trị thông minh và giao diện soạn thảo tinh tế.";

    // Sau đó mới include
    include "view/meta_tag.php";
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!--Add assets-->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>


    <?php if (isset($_SESSION['user_id'])): ?>

        <?php
        include "view/creator_main.php";
        ?>

    <?php else: ?>

        <style>
            /* --- FSTUDIO MODERN PUBLISHING THEME --- */
            :root {
                --fs-bg: var(--bg-body);
                --fs-card: var(--bg-card);
                --fs-text: var(--text-main);
                --fs-sub: var(--text-muted);
                --fs-primary: var(--primary);
                --fs-border: var(--border);
            }

            .fs-landing {
                font-family: 'Plus Jakarta Sans', sans-serif;
                color: var(--fs-text);
                overflow: hidden;
            }

            /* TYPOGRAPHY */
            .fs-serif {
                font-family: 'Merriweather', serif;
            }

            .fs-hero-title {
                font-size: 3rem;
                line-height: 1.2;
                font-weight: 900;
                margin-bottom: 20px;
                color: var(--fs-text);
            }

            .fs-hero-desc {
                font-size: 1.1rem;
                line-height: 1.6;
                color: var(--fs-sub);
                max-width: 600px;
                margin: 0 auto 35px;
            }

            /* BUTTONS */
            .fs-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 14px 32px;
                border-radius: 8px;
                font-weight: 700;
                text-decoration: none;
                transition: 0.2s;
                font-size: 1rem;
                cursor: pointer;
            }

            .fs-btn-pri {
                background: var(--fs-primary);
                color: white;
                border: 1px solid var(--fs-primary);
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
            }

            .fs-btn-pri:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35);
            }

            .fs-btn-sec {
                background: var(--fs-card);
                color: var(--fs-text);
                border: 1px solid var(--fs-border);
            }

            .fs-btn-sec:hover {
                border-color: var(--fs-primary);
                color: var(--fs-primary);
            }

            /* LAYOUT */
            .fs-container {
                max-width: 1100px;
                margin: 0 auto;
                padding: 0 20px;
            }

            /* HERO SECTION */
            .fs-hero-section {
                text-align: center;
                padding: 80px 0 60px;
                background: radial-gradient(circle at top, rgba(99, 102, 241, 0.08), transparent 60%);
            }

            /* MOCKUP EDITOR (Đơn giản hóa) */
            .fs-preview-box {
                background: var(--fs-card);
                border: 1px solid var(--fs-border);
                border-radius: 12px;
                padding: 30px;
                margin: 0 auto;
                max-width: 800px;
                box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
                text-align: left;
                position: relative;
            }

            .fs-preview-header {
                border-bottom: 1px solid var(--fs-border);
                padding-bottom: 15px;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .fs-badge-live {
                font-size: 0.75rem;
                font-weight: 700;
                color: #16a34a;
                background: #dcfce7;
                padding: 4px 10px;
                border-radius: 20px;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            /* FEATURES GRID */
            .fs-features-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
                padding: 80px 0;
                border-bottom: 1px solid var(--fs-border);
            }

            .fs-feat-card {
                padding: 20px;
                transition: 0.3s;
            }

            .fs-feat-icon {
                width: 48px;
                height: 48px;
                border-radius: 10px;
                background: var(--bg-body);
                border: 1px solid var(--fs-border);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                color: var(--fs-primary);
                margin-bottom: 20px;
            }

            .fs-feat-title {
                font-weight: 700;
                font-size: 1.1rem;
                margin-bottom: 10px;
            }

            .fs-feat-desc {
                font-size: 0.95rem;
                color: var(--fs-sub);
                line-height: 1.5;
            }

            /* CTA BANNER */
            .fs-cta-banner {
                background: var(--fs-card);
                border: 1px solid var(--fs-border);
                border-radius: 20px;
                padding: 60px 20px;
                text-align: center;
                margin: 80px 0;
            }

            @media (max-width: 768px) {
                .fs-hero-title {
                    font-size: 2.2rem;
                }

                .fs-features-grid {
                    grid-template-columns: 1fr;
                    gap: 40px;
                }

                .fs-preview-box {
                    padding: 15px;
                }
            }
        </style>

        <div class="fs-landing">
            <div class="fs-hero-section">
                <div class="fs-container">
                    <span style="color: var(--fs-primary); font-weight: 700; letter-spacing: 1px; font-size: 0.85rem; text-transform: uppercase; display: block; margin-bottom: 15px;">FStudio</span>

                    <h1 class="fs-hero-title fs-serif">
                        Nơi những câu chuyện<br>tìm thấy nhau
                    </h1>

                    <p class="fs-hero-desc">
                        Nền tảng sáng tác hiện đại, miễn phí và mạnh mẽ!<br>
                        "Chúng tôi muốn những con chữ được sống cuộc đời của chính nó."
                    </p>

                    <div style="display: flex; gap: 15px; justify-content: center; margin-bottom: 50px;">
                        <a href="page/user/account.php" class="fs-btn fs-btn-pri">
                            Đăng nhập FStudio
                        </a>
                        <a href="/fstory/" class="fs-btn fs-btn-sec">
                            Về trang chủ
                        </a>
                    </div>

                    <div class="fs-preview-box">
                        <div class="fs-preview-header">
                            <div style="font-weight: 700; color: var(--fs-text); font-size: 0.9rem;">
                                <i class="fa-solid fa-chevron-left" style="margin-right: 10px; color: var(--fs-sub);"></i>
                                Chương 1: Sự khởi đầu
                            </div>
                            <div class="fs-badge-live"><i class="fa-solid fa-check"></i> Đã lưu</div>
                        </div>
                        <h3 class="fs-serif" style="font-size: 1.5rem; margin-bottom: 15px; color: var(--fs-text);">Chương 1: Bình minh trên vùng đất mới</h3>
                        <p style="font-family: 'Merriweather', serif; color: var(--fs-sub); line-height: 1.8; margin-bottom: 15px;">
                            Cơn gió lạnh buốt thổi qua thảo nguyên, mang theo mùi hương của cỏ dại và đất ẩm. Hắn đứng đó, nhìn về phía chân trời nơi mặt trời đang dần ló dạng...
                        </p>
                        <p style="font-family: 'Merriweather', serif; color: var(--fs-sub); line-height: 1.8; width: 80%;">
                            "Vậy ra đây là thế giới mới sao?" - Hắn lẩm bẩm, bàn tay siết chặt thanh kiếm cũ kỹ bên hông.
                        </p>
                        <div style="margin-top: 30px; border-top: 1px dashed var(--fs-border); padding-top: 15px; display: flex; justify-content: flex-end;">
                            <div style="background: var(--fs-primary); width: 80px; height: 30px; border-radius: 4px; opacity: 0.8;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fs-container" id="features">
                <div class="fs-features-grid">
                    <div class="fs-feat-card">
                        <div class="fs-feat-icon"><i class="fa-solid fa-laptop-code"></i></div>
                        <div class="fs-feat-title">Editor chuyên dụng</div>
                        <p class="fs-feat-desc">Soạn thảo mượt mà với chế độ Zen Mode. Tự động lưu bản nháp và đếm chữ giúp bạn quản lý tiến độ.</p>
                    </div>
                    <div class="fs-feat-card">
                        <div class="fs-feat-icon"><i class="fa-solid fa-chart-pie"></i></div>
                        <div class="fs-feat-title">Thống kê chi tiết</div>
                        <p class="fs-feat-desc">Hệ thống đo lường lượt đọc, lượt yêu thích chính xác. Biết được độc giả của bạn thích gì.</p>
                    </div>
                    <div class="fs-feat-card">
                        <div class="fs-feat-icon"><i class="fa-solid fa-globe"></i></div>
                        <div class="fs-feat-title">Tiếp cận độc giả</div>
                        <p class="fs-feat-desc">Tác phẩm được đề xuất trên trang chủ FStory. Tối ưu hóa SEO để tìm kiếm dễ dàng trên Google.</p>
                    </div>
                </div>
            </div>

            <div class="fs-container">
                <div class="fs-cta-banner">
                    <h2 class="fs-serif" style="font-size: 2rem; margin-bottom: 15px;">Trở thành FMember</h2>
                    <p style="color: var(--fs-sub); margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
                        Để sử dụng tính năng này cần có tài khoản FMember, hãy tham gia với chúng tôi để con chữ được sống một cuộc đời trọn vẹn trong thế giới của chính nó!
                    </p>
                    <a href="page/user/account.php" class="fs-btn fs-btn-pri" style="padding: 16px 40px; font-size: 1.1rem;">
                        Tạo tài khoản FMember
                    </a>
                    <div style="margin-top: 20px; font-size: 0.85rem; color: var(--fs-sub);">
                        <i class="fa-solid fa-shield-halved"></i> Bảo vệ bản quyền nội dung 100%
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>



    <!--Script-->
    <script src="assets/js/system_display.js"></script>
</body>

</html>