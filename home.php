<?php
include 'db_connect.php';

$pageTitle = "Cosmos Explorer"; 

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
        <section class="intro">
            <h1> Cosmos Explorer Welcomes You</h1>
            <a href='all_pages.php' class="btn btn-light">Start Adventure</a>
        </section>
        <section class="gallery">
            <h2>Gallery</h2>
            <div class="article-container">
                <?php if (!empty($pages)): ?>
                <?php 
                  $counter = 0; 
                  foreach ($pages as $page):
                      if ($counter >= 6) break; 
              ?>
                <article>
                    <a href="view.php?page_id=<?= $page['page_id']; ?>">
                        <img src="<?= htmlspecialchars($page['image_url']); ?>"
                            alt="<?= htmlspecialchars($page['title']); ?>">
                    </a>
                    <h3><?= htmlspecialchars($page['title']); ?></h3>
                    <p>
                        <?= mb_substr(($page['content']), 0, 120); ?>
                        <?php if (mb_strlen($page['content']) > 120): ?>
                        ... <a href="view.php?page_id=<?= $page['page_id']; ?>">read more</a>
                        <?php endif; ?>
                    </p>
                </article>
                <?php 
                  $counter++; 
                  endforeach; 
              ?>
                <?php else: ?>
                <p>No result found.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>