<?php
require_once "../../assets/db.php";
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['success'=>false, 'message'=>'Login required']); exit(); }

$story_id = $_POST['story_id'];
$chapter_id = $_POST['chapter_id'] ?? 0;
$chap_num = $_POST['chapter_number'];
$title = trim($_POST['title']);
$content = $_POST['content'];

$check = $pdo->prepare("SELECT id FROM stories WHERE id = ? AND user_id = ?");
$check->execute([$story_id, $_SESSION['user_id']]);
if (!$check->fetch()) { echo json_encode(['success'=>false, 'message'=>'No permission']); exit(); }

try {
    if ($chapter_id > 0) {
        $pdo->prepare("UPDATE chapters SET chapter_number=?, title=?, content=? WHERE id=?")->execute([$chap_num, $title, $content, $chapter_id]);
    } else {
        $pdo->prepare("INSERT INTO chapters (story_id, chapter_number, title, content) VALUES (?,?,?,?)")->execute([$story_id, $chap_num, $title, $content]);
    }
    echo json_encode(['success'=>true]);
} catch (PDOException $e) { echo json_encode(['success'=>false, 'message'=>$e->getMessage()]); }