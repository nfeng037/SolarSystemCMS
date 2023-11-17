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
        <li><a href="create_page.php">Publishing New Celestial Body</a></li>
        <li><a href="list_pages.php">Celestial Body Management</a></li>
        <li><a href="#">Category Management</a></li>
        <li><a href="#">Comment Management</a></li>
    </ul>
    <div class="auth">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">SIGN OUT</a>
        <?php else: ?>
            <a href="login.php">SIGN IN</a>
        <?php endif; ?>
    </div>
</nav>