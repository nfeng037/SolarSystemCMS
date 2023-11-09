<?php
// view.php

session_start();

require 'db_connect.php'; 
require 'check_access.php';

// Initialize variables
$pageData = null;
$error = '';
$isAdmin = checkUserRole('admin');
$imageUrl = '';

// Check if a page_id is provided and it's a valid integer
if (isset($_GET['page_id']) && filter_var($_GET['page_id'], FILTER_VALIDATE_INT)) {
    $page_id = $_GET['page_id'];
    
    // Prepare a SELECT statement to fetch the page data with its image
    $stmt = $pdo->prepare("SELECT p.title, p.content, i.file_name FROM pages p LEFT JOIN images i ON p.page_id = i.page_id WHERE p.page_id = :page_id");
    
    // Bind the page_id parameter
    $stmt->bindParam(':page_id', $page_id, PDO::PARAM_INT);
    
    try {
        // Execute the query
        $stmt->execute();
        
        // Fetch the page data
        $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
        
       echo "pagedata: {$pageData} <br>";
        echo "filename: {$pageData['file_name']}";
        // Check if the page entry was found
        if ($pageData) {
            // Set image URL if an image exists
            if (!empty($pageData['file_name'])) {
                // We use the relative path from the web root directory
                $imageUrl = 'uploads/' . htmlspecialchars($pageData['file_name']);
            }
        } else {
            $error = "Page not found.";
        }
    } catch (PDOException $e) {
        $error = "Error fetching the page: " . $e->getMessage();
    }
} else {
    $error = "No page ID provided or invalid ID.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageData['title'] ?? 'Page Not Found'); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <main>
        <?php if ($pageData): ?>
        <article>
            <h1><?php echo htmlspecialchars($pageData['title']); ?></h1>
            <?php if ($isAdmin): ?>
                <section class="page-actions">
                    <a href="edit_page.php?page_id=<?php echo $page_id; ?>">Edit</a>
                    <a href="delete_page.php?page_id=<?php echo $page_id; ?>" onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                </section>
            <?php endif; ?>
            <?php if ($imageUrl): ?>
                <!-- Image URL is the relative path from the web root directory -->
                <img src="<?php echo $imageUrl; ?>" alt="Image for <?php echo htmlspecialchars($pageData['title']); ?>">
            <?php endif; ?>
            <section>
                <?php echo nl2br(htmlspecialchars($pageData['content'])); ?>
            </section>
        </article>
        <?php else: ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
