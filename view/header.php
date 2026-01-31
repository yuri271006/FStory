<header>
    <div class="container header-content">
        <div style="display: flex; align-items: center; gap: 40px;">
            <a href="./" class="logo">FStory</a>
            <nav class="main-nav">
                <a href="./" class="nav-link">Trang chủ</a>
                <a href="" class="nav-link">Thể loại</a>
                <a href="" class="nav-link">Kiếm tiền</a>
                <a href="" class="nav-link">Tin tức</a>
                <a href="" class="nav-link">Ủng hộ</a>
            </nav>
        </div>

        <div class="search-bar">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Bạn đang tìm kiếm gì vậy?">
        </div>

        <div class="nav-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="creator" class="btn-write">
                    <i class="fa-solid fa-edit"></i> FStudio
                </a>
                <button class="icon-btn" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
                <a href="@<?php echo $_SESSION['user_handle']; ?>"
                    class="icon-btn"
                    style="border: 2px solid var(--primary); cursor: pointer; overflow: hidden; display: flex;">
                    <img src="src/avt/<?php echo $_SESSION['user_avatar'] ?? 'default_avt.png'; ?>" alt="Profile">
                </a>
            <?php else: ?>
                <button class="icon-btn" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
                <a href="page/user/account" class="btn-write" style="background: var(--primary); color: white;">FMember</a>
            <?php endif; ?>
        </div>
    </div>
</header>