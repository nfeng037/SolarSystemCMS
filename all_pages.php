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
                <?php foreach ($pages as $page): ?>
                    <article>
                        <?php if (!empty($page['image_url'])): ?>
                            <a href="view.php?page_id=<?= htmlspecialchars($page['page_id']); ?>"> 
                                <img src="<?= htmlspecialchars($page['image_url']); ?>" alt="<?= htmlspecialchars($page['title']); ?>">
                            </a>
                        <?php endif; ?>

                        <h3><a href="view.php?page_id=<?= htmlspecialchars($page['page_id']); ?>"><?= htmlspecialchars($page['title']); ?></a> </h3>
                        <p>
                            <?= mb_substr($page['content'], 0, 120); ?> 
                            <?php if (mb_strlen($page['content']) > 120): ?>
                                ... <a href="view.php?page_id=<?= htmlspecialchars($page['page_id']); ?>">read more</a> 
                            <?php endif; ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No result found.</p>
            <?php endif; ?>
        </div>
        </section>
    </main>
</body>
</html>