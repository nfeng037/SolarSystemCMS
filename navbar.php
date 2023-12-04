<?php
// navbar.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'check_access.php';
?>


<header class="bg-dark">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 col-12">
                <h1 class="text-white my-2" id="pageTitle"><?= $pageTitle; ?></h1>
            </div>
            <div class="col-md-8 col-12">
                <nav class="navbar navbar-expand-lg navbar-dark">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarNav">
                        <ul class="navbar-nav ml-auto nav-underline">
                            <li class="nav-item"><a class="nav-link" href="index.php" role="button">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="list_pages.php" role="button">Pages</a></li>
                            <li class="nav-item"><a class="nav-link" href="list_categories.php"
                                    role="button">Categories</a></li>
                            <li class="nav-item"><a class="nav-link" href="list_comments.php" role="button">Comments</a>
                            </li>
                            <li class="nav-item"><a class="nav-link text-nowrap" href="home.php" role="button">User
                                    Page</a></li>
                        </ul>
                        <div>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="logout.php" role="button" class="btn btn-primary text-nowrap">SIGN OUT</a>
                            <?php else: ?>
                                <a href="login.php" role="button" class="btn btn-success text-nowrap">SIGN IN</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>