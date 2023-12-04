<?php
// index.php

session_start();

require 'db_connect.php'; 
require 'check_access.php'; 

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Dashboard"; 
$newsCount = $galleryCount = $commentCount = 0;

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM pages");
    $galleryCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM comments");
    $commentCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $categoriesCount = $stmt->fetchColumn();

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "An error occurred while fetching data.";
}

?>

<!DOCTYPE html>
<html lang="en">

<?php include 'header.php'; ?>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container">
        <section class="card">
            <h2>Pages</h2>
            <div>
                <p>Total: <?= $galleryCount; ?></p>
                <a class="btn btn-primary" href="list_pages.php" role="button">Manage</a>
            </div>
        </section>

        <section class="card">
            <h2>Categories</h2>
            <div>
                <p>Total: <?= $categoriesCount; ?></p>
                <a class="btn btn-primary" href="list_categories.php" role="button">Manage</a>
            </div>
        </section>

        <section class="card">
            <h2>Comments</h2>
            <div>
                <p>Total: <?= $commentCount; ?></p>
                <a class="btn btn-primary" href="list_comments.php" role="button">Manage</a>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>