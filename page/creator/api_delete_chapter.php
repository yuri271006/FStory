<?php
require_once "../../assets/db.php";
header('Content-Type: application/json');
$id = $_POST['id'];

$sql = "SELECT c.id FROM chapters c JOIN stories s ON c.story_id = s.id WHERE c.id = ? AND s.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id, $_SESSION['user_id']]);

if ($stmt->fetch()) {
    $pdo->prepare("DELETE FROM chapters WHERE id = ?")->execute([$id]);
    echo json_encode(['success'=>true]);
} else { echo json_encode(['success'=>false]); }