<?php 
    require_once "assets/db.php"; 
?>
<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FStory</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!--Add assets-->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php 
        include "view/header.php";
        include "view/creator_main.php";
        include "view/footer.php";
        include "view/nav_phone.php";
    ?>




    <!--Script-->
    <script src="assets/js/system_display.js"></script>
    <script src="assets/js/get_nav.js"></script>
</body>
</html>