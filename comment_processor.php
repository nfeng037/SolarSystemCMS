<?php
// comment_processor.php

session_start();
require 'db_connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'], $_POST['page_id'], $_POST['comment_content'])) {
        $user_id = $_SESSION['user_id'];
        $page_id = filter_input(INPUT_POST, 'page_id', FILTER_SANITIZE_NUMBER_INT);
        $comment_content = filter_input(INPUT_POST, 'comment_content', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($page_id && $comment_content) {
            $stmt = $pdo->prepare("INSERT INTO comments (user_id, page_id, content) VALUES (?, ?, ?)");
            if ($stmt->execute([$user_id, $page_id, $comment_content])) {
                header('Location: view.php?page_id=' . $page_id);
                exit;
            } else {
                $error = "Error adding the comment.";
            }
        } else {
            $error = "Invalid comment data.";
        }
    } else {
        $error = "Invalid request.";
    }
} else {
    $error = "Invalid request method.";
}

$_SESSION['error'] = $error;
header('Location: view.php?page_id=' . ($_POST['page_id'] ?? ''));
exit;
?>
