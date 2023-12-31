<?php
// create_page.php

session_start();

require 'db_connect.php';
require 'check_access.php';
require './lib/htmlpurifier-4.15.0/library/HTMLPurifier.auto.php';
require_once 'image_functions.php';

$pageTitle = "New Page"; 

// Validation of User Role and Session
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
    $title = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);     // Validate and sanitize title
    $content = $_POST['description'] ?? '';
    $category_id = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT); // Validate category_id

    if (empty($title) || $title === false || empty($category_id)) {
        $error = 'Invalid title or category.';
    } else {
        $name = trim($title);
        $description = trim($content);

        // Using HTMLPurifier to prevent XSS attacks.
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $clean_html = $purifier->purify($description);

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = $_FILES['image'];
            $temporaryPath = $image['tmp_name'];
            $resizedFilename = 'resized_' . basename($image['name']);
            $resizedPath = file_upload_path($resizedFilename); 
            $resizedRelativePath = file_upload_path($resizedFilename, 'uploads', true); 
        
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
}
?>


<!DOCTYPE html>
<html lang="en">

<?php include 'header.php'; ?>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container create">

        <?php if ($error): ?>
        <p class="alert alert-danger" role="alert"><?= $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
        <p class="text-success mt-2"><?= $success; ?></p>
        <?php endif; ?>

        <form id="myForm" action="create_page.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($title); ?>">
            </div>

            <div>
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['category_id']; ?>">
                        <?= htmlspecialchars($category['category_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <p>Description:</p>
                <div id="editor-container"></div>
                <input type="hidden" name="description" id="hidden-description">
            </div>

            <div>
                <label for="image">Upload Image (optional):</label>
                <input type="file" id="image" name="image">
            </div>


            <div>
                <input type="submit" value="Create Page" class="btn btn-primary mb-2 mt-2">
            </div>
        </form>
    </main>

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

    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>