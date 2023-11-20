<?php
// search_results.php

session_start();

require 'db_connect.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';
$filteredResults = [];

if (!empty($query)) {
    try {
        // Fetch all pages from the database
        $stmt = $pdo->prepare("SELECT * FROM pages");
        $stmt->execute();
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Filter the results based on the stripped content
        foreach ($searchResults as $page) {
            $plainContent = strip_tags($page['content']);
            if (stripos($plainContent, $query) !== false || stripos($page['title'], $query) !== false) {
                $filteredResults[] = $page;
            }
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        echo $error;  // Display error for debugging (remove or handle differently in production)
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="user">
    <?php include 'home_navbar.php'; ?>
    <main class="main-content">
        <h1>Search Results for "<?php echo htmlspecialchars($query); ?>"</h1>
        <?php if (!empty($filteredResults)): ?>
            <table class="pages-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredResults as $page): ?>
                        <tr>
                            <td><a href="view.php?page_id=<?= htmlspecialchars($page['page_id']); ?>"><?= htmlspecialchars($page['title']); ?></a></td>
                            <td><?= mb_substr($page['content'], 0, 200); ?>
                            <?php if(mb_strlen($page['content']) > 200): ?>
                                    ...<a href="view.php?page_id=<?= $page['page_id'];?>">read more</a>
                                <?php endif; ?>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No results found for "<?php echo htmlspecialchars($query); ?>".</p>
        <?php endif; ?>
    </main>
</body>
</html>
