<?php
//delete_comment.php

session_start();

require 'db_connect.php'; 
require 'check_access.php'; 

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['comment_id'])) {
    $comment_id = $_GET['comment_id'];
    
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM comments WHERE comment_id = :comment_id");
        $stmt->execute([':comment_id' => $comment_id]);
    
        $pdo->commit();
        
        header("Location: list_comments.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollback();
        error_log("Error deleting comment: " . $e->getMessage()); // Log the exception message
        exit;
    }
} else {
    exit;
}
?>
