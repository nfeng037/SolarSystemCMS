<?php
include 'db_connect.php';

// Prepare a SQL statement to fetch all planet data
$stmt = $pdo->prepare("SELECT * FROM pages");
$stmt->execute();

// Fetch all the planet records
$pages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="user">
    <?php include 'home_navbar.php'; ?>
    <main>
        <section class="gallery">
        <h2>Gallery</h2>
        <div class="article-container">
            <?php if (!empty($pages)): ?>
                <?php 
                    foreach ($pages as $page):
                        $content = $page['content'];
                        $isLong = mb_strlen($content) > 150; 
                ?>
                    <article>
                        <a href="view.php?page_id=<?= $page['page_id']; ?>"> 
                        <img src="<?= htmlspecialchars($page['image_url']); ?>" alt="<?= htmlspecialchars($page['title']); ?>">
                        </a>
                        <h3><?= htmlspecialchars($page['title']); ?></h3>
                        <p>
                            <?= mb_substr($content, 0, 150); ?> 
                            <?php if ($isLong): ?>
                                ... <a href="view.php?page_id=<?= $page['page_id']; ?>">read more</a> 
                            <?php endif; ?>
                        </p>
                    </article>
                <?php 
                    endforeach; 
                ?>
            <?php else: ?>
                <p>No result found.</p>
            <?php endif; ?>
        </div>
        </section>
    </main>
</body>
</html>