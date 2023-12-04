<?php
// all_pages.php
include 'db_connect.php';

$pageTitle = "Gallery"; 

$stmt = $pdo->prepare("SELECT * FROM pages");
$stmt->execute();

$pages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'header.php'; ?>

<body class="p-3 w-75 mx-auto border-0 bd-example m-0 border-0 bg-black">
    <header>
        <?php include 'home_navbar.php'; ?>
    </header>
    <main>
        <div class="d-flex flex-wrap justify-content-start">
            <?php if (!empty($pages)): ?>
            <?php foreach ($pages as $page): ?>
            <article class="card bg-dark m-2" style="width: 18rem;">
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
                    <a href="view.php?page_id=<?= htmlspecialchars($page['page_id']); ?>" class="btn btn-primary">Read
                        More</a>
                </div>
            </article>
            <?php endforeach; ?>
            <?php else: ?>
            <p>No result found.</p>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>