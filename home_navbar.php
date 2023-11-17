<?php
// navbar.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'check_access.php';

$stmt = $pdo->query("SELECT category_id, category_name FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Navigation bar -->
<nav>
    <ul>
        <li><a href="home.php">HOME</a></li>
        <li><a href="list_pages.php">GALLERY</a></li>
        <li>
            <label for="category">Category</label>
            <select id="category" name="category" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['category_id']; ?>">
                        <?= htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </li>
        <?php if (isset($_SESSION['user_id']) && checkUserRole('admin')): ?>
            <li><a href="index.php">ADMIN</a></li>
        <?php endif; ?>
    </ul>
    <form class="search" action="search_results.php" method="get">
        <input type="search" name="query" placeholder="SEARCH" aria-label="Search through site content">
        <input type="submit" value="SEARCH">
    </form>
    <div class="auth">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">SIGN OUT</a>
        <?php else: ?>
            <a href="login.php">SIGN IN</a>
        <?php endif; ?>
    </div>
</nav>
