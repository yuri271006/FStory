<?php
require_once "assets/db.php";
?>
<!DOCTYPE html>
<html lang="vi" data-theme="light">

<head>
    <?php
    $chapter_meta = $_GET['id'] ?? 0;
    $stmtt = $pdo->prepare("SELECT * FROM chapters WHERE id = ?");
    $stmtt->execute([$chapter_meta]);
    $chapter_rmeta = $stmtt->fetch();
    $storyStmtt = $pdo->prepare("SELECT id, title, slug FROM stories WHERE id = ?");
    $storyStmtt->execute([$chapter_rmeta['story_id']]);
    $story_rmeta = $storyStmtt->fetch();
    include "view/meta_tag.php";
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!--Add assets-->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/reading.css">
</head>

<body>

    <?php
    include "view/reading/header.php";
    include "view/reading/main.php";
    ?>


    <!--Script-->
    <script src="assets/js/system_display.js"></script>
</body>

</html>