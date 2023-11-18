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
  <title>Cosmos Explorer</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body  class="user">
  <?php include 'home_navbar.php'; ?>
  <main>
    <!-- Main banner -->
    <section class="intro">
      <h1> Cosmos Explorer Welcomes You</h1>
      <a href='all_pages.php' class="start-button">Start Adventure</a>
    </section>
    <!-- Gallery -->
    <section class="gallery">
      <h2>News & Updates</h2>
      <div class="article-container">
          <?php if (!empty($pages)): ?>
              <?php 
                  $counter = 0; 
                  foreach ($pages as $page):
                      if ($counter >= 8) break; 
                      $content = $page['content'];
                      $isLong = mb_strlen($content) > 200; 
              ?>
                  <article>
                    <a href="view.php?page_id=<?= $page['page_id']; ?>"> 
                      <img src="<?= htmlspecialchars($page['image_url']); ?>" alt="<?= htmlspecialchars($page['title']); ?>">
                    </a>
                    <h3><?= htmlspecialchars($page['title']); ?></h3>
                    <p>
                        <?= mb_substr($content, 0, 200); ?> 
                        <?php if ($isLong): ?>
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

</body>
</html>
