<?php
require_once "../../assets/db.php";
header('Content-Type: application/json');
$id = $_POST['id'];

$stmt = $pdo->prepare("SELECT cover_image FROM stories WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$story = $stmt->fetch();

if ($story) {
    if ($story['cover_image'] !== 'default-cover.jpg' && file_exists("../../src/" . $story['cover_image'])) {
        unlink("../../src/" . $story['cover_image']);
    }
    $pdo->prepare("DELETE FROM stories WHERE id = ?")->execute([$id]);
    echo json_encode(['success'=>true]);
} else { echo json_encode(['success'=>false]); }