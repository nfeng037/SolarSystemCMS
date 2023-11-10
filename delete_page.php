<?php
// delete_page.php

session_start();

require 'db_connect.php'; 
require 'check_access.php'; 

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    // Redirect to the login page or error page
    header("Location: login.php");
    exit;
}

// Check if news_id is present in the query string
if (isset($_GET['page_id'])) {
    $page_id = $_GET['page_id'];
    
    try {
        // Begin a transaction
        $pdo->beginTransaction();
        

        // Now, prepare the delete statement for the pages table
        $stmt = $pdo->prepare("DELETE FROM pages WHERE page_id = :page_id");
        $stmt->bindParam(':page_id', $page_id, PDO::PARAM_INT);
        
        // Execute the delete statement for pages
        $stmt->execute();
        
        // Commit the transaction
        $pdo->commit();
        
        // Redirect to the news list page after deletion
        header("Location: index.php");
        exit;
        
    } catch (PDOException $e) {
        // Roll back the transaction in case of error
        $pdo->rollback();
        // Log the exception message
        // Redirect to an error page or show an error message
        header("Location: error_page.php?error=" . urlencode("An error occurred while deleting the page."));
        exit;
    }
} else {
    // Redirect to an error page or show an error message
    header("Location: error_page.php?error=" . urlencode("No page ID specified."));
    exit;
}

?>