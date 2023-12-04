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
$comment_id = filter_input(INPUT_GET, 'comment_id', FILTER_VALIDATE_INT); // Validate comment_id
$pageTitle = "Edit Comment"; 

if (!$comment_id) {
    $error = 'Invalid comment id.';
}


if ($comment_id) {
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE comment_id = :comment_id");
    $stmt->execute([':comment_id' => $comment_id]);
    $comment = $stmt->fetch();

    if ($comment) {
        $guest_name = $comment['guest_name'];
        $content = $comment['content'];
    } else {
        $error = 'Comment not found.';
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

<?php include 'header.php'; ?>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container create">

        <?php if ($error): ?>
        <p class="alert alert-danger" role="alert"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
        <p class="text-success mt-2"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="edit_comment.php?comment_id=<?= htmlspecialchars($comment_id); ?>" method="post">
            <div>
                <label for="name">Guest Name:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($guest_name); ?>">
            </div>

            <div>
                <label for="name">Content:</label>
                <textarea class="form-control" name="comment_content"
                    required><?= htmlspecialchars($content) ?></textarea>
            </div>

            <div>
                <input type="submit" value="Update comment" class="btn btn-primary mb-2 mt-2">
            </div>
        </form>
    </main>
    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>