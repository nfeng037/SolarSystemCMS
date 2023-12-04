<?php
// list_pages.php

session_start();

require 'db_connect.php';
require 'check_access.php'; 

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Pages"; 
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

<?php include 'header.php'; ?>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container">
        <div>
            <a class="btn btn-primary mb-2 mt-2" role="button" href="create_page.php">Create New</a>
        </div>
        <?php if (!empty($pages)): ?>
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>
                        Title
                        <a href="?sort=title&order=asc"
                            class="<?= ($current_sort == 'titleASC') ? 'current-sort' : '' ?>"></a>
                        <a href="?sort=title&order=desc"
                            class="<?= ($current_sort == 'titleDESC') ? 'current-sort' : '' ?>"></a>
                    </th>
                    <th>Category</th>
                    <th>Content</th>
                    <th>
                        Creation Time
                        <a href="?sort=creation_time&order=asc"
                            class="<?= ($current_sort == 'creation_timeASC') ? 'current-sort' : '' ?>"></a>
                        <a href="?sort=creation_time&order=desc"
                            class="<?= ($current_sort == 'creation_timeDESC') ? 'current-sort' : '' ?>"></a>
                    </th>
                    <th>
                        Last Modified
                        <a href="?sort=last_modified_time&order=asc"
                            class="<?= ($current_sort == 'last_modified_timeASC') ? 'current-sort' : '' ?>"></a>
                        <a href="?sort=last_modified_time&order=desc"
                            class="<?= ($current_sort == 'last_modified_timeDESC') ? 'current-sort' : '' ?>"></a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $index => $page): ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td><a href="view.php?page_id=<?= $page['page_id']; ?>"><?= htmlspecialchars($page['title']); ?></a>
                    </td>
                    <td><?= htmlspecialchars($page['category_name']); ?></td>
                    <td>
                        <?= mb_substr($page['content'], 0, 120); ?>
                        <?php if(mb_strlen($page['content']) > 120): ?>
                        ...<a href="view.php?page_id=<?= $page['page_id'];?>">read more</a>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($page['creation_time']); ?></td>
                    <td><?= htmlspecialchars($page['last_modified_time']); ?></td>
                    <td>
                        <a href="edit_page.php?page_id=<?= $page['page_id']; ?>" class="btn btn-success"
                            role="button">Edit</a>
                        <a href="delete_page.php?page_id=<?= $page['page_id']; ?>" class="btn btn-danger" role="button"
                            onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p cclass="alert alert-danger mt-5" role="alert">No Pages found.</p>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>