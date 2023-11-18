<?php
// view.php

session_start();
require 'db_connect.php';
require_once 'check_access.php';

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']); 

$page_id = filter_input(INPUT_GET, 'page_id', FILTER_SANITIZE_NUMBER_INT);
$isAdmin = isset($_SESSION['user_id']) && checkUserRole('admin');
$pageData = null;

if ($page_id) {
    $stmt = $pdo->prepare("SELECT title, content, image_url FROM pages WHERE page_id = ?");
    if ($stmt->execute([$page_id])) {
        $pageData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pageData) {
            $error = "Page not found.";
        }
    } else {
        $error = "Error executing query.";
    }
} else {
    $error = "Invalid page ID.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageData['title'] ?? 'Page Not Found'); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="user">
    <?php include 'navbar.php'; ?>
    <main class="view">
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php elseif ($pageData): ?>
            <article>
                <h1><?= htmlspecialchars($pageData['title']); ?></h1>
                <?php if ($isAdmin): ?>
                    <section class="page-actions">
                        <a href="edit_page.php?page_id=<?= $page_id; ?>">Edit</a>
                        <a href="delete_page.php?page_id=<?= $page_id; ?>" onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                    </section>
                <?php endif; ?>
                <?php if (!empty($pageData['image_url'])): ?>
                    <img src="<?= htmlspecialchars($pageData['image_url']); ?>" alt="Image for <?= htmlspecialchars($pageData['title']); ?>">
                <?php endif; ?>
                <section>
                    <?= $pageData['content']; ?>
                </section>
            </article>
            <section class="comments">
                <h2>Leave a Comment</h2>
                <form action="comment_processor.php" method="post">
                    <input type="hidden" name="page_id" value="<?= $page_id; ?>">
                    <textarea name="comment_content" required></textarea>
                    <button type="submit">Submit Comment</button>
                </form>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
