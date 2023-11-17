<?php
// create_category.php

session_start();

require 'db_connect.php';
require 'check_access.php';

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';
$name = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);

    if (empty($name)) {
        $error = 'Please enter a category name.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (:category_name)");
            $stmt->execute([':category_name' => $name]);

            $success = 'Category created successfully.';
            header("Location: list_categories.php");
            exit;
        } catch (Exception $e) {
            $error = 'Error creating the category: ' . $e->getMessage();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Category</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="create">
        <h1>Create New Category</h1>
        
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="create_category.php" method="post">
            <div>
                <label for="name">Category Name:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($name); ?>">
            </div>
            
            <div>
                <input type="submit" value="Create Category">
            </div>
        </form>
    </main>
</body>
</html>
</body>
</html>
