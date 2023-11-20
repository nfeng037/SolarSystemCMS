<?php
// category_pages.php

session_start();

require 'db_connect.php';
require 'check_access.php'; 


$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$pages = [];
$category_name = '';

try {
    $categoryStmt = $pdo->prepare("SELECT category_name FROM categories WHERE category_id = :category_id");
    $categoryStmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $categoryStmt->execute();
    $categoryResult = $categoryStmt->fetch(PDO::FETCH_ASSOC);

    if ($categoryResult) {
        $category_name = $categoryResult['category_name'];
    }

    $stmt = $pdo->prepare("SELECT * FROM pages WHERE category_id = :category_id");
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($category_name) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="user">
    <?php include 'home_navbar.php'; ?>
    <main class="main-content">
        <section class="gallery">
            <h1><?= htmlspecialchars($category_name) ?></h1>
            <div class="article-container">
                <?php if (!empty($pages)): ?>
                    <?php 
                            foreach ($pages as $page):
                                $content = $page['content'];
                                $isLong = mb_strlen($content) > 150; 
                        ?>
                            <article>
                                <a href="view.php?page_id=<?= $page['page_id']; ?>"> 
                                <img src="<?= htmlspecialchars($page['image_url']); ?>" alt="<?= htmlspecialchars($page['title']); ?>">
                                </a>
                                <h3><?= htmlspecialchars($page['title']); ?></h3>
                                <p>
                                    <?= mb_substr($content, 0, 150); ?> 
                                    <?php if ($isLong): ?>
                                        ... <a href="view.php?page_id=<?= $page['page_id']; ?>">read more</a> 
                                    <?php endif; ?>
                                </p>
                            </article>
                        <?php 
                            endforeach; 
                        ?>
                <?php else: ?>
                    <p class="no-data">No pages found for this category.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
