<?php
// list_categories.php

session_start();

require 'db_connect.php';
require 'check_access.php'; 

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM categories");
    $stmt->execute();
    
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <main class="main-content">
        <h1>Categories List</h1>
        <div class="admin-links">
                <a href="create_category.php">Create New</a>
        </div>
        
        <?php if (!empty($categories)): ?>
            <table class="pages-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $index => $category): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= $category['category_name']; ?></a></td>
                            <td>
                                <a href="edit_category.php?category_id=<?= $category['category_id']; ?>" class="edit-btn">Edit</a>
                                <a href="delete_category.php?category_id=<?= $category['category_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No categories found.</p>
        <?php endif; ?>
    </main>
</body>
</html>