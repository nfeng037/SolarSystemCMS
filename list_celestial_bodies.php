<?php
// list_celestial_bodies.php

session_start();

require 'db_connect.php';

// Initialize an array to hold the celestial bodies data
$celestial_bodies = [];

try {
    // Prepare the SQL statement to fetch data from 'pages' table
    // Join with 'images' table to get the image file names
    $stmt = $pdo->prepare("SELECT p.page_id, p.title, p.content, i.file_name FROM pages p LEFT JOIN images i ON p.page_id = i.page_id");
    $stmt->execute();
    
    // Fetch all celestial bodies records
    $celestial_bodies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Celestial Bodies List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <main>
        <h1>Celestial Bodies List</h1>
        
        <?php if (!empty($celestial_bodies)): ?>
            <div class="celestial-bodies-list">
                <?php foreach ($celestial_bodies as $body): ?>
                    <div class="celestial-body">
                        <h2><?php echo htmlspecialchars($body['title']); ?></h2>
                        <p><?php echo htmlspecialchars($body['content']); ?></p>
                        <?php if (!empty($body['file_name'])): ?>
                            <img src="/WEBD2/planetCMS/uploads/<?php echo htmlspecialchars($body['file_name']); ?>" alt="Image of <?php echo htmlspecialchars($body['title']); ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No celestial bodies found.</p>
        <?php endif; ?>
    </main>
</body>
</html>
