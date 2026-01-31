<?php
// Đường dẫn kết nối DB (Kiểm tra kỹ lại số lượng ../ cho đúng với cấu trúc thư mục của bạn)
// Giả định file này nằm ở: /fstory/page/me/func/post/post.php
require_once "../../../../assets/db.php"; 

header('Content-Type: application/json; charset=utf-8');

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Bạn cần đăng nhập để thực hiện thao tác này.']); 
    exit();
}

// 2. Lấy dữ liệu đầu vào
$action = $_POST['action'] ?? '';
$uId    = $_SESSION['user_id'];
$pId    = isset($_POST['id']) ? (int)$_POST['id'] : 0; // Ép kiểu int cho an toàn

try {
    // =================================================================
    // CASE 1: ĐĂNG BÀI VIẾT MỚI (QUAN TRỌNG: Phần này bạn đang thiếu)
    // =================================================================
    if ($action == 'post_status') {
        $content = trim($_POST['content'] ?? '');
        
        if (empty($content)) { 
            echo json_encode(['status' => false, 'message' => 'Nội dung không được để trống']); 
            exit; 
        }

        // a. Insert vào database
        $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, content) VALUES (?, ?)");
        $stmt->execute([$uId, $content]);
        $newId = $pdo->lastInsertId();

        // b. Lấy thông tin user để trả về HTML hiển thị ngay lập tức
        $stmtUser = $pdo->prepare("SELECT nickname, avatar FROM users WHERE id = ?");
        $stmtUser->execute([$uId]);
        $user = $stmtUser->fetch();
        
        $nickname   = htmlspecialchars($user['nickname']);
        $avatarPath = 'src/avt/' . ($user['avatar'] ?? 'default_avt.png');
        $safeContent = nl2br(htmlspecialchars($content));
        
        // c. Tạo cấu trúc HTML giống hệt vòng lặp bên ngoài để JS chèn vào (Prepend)
        // Lưu ý: Vì là bài vừa đăng xong, người dùng chắc chắn là chủ sở hữu -> Có menu Sửa/Xóa
        $html = '
        <div class="sidebar-card activity-item animate-new" id="activity-'.$newId.'" style="margin-bottom: 15px;">
            <div class="act-header" style="display:flex; justify-content: space-between; align-items: flex-start;">
                <div style="display:flex; gap:10px;">
                    <img src="'.$avatarPath.'" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                    <div>
                        <div style="font-weight:700;">'.$nickname.'</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">Vừa xong</div>
                    </div>
                </div>
                
                <div class="post-options">
                    <i class="fa-solid fa-ellipsis" onclick="toggleMenu('.$newId.')" style="padding: 0 10px;"></i>
                    <div class="dropdown-menu" id="menu-'.$newId.'">
                        <button class="dropdown-item" onclick="editPost('.$newId.')">
                            <i class="fa-solid fa-pen"></i> Chỉnh sửa
                        </button>
                        <button class="dropdown-item text-danger" onclick="deletePost('.$newId.')">
                            <i class="fa-solid fa-trash"></i> Xóa bài
                        </button>
                    </div>
                </div>
            </div>

            <div class="act-body" id="content-'.$newId.'" style="margin: 15px 0; line-height: 1.6;" data-original="'.htmlspecialchars($content).'">
                '.$safeContent.'
            </div>

            <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:var(--text-muted); margin-bottom: 5px;">
                <span id="like-count-'.$newId.'"></span>
                <span onclick="toggleComment('.$newId.')" style="cursor:pointer;">0 bình luận</span>
            </div>

            <div class="act-actions">
                <button class="act-btn" onclick="likePost('.$newId.', this)">
                    <i class="fa-regular fa-thumbs-up"></i> Thích
                </button>
                <button class="act-btn" onclick="toggleComment('.$newId.')">
                    <i class="fa-regular fa-comment"></i> Bình luận
                </button>
            </div>

            <div class="comment-section" id="cmt-section-'.$newId.'">
                <div class="comment-list" id="cmt-list-'.$newId.'" style="margin-bottom:15px; margin-top: 10px;"></div>
                <div class="comment-input-box">
                     <img src="'.$avatarPath.'" style="width:30px; height:30px; border-radius:50%; object-fit: cover;">
                     <textarea class="comment-input auto-expand" placeholder="Viết bình luận... (Enter để gửi)" onkeydown="submitComment(event, '.$newId.')"></textarea>
                </div>
            </div>
        </div>';

        echo json_encode(['status' => true, 'html' => $html]);
    }

    // =================================================================
    // CASE 2: XỬ LÝ LIKE / UNLIKE
    // =================================================================
    elseif ($action == 'toggle_like') {
        $stmt = $pdo->prepare("SELECT id FROM activity_likes WHERE user_id=? AND activity_id=?");
        $stmt->execute([$uId, $pId]);
        
        if ($stmt->fetch()) {
            // Đã like -> Xóa (Unlike)
            $pdo->prepare("DELETE FROM activity_likes WHERE user_id=? AND activity_id=?")->execute([$uId, $pId]);
        } else {
            // Chưa like -> Thêm (Like)
            $pdo->prepare("INSERT INTO activity_likes (user_id, activity_id) VALUES (?, ?)")->execute([$uId, $pId]);
        }
        
        // Đếm lại số like để cập nhật UI
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM activity_likes WHERE activity_id=?");
        $stmtCount->execute([$pId]);
        echo json_encode(['status' => true, 'new_count' => $stmtCount->fetchColumn()]);
    } 
    
    // =================================================================
    // CASE 3: XÓA BÀI VIẾT
    // =================================================================
    elseif ($action == 'delete_post') {
        // Chỉ xóa khi user_id khớp với người đang đăng nhập
        $stmt = $pdo->prepare("DELETE FROM user_activities WHERE id=? AND user_id=?");
        $stmt->execute([$pId, $uId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => true]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Bạn không có quyền xóa bài này hoặc bài không tồn tại.']);
        }
    }

    // =================================================================
    // CASE 4: SỬA BÀI VIẾT
    // =================================================================
    elseif ($action == 'edit_post') {
        $content = trim($_POST['content']);
        if (empty($content)) {
            echo json_encode(['status' => false, 'message' => 'Nội dung không được để trống']);
            exit;
        }
        
        $stmt = $pdo->prepare("UPDATE user_activities SET content=? WHERE id=? AND user_id=?");
        $stmt->execute([$content, $pId, $uId]);
        
        if ($stmt->rowCount() > 0 || $stmt->errorCode() == '00000') { // 00000 là thành công dù nội dung y hệt cũ
             echo json_encode(['status' => true]);
        } else {
             echo json_encode(['status' => false, 'message' => 'Lỗi khi cập nhật bài viết']);
        }
    }

    // =================================================================
    // CASE 5: ĐĂNG BÌNH LUẬN
    // =================================================================
