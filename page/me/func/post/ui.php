<div id="activities" class="tab-content"> <?php if ($isOwner): ?>
        <div class="sidebar-card" id="post-box-container" style="margin-bottom: 20px;">
            <div id="post-collapsed" class="post-collapsed" onclick="togglePostBox(true)" style="display: flex; align-items: center; gap: 15px; cursor: pointer;">
                <input type="text" placeholder="<?php echo $profileUser['nickname'];?>, bạn đang nghĩ gì vậy?" readonly
                    style="width: 100%; padding: 10px 15px; border-radius: 20px; border: 1px solid var(--border); background: var(--bg-body); cursor: pointer; outline: none;">
            </div>

            <div id="post-expanded" class="post-expanded" style="display: none;">
                <div class="post-header" style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-bottom: 10px;">
                    <span style="font-weight: 600;">Bài đăng mới</span>
                    <button onclick="togglePostBox(false)" style="background: none; border: none; cursor: pointer; font-size: 1.2rem;"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <textarea id="postContent" class="auto-expand" rows="1" placeholder="Viết suy nghĩ của bạn đi nào..."
                    style="width: 100%; border: none; background: transparent; resize: none; outline: none; font-size: 1rem; font-family: inherit; min-height: 80px;"></textarea>

                <div class="post-actions" style="display: flex; justify-content: flex-end; margin-top: 10px;">
                    <button class="btn-write" id="btnPostSubmit" style="background: var(--primary); color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer;">
                        <i class="fa-solid fa-paper-plane"></i> Đăng tải
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div id="activity-feed">
        <?php
        // Query lấy bài viết
        $sql = "SELECT a.*, 
            (SELECT COUNT(*) FROM activity_likes WHERE activity_id = a.id) as like_count,
            (SELECT COUNT(*) FROM activity_comments WHERE activity_id = a.id) as cmt_count
            FROM user_activities a WHERE a.user_id = ? ORDER BY a.created_at DESC";
        $stmtAct = $pdo->prepare($sql);
        $stmtAct->execute([$profileUser['id']]);
        $activities = $stmtAct->fetchAll();

        if (count($activities) == 0) echo '<div class="sidebar-card"><p style="text-align:center; color:var(--text-muted);">Chưa có hoạt động nào ở đây.</p></div>';

        foreach ($activities as $act):
            $actId = $act['id'];
            $timePost = date('H:i d/m/Y', strtotime($act['created_at']));

            // Check like
            $userLiked = false;
            if (isset($_SESSION['user_id'])) {
                $check = $pdo->prepare("SELECT id FROM activity_likes WHERE user_id=? AND activity_id=?");
                $check->execute([$_SESSION['user_id'], $actId]);
                if ($check->fetch()) $userLiked = true;
            }

            // Lấy toàn bộ bình luận
            $stmtCmt = $pdo->prepare("SELECT c.*, u.nickname, u.avatar FROM activity_comments c JOIN users u ON c.user_id = u.id WHERE activity_id = ? ORDER BY created_at ASC");
            $stmtCmt->execute([$actId]);
            $allComments = $stmtCmt->fetchAll();

            // Tách cha con (Logic xử lý PHP)
            $parents = [];
            $children = [];
            foreach ($allComments as $c) {
                if ($c['parent_id']) $children[$c['parent_id']][] = $c;
                else $parents[] = $c;
            }
        ?>
            <div class="sidebar-card activity-item" id="activity-<?php echo $actId; ?>" style="margin-bottom: 15px;">
                <div class="act-header" style="display:flex; justify-content: space-between;">
                    <div style="display:flex; gap:10px;">
                        <img src="src/avt/<?php echo $profileUser['avatar'] ?? 'default_avt.png'; ?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                        <div>
                            <div style="font-weight:700;"><?php echo htmlspecialchars($profileUser['nickname']); ?></div>
                            <div style="font-size:0.8rem; color:var(--text-muted);"><?php echo $timePost; ?></div>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $act['user_id']): ?>
                        <div class="post-options">
                            <i class="fa-solid fa-ellipsis" onclick="toggleMenu(<?php echo $actId; ?>)" style="padding: 0 10px;"></i>
                            <div class="dropdown-menu" id="menu-<?php echo $actId; ?>">
                                <button class="dropdown-item" onclick="editPost(<?php echo $actId; ?>)"><i class="fa-solid fa-pen"></i> Sửa</button>
                                <button class="dropdown-item text-danger" onclick="deletePost(<?php echo $actId; ?>)"><i class="fa-solid fa-trash"></i> Xóa</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="act-body" id="content-<?php echo $actId; ?>" style="margin: 15px 0; line-height: 1.6;" data-original="<?php echo htmlspecialchars($act['content']); ?>">
                    <?php echo nl2br(htmlspecialchars($act['content'])); ?>
                </div>

                <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:var(--text-muted); margin-bottom: 5px;">
                    <span id="like-count-<?php echo $actId; ?>" onclick="showLikers(<?php echo $actId; ?>)" style="cursor:pointer;">
                        <?php if ($act['like_count'] > 0) echo '<i class="fa-solid fa-thumbs-up" style="color:var(--primary)"></i> ' . $act['like_count']; ?>
                    </span>
                    <span onclick="toggleComment(<?php echo $actId; ?>)" style="cursor:pointer;"><?php echo $act['cmt_count']; ?> bình luận</span>
                </div>

                <div class="act-actions">
                    <button class="act-btn <?php echo $userLiked ? 'liked' : ''; ?>" onclick="likePost(<?php echo $actId; ?>, this)">
                        <i class="fa-regular fa-thumbs-up"></i> Thích
                    </button>
                    <button class="act-btn" onclick="toggleComment(<?php echo $actId; ?>)">
                        <i class="fa-regular fa-comment"></i> Bình luận
                    </button>
                </div>

                <div class="comment-section" id="cmt-section-<?php echo $actId; ?>" style="<?php echo count($parents) > 0 ? 'display:block;' : ''; ?>">

                    <div class="comment-list" id="cmt-list-<?php echo $actId; ?>">
                        <?php
                        $count = 0;
                        foreach ($parents as $cmt):
                            $count++;
                            $isHidden = $count > 3 ? 'hidden-comment' : '';
                            $cmtId = $cmt['id'];
                        ?>
                            <div class="comment-wrapper <?php echo $isHidden; ?>" id="comment-wrapper-<?php echo $cmtId; ?>">
                                <div class="comment-item" style="display:flex; gap:10px; ">
                                    <img src="src/avt/<?php echo $cmt['avatar'] ?? 'default_avt.png'; ?>" style="width:30px; height:30px; border-radius:50%; object-fit:cover;">

                                    <div style="flex:1;">
                                        <div class="comment-bubble" style="display: flex; align-items: center; gap: 10px;">
                                            <div class="comment-content" id="cmt-content-<?php echo $cmtId; ?>">
                                                <div style="font-weight:700; font-size:0.9rem;"><?php echo htmlspecialchars($cmt['nickname']); ?></div>
                                                <div class="text-content"><?php echo nl2br(htmlspecialchars($cmt['content'])); ?></div>
                                            </div>

                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $cmt['user_id']): ?>
                                                <div class="post-options cmt-options">
                                                    <i class="fa-solid fa-ellipsis" onclick="toggleMenu('cmt-<?php echo $cmtId; ?>')"></i>
                                                    <div class="dropdown-menu" id="menu-cmt-<?php echo $cmtId; ?>">
                                                        <button class="dropdown-item" onclick="enableEditComment(<?php echo $cmtId; ?>)">Sửa</button>
                                                        <button class="dropdown-item text-danger" onclick="deleteComment(<?php echo $cmtId; ?>)">Xóa</button>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="comment-actions">
                                            <span class="btn-reply" onclick="openReplyBox(<?php echo $cmtId; ?>)">Trả lời</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="reply-list" id="reply-list-<?php echo $cmtId; ?>">
                                    <?php if (isset($children[$cmtId])): foreach ($children[$cmtId] as $rep): ?>
                                            <div class="reply-item" id="reply-item-<?php echo $rep['id']; ?>" style="display:flex; gap:10px;">
                                                <img src="src/avt/<?php echo $rep['avatar'] ?? 'default_avt.png'; ?>" style="width:24px; height:24px; border-radius:50%; object-fit:cover;">

                                                <div style="flex:1;">
                                                    <div class="comment-bubble" style="display: flex; align-items: center; gap: 10px;">
                                                        <div class="comment-content" id="cmt-content-<?php echo $rep['id']; ?>">
                                                            <div style="font-weight:700; font-size:0.85rem;"><?php echo htmlspecialchars($rep['nickname']); ?></div>
                                                            <div class="text-content"><?php echo nl2br(htmlspecialchars($rep['content'])); ?></div>
                                                        </div>

                                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $rep['user_id']): ?>
                                                            <div class="post-options cmt-options">
                                                                <i class="fa-solid fa-ellipsis" onclick="toggleMenu('cmt-<?php echo $rep['id']; ?>')"></i>
                                                                <div class="dropdown-menu" id="menu-cmt-<?php echo $rep['id']; ?>">
                                                                    <button class="dropdown-item" onclick="enableEditComment(<?php echo $rep['id']; ?>)">Sửa</button>
                                                                    <button class="dropdown-item text-danger" onclick="deleteComment(<?php echo $rep['id']; ?>)">Xóa</button>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="comment-actions">
                                                        <span class="btn-reply" onclick="replyToChild(<?php echo $cmtId; ?>, '<?php echo htmlspecialchars($rep['nickname']); ?>')">Trả lời</span>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php endforeach;
                                    endif; ?>
                                </div>

                                <div class="reply-input-box" id="reply-box-<?php echo $cmtId; ?>">
                                    <div style="display:flex; gap:5px;">
                                        <img src="src/avt/<?php echo $_SESSION['user_avatar'] ?? 'default_avt.png'; ?>" style="width:24px; height:24px; border-radius:50%;">
                                        <input type="text" class="comment-input" placeholder="Trả lời..."
                                            onkeydown="submitReply(event, <?php echo $actId; ?>, <?php echo $cmtId; ?>)">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($parents) > 3): ?>
                        <span class="view-more-cmt" onclick="showAllComments(<?php echo $actId; ?>, this)">Xem thêm bình luận...</span>
                    <?php endif; ?>

                    <div class="comment-input-box" style="margin-top: 15px;">
                        <img src="src/avt/<?php echo $_SESSION['user_avatar'] ?? 'default_avt.png'; ?>" style="width:30px; height:30px; border-radius:50%; object-fit: cover;">
                        <textarea class="comment-input auto-expand" placeholder="Viết bình luận..." onkeydown="submitComment(event, <?php echo $actId; ?>)"></textarea>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="likesModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <span>Người đã thích</span>
                <i class="fa-solid fa-xmark" style="cursor:pointer;" onclick="closeLikeModal()"></i>
            </div>
            <div class="liker-list" id="likerListContent">
                <div style="text-align:center;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>
            </div>
        </div>
    </div>
</div>

<script src="page/me/func/post/script.js"></script>
<link rel="stylesheet" href="page/me/func/post/style.css">