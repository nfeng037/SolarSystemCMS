<?php
// list_categories.php

session_start();

require 'db_connect.php';
require 'check_access.php'; 

$pageTitle = "Categories"; 

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

<?php include 'header.php'; ?>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container">
        <div>
            <a class="btn btn-primary mb-2 mt-2" role="button" href="create_category.php">Create New</a>
        </div>
        <?php if (!empty($categories)): ?>
        <table class="table table-hover">
            <thead class="table-dark">
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
                    <td>
                        <a href="category_pages.php?category_id=<?= $category['category_id']; ?>">
                            <?= htmlspecialchars($category['category_name']); ?>
                        </a>
                    </td>
                    <td>
                        <a href="edit_category.php?category_id=<?= $category['category_id']; ?>" class="btn btn-success"
                            role="button">Edit</a>
                        <a href="delete_category.php?category_id=<?= $category['category_id']; ?>"
                            class="btn btn-danger" role="button"
                            onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="alert alert-danger mt-5" role="alert">No categories found.</p>
        <?php endif; ?>
    </main>
    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>