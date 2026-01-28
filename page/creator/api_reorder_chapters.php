<?php
require_once "../../assets/db.php";
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['success'=>false]); exit(); }

$data = json_decode(file_get_contents('php://input'), true);
$story_id = $data['story_id'];
$order = $data['order'];

$check = $pdo->prepare("SELECT id FROM stories WHERE id = ? AND user_id = ?");
$check->execute([$story_id, $_SESSION['user_id']]);
if (!$check->fetch()) { echo json_encode(['success'=>false]); exit(); }

$pdo->beginTransaction();
try {
    $sql = "UPDATE chapters SET chapter_number = ? WHERE id = ? AND story_id = ?";
    $stmt = $pdo->prepare($sql);
    foreach ($order as $idx => $chap_id) {
        $stmt->execute([$idx + 1, $chap_id, $story_id]);
    }
    $pdo->commit();
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}