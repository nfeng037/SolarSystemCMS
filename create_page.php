<?php
// create_page.php

session_start();

require 'db_connect.php';
require 'check_access.php';
require './lib/htmlpurifier-4.15.0/library/HTMLPurifier.auto.php';
require_once 'image_functions.php';

if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

$stmt = $pdo->query("SELECT category_id, category_name FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
$title = ''; 
$content = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['name'] ?? ''; 
    $content = $_POST['description'] ?? ''; 
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category'];
    $imageFilename = '';
    $imageUploaded = false; 

    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    $clean_html = $purifier->purify($description);

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $temporaryPath = $image['tmp_name'];
        $resizedFilename = 'resized_' . basename($image['name']);
        $resizedPath = file_upload_path($resizedFilename); // Absolute path for resized image
        $resizedRelativePath = file_upload_path($resizedFilename, 'uploads', true); // Relative path for resized image
    
        if (file_is_an_image($temporaryPath, $resizedPath)) {
            if (resize_image($temporaryPath, $resizedPath, 400)) {
                $imageUploaded = true; 
            } else {
                $error = 'Image could not be resized.';
            }
        } else {
            $error = 'The file is not a valid image.';
        }
    }

    if (!$error) {
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("INSERT INTO pages (title, content, category_id, creator_id) VALUES (:title, :content, :category_id, :creator_id)");
            $stmt->execute([
                ':title' => $name,
                ':content' => $clean_html,
                ':category_id' => $category_id,
                ':creator_id' => $_SESSION['user_id']
            ]);
            $page_id = $pdo->lastInsertId();

            if ($imageUploaded) {
                $stmt = $pdo->prepare("INSERT INTO images (page_id, file_name) VALUES (:page_id, :file_name)");
                $stmt->execute([
                    ':page_id' => $page_id,
                    ':file_name' => $resizedRelativePath
                ]);
                
                $stmt = $pdo->prepare("UPDATE pages SET image_url = :image_url WHERE page_id = :page_id");
                $stmt->execute([
                    ':image_url' => $resizedRelativePath,
                    ':page_id' => $page_id
                ]);
            }

            $pdo->commit();
            $success = 'Page created successfully.';
            header("Location: view.php?page_id=" . $page_id);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error creating the page: ' . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Page</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Include Quill library -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="create">
        <h1>Create New Page</h1>
        
        <!-- Error or success messages -->
        <?php if ($error): ?>
            <p class="error"><?= $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?= $success; ?></p>
        <?php endif; ?>

        <form id="myForm" action="create_page.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($title); ?>">
            </div>
            
            <div>
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['category_id']; ?>">
                            <?= htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Hidden input to store the description -->
            <div>
                <label for="description">Description:</label>
                <div id="editor-container"></div>
                <input type="hidden" name="description" id="hidden-description">
            </div>
            
            <div>
                <label for="image">Upload Image (optional):</label>
                <input type="file" id="image" name="image">
            </div>

            
            <div>
                <input type="submit" value="Create Page">
            </div>
        </form>
    </main>

    <!-- Include Quill JS after the form to capture the submission -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor-container', {
            theme: 'snow'
        });
        var storedContent = '<?= addslashes($content); ?>';
        if (storedContent) {
            quill.root.innerHTML = storedContent;
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            var form = document.querySelector('#myForm');
            form.addEventListener('submit', function(e) {

                var description = document.querySelector('#hidden-description');
                description.value = quill.root.innerHTML;
                console.log('Captured description:', description.value);
            });
        });


    </script>

</body>
</html>
