<?php
// edit_category.php

session_start();
require 'db_connect.php';
require 'check_access.php'; 


if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$category_name = '';

if ($category_id) {
    $stmt = $pdo->prepare("SELECT category_name FROM categories WHERE category_id = :category_id");
    $stmt->execute([':category_id' => $category_id]);
    $category = $stmt->fetch();

    if ($category) {
        $category_name = $category['category_name'];
    } else {
        $error = 'Category not found.';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $category_id) {
    $category_name = trim($_POST['name']);

    if (empty($category_name)) {
        $error = 'Please enter a category name.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE categories SET category_name = :category_name WHERE category_id = :category_id");
            $stmt->execute([
                ':category_name' => $category_name,
                ':category_id' => $category_id
            ]);

            $success = 'Category updated successfully.';
            header("Location: list_categories.php");
            exit;
        } catch (Exception $e) {
            $error = 'Error updating the category: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
            <?php include 'navbar.php'; ?>
    </header>
    <main class="edit">
        <h1>Edit Category</h1>
        
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form class="edit_form" action="edit_category.php?category_id=<?= htmlspecialchars($category_id); ?>" method="post">
            <div>
                <label for="name">Category Name:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($category_name); ?>">
            </div>
            
            <div>
                <input type="submit" value="Update Category">
            </div>
        </form>
    </main>
</body>
</html>
