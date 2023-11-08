<?php
// navbar.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'check_access.php';
?>

<!-- Navigation bar -->
<nav>
    <ul>
        <li><a href="index.php">HOME</a></li>
        <li><a href="galley.php">GALLERY</a></li>
        <?php if (isset($_SESSION['user_id']) && checkUserRole('admin')): ?>
            <li><a href="admin.php">ADMIN</a></li>
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
