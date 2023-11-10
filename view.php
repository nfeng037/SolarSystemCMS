<?php
// view.php

session_start();

require 'db_connect.php'; 
require 'check_access.php';

// Initialize variables
$pageData = null;
$error = '';
$isAdmin = checkUserRole('admin');

// Check if a page_id is provided and it's a valid integer
if (isset($_GET['page_id']) && filter_var($_GET['page_id'], FILTER_VALIDATE_INT)) {
    $page_id = $_GET['page_id'];
    
    // Prepare a SELECT statement to fetch the page data including the image URL
    $stmt = $pdo->prepare("SELECT title, content, image_url FROM pages WHERE page_id = :page_id");
    
    // Bind the page_id parameter
    $stmt->bindParam(':page_id', $page_id, PDO::PARAM_INT);
    
    try {
        // Execute the query
        $stmt->execute();
        
        // Fetch the page data
        $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if the page entry was found
        if (!$pageData) {
            $error = "Page not found.";
        }
    } catch (PDOException $e) {
        $error = "Error fetching the page: " . $e->getMessage();
    }
} else {
    $error = "No page ID provided or invalid ID.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageData['title'] ?? 'Page Not Found'); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <main>
        <?php if ($pageData): ?>
        <article>
            <h1><?= htmlspecialchars($pageData['title']); ?></h1>
            <?php if ($isAdmin): ?>
                <section class="page-actions">
                    <a href="edit_page.php?page_id=<?= $page_id; ?>">Edit</a>
                    <a href="delete_page.php?page_id=<?= $page_id; ?>" onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                </section>
            <?php endif; ?>
            <?php if (!empty($pageData['image_url'])): ?>
                <!-- Display the image using the image_url from the pages table -->
                <img src="<?= htmlspecialchars($pageData['image_url']); ?>" alt="Image for <?= htmlspecialchars($pageData['title']); ?>">
            <?php endif; ?>
            <section>
                <?= $pageData['content']; ?>
            </section>
        </article>
        <?php else: ?>
            <p><?= $error; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
