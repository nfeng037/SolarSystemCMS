<?php 

// list_comments.php
require 'db_connect.php';
require 'check_access.php';

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM comments ORDER BY creation_time DESC");
    $stmt->execute();
    
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Comments</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <main class="main-content">
        <h1>Manage Comments</h1>
        
        <?php if (!empty($comments)): ?>
            <table class="pages-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Creation Time</th>
                        <th>Name</th>
                        <th>Content</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $index => $comment): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= $comment['creation_time']; ?></a></td>
                            <td><?= $comment['guest_name']; ?></a></td>
                            <td><?= $comment['content']; ?></a></td>
                            <td>
                                <a href="edit_comment.php?comment_id=<?= $comment['comment_id']; ?>" class="edit-btn">Edit</a>
                                <a href="delete_comment.php?comment_id=<?= $comment['comment_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No comment found.</p>
        <?php endif; ?>
    </main>
</body>
</html>