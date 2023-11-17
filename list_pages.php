<?php
// list_pages.php

session_start();

require 'db_connect.php';

// Initialize an array to hold the pages data
$pages = [];

try {
    // Prepare the SQL statement to fetch data from 'pages' table
    // Join with 'images' table to get the image file names
    $stmt = $pdo->prepare("SELECT * FROM pages");
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
    <main class="main-content">
        <h1>Pages List</h1>
        
        <?php if (!empty($pages)): ?>
            <table class="pages-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Creation Time</th>
                        <th>Last Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $index => $page): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($page['title']); ?></td>
                            <td><?= $page['content']; ?></td>
                            <td><?= htmlspecialchars($page['creation_time']); ?></td>
                            <td><?= htmlspecialchars($page['last_modified_time']); ?></td>
                            <td>
                                <a href="edit_page.php?page_id=<?= $page['page_id']; ?>" class="edit-btn">Edit</a>
                                <a href="delete_page.php?page_id=<?= $page['page_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No celestial bodies found.</p>
        <?php endif; ?>
    </main>
</body>
</html>
