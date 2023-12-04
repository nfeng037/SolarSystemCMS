<?php
// category_pages.php

session_start();

require 'db_connect.php';
require 'check_access.php'; 


$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$pages = [];
$category_name = '';

try {
    $categoryStmt = $pdo->prepare("SELECT category_name FROM categories WHERE category_id = :category_id");
    $categoryStmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $categoryStmt->execute();
    $categoryResult = $categoryStmt->fetch(PDO::FETCH_ASSOC);

    if ($categoryResult) {
        $category_name = $categoryResult['category_name'];
    }

    $stmt = $pdo->prepare("SELECT * FROM pages WHERE category_id = :category_id");
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

$pageTitle = $category_name; 

?>

<!DOCTYPE html>
<html lang="en">

<?php include 'header.php'; ?>

<body class="p-3 w-75 mx-auto border-0 bd-example m-0 border-0 bg-black">
    <header>
        <?php include 'home_navbar.php'; ?>
    </header>
    <main>
        <section>
            <h2 class="text-white m-2"><?= htmlspecialchars($category_name) ?></h2>
            <div class="d-flex flex-wrap justify-content-start">
                <?php if (!empty($pages)): ?>
                <?php foreach ($pages as $page):?>
                <article class="card bg-dark m-2" style="width: 17rem;">
                    <?php if (!empty($page['image_url'])): ?>
                    <img class="card-img-top" src="<?= htmlspecialchars($page['image_url']); ?>"
                        alt="<?= htmlspecialchars($page['title']); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title text-white"><?= htmlspecialchars($page['title']); ?></h5>
                        <div class="card-text text-white"><?= mb_substr($page['content'], 0, 130); ?>
                            <?php if (mb_strlen($page['content']) > 130): ?>
                            ...
                            <?php endif; ?>
                        </div>
                        <a href="view.php?page_id=<?= htmlspecialchars($page['page_id']); ?>"
                            class="btn btn-primary">Read More</a>
                    </div>
                </article>
                <?php endforeach;?>
                <?php else: ?>
                <p class="alert alert-danger mt-5" role="alert">No pages found for this category.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>