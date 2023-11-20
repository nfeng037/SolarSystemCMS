<?php
// view.php

session_start();
require 'db_connect.php';
require_once 'check_access.php';

$error = $_SESSION['error'] ?? '';
$commentContent = ''; 
$page_id = filter_input(INPUT_GET, 'page_id', FILTER_SANITIZE_NUMBER_INT) ?: ($_POST['page_id'] ?? null);

$isAdmin = isset($_SESSION['user_id']) && checkUserRole('admin');

$pageData = null;
$comments = [];

// Fetch page data and comments, this should be done regardless of POST or GET
if ($page_id) {
    // Fetch page data
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE page_id = ?");
    if ($stmt->execute([$page_id])) {
        $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pageData) {
            $error = "Page not found.";
        }
    } else {
        $error = "Error executing query.";
    }

    // Fetch comments (including guest name if available)
    if (!$error) {
        $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments LEFT JOIN users ON comments.user_id = users.user_id WHERE comments.page_id = :page_id ORDER BY comments.creation_time DESC");
        $stmt->bindParam(':page_id', $page_id, PDO::PARAM_INT);
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    $error = "Invalid page ID.";
}

// Process POST request for adding a comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page_id && isset($_POST['comment_content'])) {
    $commentContent = trim($_POST['comment_content']);

    if ($commentContent) {
        $user_id = $_SESSION['user_id'] ?? null;
        $guest_name = isset($_POST['comment_name']) ? trim($_POST['comment_name']) : null;

        if (isset($_POST['captcha']) && $_POST['captcha'] == $_SESSION['captcha']) {
            $stmt = $pdo->prepare("INSERT INTO comments (user_id, page_id, content, guest_name) VALUES (:user_id, :page_id, :content, :guest_name)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':page_id', $page_id, PDO::PARAM_INT);
            $stmt->bindParam(':content', $commentContent, PDO::PARAM_STR);
            $stmt->bindParam(':guest_name', $guest_name, PDO::PARAM_STR);

            try {
                $stmt->execute();
                $commentContent = ''; 

                header("Location: view.php?page_id=" . $page_id . "#comments-display");
                exit();
            } catch (PDOException $e) {
                $error = "Error adding the comment: " . $e->getMessage();
            }
        } else {
            $error = "Incorrect CAPTCHA.";
        }
    } else {
        $error = "Missing required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageData['title'] ?? 'Page Not Found'); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="user view">
    <?php include 'home_navbar.php'; ?>

    <main>
        <?php if ($pageData): ?>
            <article>
                <header>
                    <h1><?= htmlspecialchars($pageData['title']); ?></h1>
                </header>

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
                <header>
                    <h2>Leave a Comment</h2>
                </header>

                <form action="view.php?page_id=<?= $page_id; ?>" method="post">
                    <input type="hidden" name="page_id" value="<?= $page_id; ?>">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <input type="text" name="comment_name" placeholder="Your name" value="<?= htmlspecialchars($_POST['comment_name'] ?? ''); ?>" required>
                    <?php endif; ?>
                    <textarea name="comment_content" placeholder="Enter your comments..." required><?= htmlspecialchars($commentContent); ?></textarea>
                    <img src="captcha_generator.php" alt="CAPTCHA" />
                    <input type="text" name="captcha" placeholder="Enter CAPTCHA" required>
                    <button type="submit">Submit Comment</button>

                    <?php if (!empty($error)): ?>
                        <p class="error"><?= htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                </form>
            </section>

            <section class="comments-display" id="comments-display">
                <?php foreach ($comments as $comment): ?>
                    <div class='comment'>
                        <div class="comment-header">
                            <?php if (isset($comment['guest_name'])): ?>
                                <strong><?= htmlspecialchars($comment['guest_name']); ?></strong>
                            <?php elseif (isset($comment['username'])): ?>
                                <strong><?= htmlspecialchars($comment['username']); ?></strong>
                            <?php endif; ?>
                            <p><?= date('Y-m-d H:i:s', strtotime($comment['creation_time'])); ?></p>
                        </div>
                        <?= htmlspecialchars($comment['content']); ?>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <script type="text/javascript">
        window.onload = function() {
            if (window.location.hash && window.location.hash === '#comments-display') {
                var commentsSection = document.getElementById('comments-display');
                if (commentsSection) {
                    commentsSection.scrollIntoView();
                }
            }
            var error = "<?= $error; ?>";
            var captchaError = error.includes("CAPTCHA");
            
            if (captchaError) {
                document.getElementsByName("captcha")[0].focus();
            }
        };
    </script>
</body>
</html>
