<?php
// edit_category.php

session_start();
require 'db_connect.php';
require 'check_access.php'; 


if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Edit Category";
$error = '';
$success = '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$category_name = '';

$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT); // Validate category_id


if (!$category_id) {
    $error = 'Invalid category ID.';
} 

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

<?php include 'header.php'; ?>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container create">

        <?php if ($error): ?>
        <p class="alert alert-danger" role="alert"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
        <p cclass="text-success mt-2"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form class="edit_form" action="edit_category.php?category_id=<?= htmlspecialchars($category_id); ?>"
            method="post">
            <div>
                <label for="name">Category Name:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($category_name); ?>">
            </div>

            <div>
                <input type="submit" value="Update Category" class="btn btn-primary mb-2 mt-2">
            </div>
        </form>
    </main>
</body>

</html>