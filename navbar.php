<?php
// navbar.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'check_access.php';
?>

<header class="index_header">
    <nav>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="create_page.php">Create New</a></li>
            <li><a href="list_pages.php">All Pages</a></li>
            <li><a href="list_categories.php">Categories</a></li>
            <li><a href="list_comments.php">Comments</a></li>
            <li><a href="home.php">User Page</a></li>
        </ul>
        <div class="auth">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">SIGN OUT</a>
            <?php else: ?>
                <a href="login.php">SIGN IN</a>
            <?php endif; ?>
        </div>
    </nav>
</header>