<header>
    <div class="container header-content">
        <div style="display: flex; align-items: center; gap: 40px;">
            <a href="./" class="logo">FStory</a>
            <nav class="main-nav">
                <a href="./" class="nav-link">Trang chủ</a>

            </nav>
        </div>

        <div class="search-bar">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Bạn đang tìm kiếm gì vậy?">
        </div>

        <div class="nav-actions">
            <button class="icon-btn" id="themeToggle"><i class="fa-solid fa-moon"></i></button>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="creator" class="btn-write">
                    <i class="fa-solid fa-pen-nib"></i> Sáng tác
                </a>
                <div class="icon-btn" style="border: 2px solid var(--primary); cursor: pointer;">
                    <img src="<?php echo $_SESSION['user_avatar'] ?? 'https://i.pravatar.cc/100?img=12'; ?>" alt="Profile">
                </div>
            <?php else: ?>
                <a href="page/user/account" class="btn-write" style="background: var(--primary); color: white;">FMember</a>
            <?php endif; ?>
        </div>
    </div>
</header>