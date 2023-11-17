<?php
// delete_category.php


session_start();

require 'db_connect.php'; 
require 'check_access.php'; 

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

// Check if page_id is present in the query string
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = :category_id");
        $stmt->execute([':category_id' => $category_id]);
    
        $pdo->commit();
        
        header("Location: list_categories.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollback();
        error_log("Error deleting category: " . $e->getMessage()); // Log the exception message
        header("Location: error_category.php?error=" . urlencode("An error occurred while deleting the category."));
        exit;
    }
} else {
    header("Location: error_category.php?error=" . urlencode("No category ID specified."));
    exit;
}
?>
