<?php
// delete_category.php


session_start();

require 'db_connect.php'; 
require 'check_access.php'; 

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

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
        error_log("Error deleting category: " . $e->getMessage()); 
        exit;
    }
} else {
    exit;
}
?>
