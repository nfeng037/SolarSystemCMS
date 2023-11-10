<?php
// list_pages.php

session_start();

require 'db_connect.php';

// Initialize an array to hold the pages data
$pages = [];

try {
    // Prepare the SQL statement to fetch data from 'pages' table
    // Join with 'images' table to get the image file names
    $stmt = $pdo->prepare("SELECT p.page_id, p.title, p.content, i.file_name FROM pages p LEFT JOIN images i ON p.page_id = i.page_id");
    $stmt->execute();
    
    // Fetch all pages records
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pages List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <main>
        <h1>Pages List</h1>
        
        <?php if (!empty($pages)): ?>
            <div class="pages-list">
                <?php foreach ($pages as $page): ?>
                    <div class="page-body">
                        <h2><?= htmlspecialchars($page['title']); ?></h2>
                        <p><?= $page['content']; ?></p>
                        <?php if (!empty($page['file_name'])): ?>
                            <img src="<?= htmlspecialchars($page['file_name']); ?>" alt="Image of <?= htmlspecialchars($page['title']); ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No celestial bodies found.</p>
        <?php endif; ?>
    </main>
</body>
</html>
