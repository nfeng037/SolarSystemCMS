<?php
// home_navbar.php

require_once 'db_connect.php';
require 'check_access.php';

function getCategories() {
    global $pdo; 
    $stmt = $pdo->query("SELECT * FROM categories");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


if(isset($_GET['action']) && $_GET['action'] == 'get_categories') {
    $categories = getCategories();
    echo json_encode($categories); 
    exit;
}
?>

<!-- Navigation bar -->
<nav>
    <ul>
        <li><a href="home.php">HOME</a></li>
        <li><a href="all_pages.php">GALLERY</a></li>
        <li><a href="javascript:void(0)" id="categoryLink" onclick="toggleCategories()">CATEGORIES</a></li>

        <div id="categoryPopup">
            <ul id="categoryList"></ul>
        </div>
        <?php if (isset($_SESSION['user_id']) && checkUserRole('admin')): ?>
            <li><a href="index.php">ADMIN</a></li>
        <?php endif; ?>
    </ul>
    <form class="search" action="search_results.php" method="get">
        <input type="search" name="query" placeholder="SEARCH" aria-label="Search through site content">
        <input type="submit" value="SEARCH">
    </form>
    <div class="auth">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">SIGN OUT</a>
        <?php else: ?>
            <a href="login.php">SIGN IN</a>
        <?php endif; ?>
    </div>
</nav>



<script>
    function toggleCategories() {
        var popup = document.getElementById('categoryPopup');
        if (popup.style.display === 'block') {
            popup.style.display = 'none';
        } else {
            fetchCategories();
        }
    }

    function fetchCategories() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'home_navbar.php?action=get_categories', true);

        xhr.onload = function() {
            if (this.status === 200) {
                try {
                    var categories = JSON.parse(this.responseText);
                    var output = categories.map(function(cat) {
                        return '<li><a href="category_pages.php?category_id=' + cat.category_id + '">' + cat.category_name + '</a></li>';
                    }).join('');
                    document.getElementById('categoryList').innerHTML = output;
                    document.getElementById('categoryPopup').style.display = 'block';
                } catch(e) {
                    console.error('Error parsing JSON:', e);
                }
            } else {
                console.error('Server returned status code ' + this.status);
            }
        };

        xhr.onerror = function() {
            console.error('Request failed');
        };

        xhr.send();
    }

</script>