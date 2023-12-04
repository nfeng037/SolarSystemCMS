<?php
// delete_page.php

session_start();

require 'db_connect.php'; 
require 'check_access.php'; 

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['page_id'])) {
    $page_id = $_GET['page_id'];
    
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM images WHERE page_id = :page_id");
        $stmt->execute([':page_id' => $page_id]);

        $stmt = $pdo->prepare("DELETE FROM pages WHERE page_id = :page_id");
        $stmt->execute([':page_id' => $page_id]);
        
        $pdo->commit();
        
        header("Location: list_pages.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollback();
        error_log("Error deleting page: " . $e->getMessage()); 
        header("Location: error_page.php?error=" . urlencode("An error occurred while deleting the page."));
        exit;
    }
} else {
    header("Location: error_page.php?error=" . urlencode("No page ID specified."));
    exit;
}
?>
