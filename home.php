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
  <header>
    <!-- Navigation bar -->
    <?php include 'home_navbar.php'; ?>
  </header>

  <main>
    <!-- Main banner -->
    <section class="intro">
      <h1>Welcome to the Cosmos Explorer</h1>
      <p>Your gateway to the wonders of our universe! Embark on an interstellar journey with us as we unveil the secrets of the cosmos. Our intuitive interface invites space enthusiasts of all ages to explore, learn, and connect. Delve into our extensive database, join vibrant discussions, and customize your celestial voyage. Whether you're here to contribute knowledge or simply gaze at the stars, the Cosmos Explorer is your space odyssey. Begin your adventure with a click and let the sky be not the limit, but the beginning!</p>
      <a href='list_pages.php' class="start-button">Start Adventure</a>
    </section>
    <!-- Gallery -->
    <section class="gallery">
    <h2>Gallery</h2>
    <div class="article-container">
        <?php if (!empty($pages)): ?>
            <?php foreach ($pages as $page): ?>
                <article>
                    <img src="<?= htmlspecialchars($page['image_url']); ?>" alt="<?= htmlspecialchars($page['title']); ?>">
                    <h3><?= htmlspecialchars($page['title']); ?></h3>
                    <p><?= $page['content']; ?></p>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No result found.</p>
        <?php endif; ?>
    </div>
    </section>
  </main>

  <footer>
    <!-- Footer content -->
  </footer>

</body>
</html>
