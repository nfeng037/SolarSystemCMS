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
$pageTitle = "New Category"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));

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

<?php include 'header.php'; ?>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container create">

        <?php if ($error): ?>
        <p class="text-danger mt-2"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
        <p class="text-success mt-2"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="create_category.php" method="post">
            <div>
                <label for="name">Category Name:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($name); ?>">
            </div>

            <div>
                <input type="submit" value="Create Category" class="btn btn-primary mb-2 mt-2">
            </div>
        </form>
    </main>
    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>