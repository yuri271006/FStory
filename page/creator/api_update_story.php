<?php
require_once "../../assets/db.php";
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) exit();

$id = $_POST['id'];
$title = trim($_POST['title']);
$desc = trim($_POST['description']);
$user_id = $_SESSION['user_id'];

if (isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] === 0) {
    $stmt = $pdo->prepare("SELECT cover_image FROM stories WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $old_img = $stmt->fetchColumn();

    $ext = pathinfo($_FILES['cover_file']['name'], PATHINFO_EXTENSION);
    $new_name = "u" . $user_id . "_" . time() . "." . $ext;
    if (move_uploaded_file($_FILES['cover_file']['tmp_name'], "../../src/" . $new_name)) {
        if ($old_img && $old_img !== 'default-cover.jpg') unlink("../../src/" . $old_img);
        $pdo->prepare("UPDATE stories SET cover_image = ? WHERE id = ?")->execute([$new_name, $id]);
    }
}

$pdo->prepare("UPDATE stories SET title = ?, description = ? WHERE id = ? AND user_id = ?")->execute([$title, $desc, $id, $user_id]);
echo json_encode(['success'=>true]);