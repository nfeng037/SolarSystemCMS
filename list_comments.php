<?php 

// list_comments.php
require 'db_connect.php';
require 'check_access.php';

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Comments"; 

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

<?php include 'header.php'; ?>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container create">   
        <?php if (!empty($comments)): ?>
            <table class="table table-hover">
                <thead class="table-dark">
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
                                <a href="edit_comment.php?comment_id=<?= $comment['comment_id']; ?>" class="btn btn-success" role="button">Edit</a>
                                <a href="delete_comment.php?comment_id=<?= $comment['comment_id']; ?>" class="btn btn-danger" role="button" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-danger mt-5" role="alert">No comments found.</p>
        <?php endif; ?>
    </main>
    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>
</html>