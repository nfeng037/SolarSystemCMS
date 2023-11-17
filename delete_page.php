<?php
// delete_page.php

session_start();

require 'db_connect.php'; 
require 'check_access.php'; 

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

// Check if page_id is present in the query string
if (isset($_GET['page_id'])) {
    $page_id = $_GET['page_id'];
    
    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Delete related records from images tables
        $stmt = $pdo->prepare("DELETE FROM images WHERE page_id = :page_id");
        $stmt->execute([':page_id' => $page_id]);

        // Prepare the delete statement for the pages table
        $stmt = $pdo->prepare("DELETE FROM pages WHERE page_id = :page_id");
        $stmt->execute([':page_id' => $page_id]);
        
        // Commit the transaction
        $pdo->commit();
        
        header("Location: list_pages.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollback();
        error_log("Error deleting page: " . $e->getMessage()); // Log the exception message
        header("Location: error_page.php?error=" . urlencode("An error occurred while deleting the page."));
        exit;
    }
} else {
    header("Location: error_page.php?error=" . urlencode("No page ID specified."));
    exit;
}
?>
