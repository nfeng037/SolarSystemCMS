<?php
// home_navbar.php

require_once 'db_connect.php';
require 'check_access.php';

$categories = []; 

if(isset($_GET['action']) && $_GET['action'] == 'get_categories') {
    try {
        global $pdo; 
        $stmt = $pdo->query("SELECT * FROM categories");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categories); 
        exit;
    } catch (PDOException $e) {
        echo 'Database error: ' . $e->getMessage();
        exit;
    }
}

try {
    global $pdo; 
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
}

?>

<nav class="navbar navbar-expand-lg bg-black" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="home.php">HOME</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="all_pages.php">GALLERY</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        CATEGORIES
                    </a>
                    <ul class="dropdown-menu">
                        <?php foreach($categories as $category): ?>
                        <li><a class="dropdown-item"
                                href="category_pages.php?category_id=<?= $category['category_id']; ?>"><?= htmlspecialchars($category['category_name'])?></a>
                        </li>
                        <?php endforeach; ?>

                    </ul>
                </li>
                <?php if (isset($_SESSION['user_id']) && checkUserRole('admin')): ?>
                    <li><a class="nav-link" href="index.php">ADMIN</a></li>
                <?php endif; ?>
            </ul>
            <form class="search d-flex justify-content-around" action="search_results.php" method="get">
                <input class="form-control m-2" type="search" name="query" placeholder="SEARCH"
                    aria-label="Search through site content">

                <select name="category" class="m-2 rounded-2 p-2 form-select form-select-sm">
                    <option value="all">ALL CATEGORIES</option>
                    <?php foreach($categories as $category): ?>
                    <option value="<?=htmlspecialchars($category['category_id'])?>">
                        <?= htmlspecialchars($category['category_name'])?></option>
                    <?php endforeach; ?>
                </select>

                <input type="submit" value="SEARCH" class="btn btn-primary m-2">
            </form>
            <div class="m-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" role="button" class="btn btn-outline-dark text-nowrap">SIGN OUT</a>
                <?php else: ?>
                    <a href="login.php" role="button" class="btn btn-outline-dark text-nowrap">SIGN IN</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>