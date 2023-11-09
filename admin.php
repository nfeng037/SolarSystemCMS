<?php
// admin.php

session_start();

require 'db_connect.php'; // Use your database connection script
require 'check_access.php'; // Use your user role checking script

// Redirect user to login page if they're not logged in or if they're not an admin
if (!isset($_SESSION['user_id']) || !checkUserRole('admin', $_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize counts
$newsCount = $galleryCount = $commentCount = 0;

try {
    // Get the count of gallery items
    $stmt = $pdo->query("SELECT COUNT(*) FROM pages");
    $galleryCount = $stmt->fetchColumn();

    // Get the count of comments
    $stmt = $pdo->query("SELECT COUNT(*) FROM comments");
    $commentCount = $stmt->fetchColumn();

} catch (PDOException $e) {
    // Handle exceptions by logging and displaying an error message
    error_log("Database error: " . $e->getMessage());
    $error = "An error occurred while fetching data.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main>
        <h1>Admin Dashboard</h1>

        <div class="admin-statistics">

            <div class="statistic">
                <a href="list_celestial_bodies.php">Celestial Bodies: <?php echo $galleryCount; ?></a>
            </div>

            <div class="statistic">
                <a href="list_comments.php">Comments: <?php echo $commentCount; ?></a>
            </div>
        </div>

        <div class="admin-links">
            <a href="create_page.php">Create New</a>
            <!-- Add more links as needed for other creation pages -->
        </div>
    </main>

    <footer>
        <!-- Footer content -->
    </footer>
</body>
</html>