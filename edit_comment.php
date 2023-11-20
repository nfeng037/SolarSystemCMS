<?php
// edit_comment.php
require 'db_connect.php';
require 'check_access.php';

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';
$comment_id = isset($_GET['comment_id']) ? $_GET['comment_id'] : null;

if ($comment_id) {
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE comment_id = :comment_id");
    $stmt->execute([':comment_id' => $comment_id]);
    $comment = $stmt->fetch();

    if ($comment) {
        $guest_name = $comment['guest_name'];
        $content = $comment['content'];
    } else {
        $error = 'comment not found.';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $comment_id) {
    $guest_name = trim($_POST['name']);
    $content = trim($_POST['comment_content']);

    if (empty($guest_name) || empty($content)) {
        $error = 'Please enter a guest name.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE comments SET guest_name = :guest_name, content = :content WHERE comment_id = :comment_id");
            $stmt->execute([
                ':guest_name' => $guest_name,
                ':comment_id' => $comment_id,
                ':content' => $content 
            ]);

            $success = 'comment updated successfully.';
            header("Location: list_comments.php");
            exit;
        } catch (Exception $e) {
            $error = 'Error updating the comment: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Comment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
            <?php include 'navbar.php'; ?>
    </header>
    <main class="edit">
        <h1>Edit Comment</h1>
        
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form class="edit_form" action="edit_comment.php?comment_id=<?= htmlspecialchars($comment_id); ?>" method="post">
            <div>
                <label for="name">Guest Name:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($guest_name); ?>">
            </div>

            <div>
                <label for="name">Content:</label>
                <textarea class="edit_textarea" name="comment_content" required><?= htmlspecialchars($content) ?></textarea>            
            </div>
            
            <div>
                <input type="submit" value="Update comment">
            </div>
        </form>
    </main>
</body>
</html>