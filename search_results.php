<?php
// search_results.php

session_start();
require 'db_connect.php';

$query = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
$category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'all';
$resultsPerPage = 2;
$currentPage = 1; 

if (isset($_GET['page'])) {
    $pageParam = $_GET['page'];
    if (is_numeric($pageParam) && $pageParam > 0) {
        $currentPage = (int)$pageParam;
    }
}
$start = ($currentPage - 1) * $resultsPerPage;

if (!empty($query)) {
    try {
        $searchQuery = '%' . $query . '%';
        if ($category === 'all') {
            $stmt = $pdo->prepare("SELECT * FROM pages WHERE title LIKE ? OR content LIKE ? LIMIT ?, ?");
            $stmt->execute([$searchQuery, $searchQuery, $start, $resultsPerPage]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM pages WHERE (title LIKE ? OR content LIKE ?) AND category_id = ? LIMIT ?, ?");
            $stmt->execute([$searchQuery, $searchQuery, $category, $start, $resultsPerPage]);
        }
        $filteredResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalResultsStmt = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE title LIKE ? OR content LIKE ?");
        $totalResultsStmt->execute([$searchQuery, $searchQuery]);
        $totalResults = $totalResultsStmt->fetchColumn();
        $totalPages = ceil($totalResults / $resultsPerPage);

    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        echo '<p class="error">' . $error . '</p>';
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
                            <td><?= mb_substr($page['content'], 0, 160); ?>
                            <?php if(mb_strlen($page['content']) > 160): ?>
                                    ...<a href="view.php?page_id=<?= htmlspecialchars($page['page_id']); ?>">read more</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($totalResults > $resultsPerPage): ?>
                <div class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="?query=<?= urlencode($query) ?>&page=<?= max(1, $currentPage - 1) ?>">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?query=<?= urlencode($query) ?>&page=<?= $i ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?query=<?= urlencode($query) ?>&page=<?= (int)($currentPage + 1) ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p>No results found for "<?php echo htmlspecialchars($query); ?>".</p>
        <?php endif; ?>
    </main>
</body>
</html>
