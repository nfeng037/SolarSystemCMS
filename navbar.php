<?php
// navbar.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'check_access.php';
?>


<nav>
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="create_page.php">Create New</a></li>
        <li><a href="list_pages.php">All Pages</a></li>
        <li><a href="#">Categories</a></li>
        <li><a href="#">Comments</a></li>
    </ul>
    <div class="auth">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">SIGN OUT</a>
        <?php else: ?>
            <a href="login.php">SIGN IN</a>
        <?php endif; ?>
    </div>
</nav>