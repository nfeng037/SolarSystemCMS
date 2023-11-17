<?php
// list_pages.php

session_start();

require 'db_connect.php';
require 'check_access.php'; 

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$pages = [];
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title'; 
$sort_order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC';

$current_sort = $sort . $sort_order;

try {
    $stmt = $pdo->prepare("SELECT pages.*, categories.category_name FROM pages LEFT JOIN categories ON pages.category_id = categories.category_id ORDER BY $sort $sort_order");
    $stmt->execute();
    
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pages List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <main class="main-content">
        <h1>Pages List</h1>
        
        <?php if (!empty($pages)): ?>
            <table class="pages-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            Title
                            <a href="?sort=title&order=asc" class="<?= ($current_sort == 'titleASC') ? 'current-sort' : '' ?>"></a>
                            <a href="?sort=title&order=desc" class="<?= ($current_sort == 'titleDESC') ? 'current-sort' : '' ?>"></a>
                        </th>
                        <th>Category</th>
                        <th>Content</th>
                        <th>
                            Creation Time
                            <a href="?sort=creation_time&order=asc" class="<?= ($current_sort == 'creation_timeASC') ? 'current-sort' : '' ?>"></a>
                            <a href="?sort=creation_time&order=desc" class="<?= ($current_sort == 'creation_timeDESC') ? 'current-sort' : '' ?>"></a>
                        </th>
                        <th>
                            Last Modified
                            <a href="?sort=last_modified_time&order=asc" class="<?= ($current_sort == 'last_modified_timeASC') ? 'current-sort' : '' ?>"></a>
                            <a href="?sort=last_modified_time&order=desc" class="<?= ($current_sort == 'last_modified_timeDESC') ? 'current-sort' : '' ?>"></a>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $index => $page): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><a href="view.php?page_id=<?= $page['page_id']; ?>"><?= htmlspecialchars($page['title']); ?></a></td>
                            <td><?= htmlspecialchars($page['category_name']); ?></td>
                            <td><?= $page['content']; ?></td>
                            <td><?= htmlspecialchars($page['creation_time']); ?></td>
                            <td><?= htmlspecialchars($page['last_modified_time']); ?></td>
                            <td>
                                <a href="edit_page.php?page_id=<?= $page['page_id']; ?>" class="edit-btn">Edit</a>
                                <a href="delete_page.php?page_id=<?= $page['page_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="admin-links">
                <a href="create_page.php">Create New</a>
            </div>
            <p class="no-data">No celestial bodies found.</p>
        <?php endif; ?>
    </main>
</body>
</html>
