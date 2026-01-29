<?php
// 1. THIẾT LẬP GIÁ TRỊ MẶC ĐỊNH & HỖ TRỢ TÙY CHỈNH TỪ TRANG KHÁC
// Nếu trang gọi file này có đặt biến $page_title, dùng nó. Nếu không, dùng "FStory".
$meta_title = isset($page_title) ? $page_title . "" : "FStory";

// Nếu trang gọi file này có đặt biến $page_desc, dùng nó. Nếu không, dùng mô tả mặc định.
$meta_desc = isset($page_desc) ? $page_desc : "Đọc truyện online miễn phí, cập nhật liên tục. Nền tảng sáng tác truyện chữ chuyên nghiệp dành cho tác giả Việt.";

$meta_image = "http://" . $_SERVER['HTTP_HOST'] . "/fstory/assets/images/default-share-banner.jpg"; // Ảnh mặc định
$meta_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$meta_type = "website";

// 2. XỬ LÝ NẾU ĐANG Ở TRANG CHI TIẾT TRUYỆN (detail.php)
// (Logic này giữ nguyên, nó sẽ ghi đè $page_title nếu đang xem truyện)
if (isset($story) && !empty($story['title'])) {
    $meta_title = htmlspecialchars($story['title']) . " | FStory";
    // Lấy 150 ký tự đầu của mô tả
    $clean_desc = strip_tags($story['description']);
    $meta_desc = mb_substr($clean_desc, 0, 150) . "..."; 
    // Đường dẫn ảnh bìa tuyệt đối
    $meta_image = "http://" . $_SERVER['HTTP_HOST'] . "/fstory/src/" . $story['cover_image'];
    $meta_type = "book";
}

// 3. XỬ LÝ NẾU ĐANG Ở TRANG ĐỌC (reading.php)
// (Logic này giữ nguyên, ưu tiên cao nhất)
if (isset($chapter) && isset($story)) {
    $meta_title = "Chương " . $chapter['chapter_number'] . ": " . htmlspecialchars($chapter['title']) . " - " . htmlspecialchars($story['title']);
    $meta_desc = "Đọc chương " . $chapter['chapter_number'] . " của truyện " . htmlspecialchars($story['title']) . " tại FStory.";
    // Vẫn dùng ảnh bìa truyện
    $meta_image = "http://" . $_SERVER['HTTP_HOST'] . "/fstory/src/" . (isset($story['cover_image']) ? $story['cover_image'] : 'default-cover.jpg');
    $meta_type = "article";
}
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="theme-color" content="#6366f1"> 
<title><?php echo $meta_title; ?></title>
<meta name="description" content="<?php echo $meta_desc; ?>">
<meta name="author" content="FStory Team">
<meta name="generator" content="FStory Platform">
<link rel="canonical" href="<?php echo $meta_url; ?>">

<meta property="og:site_name" content="FStory Studio">
<meta property="og:type" content="<?php echo $meta_type; ?>">
<meta property="og:url" content="<?php echo $meta_url; ?>">
<meta property="og:title" content="<?php echo $meta_title; ?>">
<meta property="og:description" content="<?php echo $meta_desc; ?>">
<meta property="og:image" content="<?php echo $meta_image; ?>">
<meta property="og:image:width" content="600">
<meta property="og:image:height" content="800">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $meta_title; ?>">
<meta name="twitter:description" content="<?php echo $meta_desc; ?>">
<meta name="twitter:image" content="<?php echo $meta_image; ?>">

<link rel="icon" type="image/png" href="/fstory/assets/img/fstory_logo.png">
<link rel="apple-touch-icon" href="/fstory/assets/img/fstory_logo.png">