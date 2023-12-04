<?php
// view.php

session_start();
require 'db_connect.php';
require_once 'check_access.php';

$error = $_SESSION['error'] ?? '';
$commentContent = ''; 
$page_id = filter_input(INPUT_GET, 'page_id', FILTER_VALIDATE_INT) ?: null; // Validate page_id

$isAdmin = isset($_SESSION['user_id']) && checkUserRole('admin');

$pageData = null;
$comments = [];

if ($page_id) {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE page_id = ?");
    if ($stmt->execute([$page_id])) {
        $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pageData) {
            $error = "Page not found.";
        }
    } else {
        $error = "Error executing query.";
    }

    if (!$error) {
        $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments LEFT JOIN users ON comments.user_id = users.user_id WHERE comments.page_id = :page_id ORDER BY comments.creation_time DESC");
        $stmt->bindParam(':page_id', $page_id, PDO::PARAM_INT);
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    $error = "Invalid page ID.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page_id) {
    $commentContent = filter_input(INPUT_POST, 'comment_content', FILTER_SANITIZE_STRING);
    $guest_name = filter_input(INPUT_POST, 'comment_name', FILTER_SANITIZE_STRING) ?: 'Anonymous';
    $captchaInput = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_STRING);

    if ($commentContent) {
        $user_id = $_SESSION['user_id'] ?? null;

        if (strtolower($captchaInput) == strtolower($_SESSION['captcha'])) {
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

$pageTitle = htmlspecialchars($pageData['title'] ?? 'Page Not Found');

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>

<body class="p-3 w-75 mx-auto border-0 bd-example m-0 border-0 bg-black">
    <header>
        <?php include 'home_navbar.php'; ?>
    </header>
    <main>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($pageData): ?>
        <article>

            <div class="row">
                <h1 class="h1 text-white m-2"><?= htmlspecialchars($pageData['title']); ?></h1>

                <?php if ($isAdmin): ?>
                <div class="page-actions m-2">
                    <a href="edit_page.php?page_id=<?= $page_id; ?>" class="btn btn-success mr-2" role="button">Edit</a>
                    <a href="delete_page.php?page_id=<?= $page_id; ?>" class="btn btn-danger" role="button"
                        onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                </div>
                <?php endif; ?>
            </div>

            <div class="row mt-2 mb-5">
                <div class="col-md-4">
                    <img src="<?= htmlspecialchars($pageData['image_url']); ?>"
                        alt="Image for <?= htmlspecialchars($pageData['title']); ?>" class="img-fluid rounded-5">
                </div>
                <div class="col-md-8">

                    <div data-bs-spy="scroll" data-bs-smooth-scroll="true" tabindex="0"
                        class="scrollspy-example-2 bg-body-black text-white p-2 m-3">
                        <?= $pageData['content']; ?>
                    </div>
                </div>
            </div>

        </article>

        <div class="comments">
            <header>
                <h2 class="text-white">Leave a Comment</h2>
            </header>

            <form action="view.php?page_id=<?= $page_id; ?>" method="post">
                <input type="hidden" name="page_id" value="<?= $page_id; ?>">
                <?php if (!isset($_SESSION['user_id'])): ?>
                <input type="text" name="comment_name" placeholder="Your name"
                    value="<?= htmlspecialchars($_POST['comment_name'] ?? ''); ?>">
                <?php endif; ?>
                <textarea name="comment_content" placeholder="Enter your comments..."
                    required><?= htmlspecialchars($commentContent); ?></textarea>
                <img src="captcha_generator.php" alt="CAPTCHA">
                <input type="text" name="captcha" placeholder="Enter CAPTCHA" required>
                <button type="submit" class="btn btn-success">Submit Comment</button>

                <?php if (!empty($error)): ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
                <?php endif; ?>
            </form>
        </div>

        <div class="comments-display" id="comments-display">
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
        </div>
        <?php endif; ?>
    </main>

    <script>
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

    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>