// ... Các phần trước giữ nguyên ...

    // --- CASE MỚI: LẤY DANH SÁCH LIKE ---
    elseif ($action == 'get_likers') {
        $stmt = $pdo->prepare("SELECT u.nickname, u.avatar FROM activity_likes l JOIN users u ON l.user_id = u.id WHERE activity_id = ?");
        $stmt->execute([$pId]);
        $likers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => true, 'likers' => $likers]);
    }

    // --- CASE CẬP NHẬT: ĐĂNG BÌNH LUẬN (HỖ TRỢ TRẢ LỜI) ---
    elseif ($action == 'post_comment') {
        $content = trim($_POST['content']);
        $parentId = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null; // Lấy parent_id

        if ($content) {
            $stmt = $pdo->prepare("INSERT INTO activity_comments (user_id, activity_id, content, parent_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$uId, $pId, $content, $parentId]);
            
            $u = $pdo->prepare("SELECT nickname, avatar FROM users WHERE id=?");
            $u->execute([$uId]);
            $user = $u->fetch();
            
            echo json_encode([
                'status' => true, 
                'nickname' => htmlspecialchars($user['nickname']),
                'avatar' => 'src/avt/' . ($user['avatar'] ?? 'default_avt.png'),
                'content' => nl2br(htmlspecialchars($content))
            ]);
        }
    }
elseif ($action == 'delete_comment') {
        // Chỉ xóa comment của chính mình
        // Lưu ý: Nếu xóa comment cha, database (CASCADE) sẽ tự xóa các reply con.
        $stmt = $pdo->prepare("DELETE FROM activity_comments WHERE id=? AND user_id=?");
        $stmt->execute([$pId, $uId]);
        
        if ($stmt->rowCount() > 0) echo json_encode(['status' => true]);
        else echo json_encode(['status' => false, 'message' => 'Lỗi hoặc không có quyền xóa.']);
    }

    // --- CASE 6: SỬA BÌNH LUẬN ---
    elseif ($action == 'edit_comment') {
        $content = trim($_POST['content']);
        if ($content) {
            $stmt = $pdo->prepare("UPDATE activity_comments SET content=? WHERE id=? AND user_id=?");
            $stmt->execute([$content, $pId, $uId]);
            echo json_encode(['status' => true]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Nội dung trống']);
        }
    }
     else {
        // Trường hợp không tìm thấy action
        echo json_encode(['status' => false, 'message' => 'Invalid Action']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>