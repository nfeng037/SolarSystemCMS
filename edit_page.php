<?php
// edit_page.php

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

$pageTitle = "Edit Page"; 

// Validate page_id
$page_id = filter_input(INPUT_GET, 'page_id', FILTER_VALIDATE_INT);
if (!$page_id) {
    $error = 'Invalid page id.';
}

$stmt = $pdo->prepare("SELECT * FROM pages WHERE page_id = :page_id");
$stmt->execute([':page_id' => $page_id]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$page) {
    $error = 'The page was not found.';
}

$stmt = $pdo->query("SELECT category_id, category_name FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
$title = $page['title']; 
$content = $page['content']; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category'];
    $imageUploaded = false; 
    $title = $_POST['name'] ?? $title;
    $content = $_POST['description'] ?? $content;

    // Using HTMLPurifier to prevent XSS attacks.
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    $clean_html = $purifier->purify($description);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = $_FILES['image'];
        $temporaryPath = $image['tmp_name'];
        $imageFilename = basename($image['name']);
        $resizedFilename = 'resized_' . $imageFilename;
        $resizedPath = file_upload_path($resizedFilename); 
    
        if (file_is_an_image($temporaryPath, $resizedPath)) {
            if (resize_image($temporaryPath, $resizedPath, 400)) {
                $resizedRelativePath = 'uploads/' . $resizedFilename;
        
                $checkImageStmt = $pdo->prepare("SELECT * FROM images WHERE page_id = :page_id");
                $checkImageStmt->execute([':page_id' => $page_id]);
                $existingImage = $checkImageStmt->fetch(PDO::FETCH_ASSOC);
        
                if ($existingImage) {
                    $updateImageStmt = $pdo->prepare("UPDATE images SET file_name = :file_name WHERE page_id = :page_id");
                    $updateImageStmt->execute([
                        ':file_name' => $resizedRelativePath,
                        ':page_id' => $page_id
                    ]);
                } else {
                    $insertImageStmt = $pdo->prepare("INSERT INTO images (page_id, file_name) VALUES (:page_id, :file_name)");
                    $insertImageStmt->execute([
                        ':page_id' => $page_id,
                        ':file_name' => $resizedRelativePath
                    ]);
                }
        
                $imageUploaded = true;
            } else {
                $error = 'Image could not be resized.';
            }
        } else {
            $error = 'The file is not a valid image.';
        }
    }
    
    $deleteImage = isset($_POST['delete_image']) && $_POST['delete_image'] == '1';

    if ($deleteImage && !empty($page['image_url'])) {
        $deleteImageStmt = $pdo->prepare("DELETE FROM images WHERE page_id = :page_id");
        $deleteImageStmt->execute([':page_id' => $page_id]);

        $stmt = $pdo->prepare("UPDATE pages SET image_url = NULL WHERE page_id = :page_id");
        $stmt->execute([':page_id' => $page_id]);

        $resizedFilename = basename($page['image_url']);
        $imagePath = 'uploads/' . $resizedFilename;

        if (file_exists($imagePath)) {
            if (!unlink($imagePath)) {
                $error = "Error: Unable to delete file.";
            } else {
                echo "File successfully deleted.";
            }
        } else {
            $error = "Error: File does not exist.";
        }
    }

    if (!$error) {
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("UPDATE pages SET title = :title, content = :content, category_id = :category_id WHERE page_id = :page_id");
            $stmt->execute([
                ':title' => $name,
                ':content' => $clean_html,
                ':category_id' => $category_id,
                ':page_id' => $page_id
            ]);

            if ($imageUploaded) {
                $updatePageStmt = $pdo->prepare("UPDATE pages SET image_url = :image_url WHERE page_id = :page_id");
                $updatePageStmt->execute([
                    ':image_url' => $resizedRelativePath,
                    ':page_id' => $page_id
                ]);

                $updateImageStmt = $pdo->prepare("UPDATE images SET file_name = :file_name WHERE page_id = :page_id");
                $updateImageStmt->execute([
                    ':file_name' => $resizedRelativePath,
                    ':page_id' => $page_id
                ]);
            }

            $pdo->commit();
            $success = 'Page updated successfully.';
            header("Location: view.php?page_id=" . $page_id);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error updating the page: ' . $e->getMessage();
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
        <div class="d-flex justify-content-center">
            <h1>Page - <?= $page['title'] ?></h1>
        </div>
        <?php if ($error): ?>
        <p class="alert alert-danger" role="alert"><?= $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
        <p class="text-success mt-2"><?= $success; ?></p>
        <?php endif; ?>

        <form class="edit_form" id="editForm" action="edit_page.php?page_id=<?= $page_id; ?>" method="post"
            enctype="multipart/form-data">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($title); ?>">
            </div>

            <div>
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['category_id']; ?>"
                        <?php if ($category['category_id'] == $page['category_id']) echo 'selected'; ?>>
                        <?= htmlspecialchars($category['category_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <p>Description:</p>
                <div id="editor-container"><?= htmlspecialchars($content); ?></div>
                <input type="hidden" name="description" id="hidden-description"
                    value="<?= htmlspecialchars($content); ?>">
            </div>

            <div>
                <label for="image">Change Image (optional):</label>
                <input type="file" id="image" name="image">
            </div>

            <div>
                <?php if (!empty($page['image_url'])): ?>
                <label for="delete_image">Delete Current Image:</label>
                <input type="checkbox" id="delete_image" name="delete_image" value="1">
                <img src="<?= htmlspecialchars($page['image_url']); ?>" alt="Current Image" class="edit-image-preview">
                <?php endif; ?>
            </div>

            <div>
                <input class="btn btn-primary mb-2 mt-2" type="submit" value="Update Page">
                <a href="delete_page.php?page_id=<?= $page['page_id']; ?>" class="btn btn-danger ml-3" role="button"
                    onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
            </div>
        </form>
    </main>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
    var quill = new Quill('#editor-container', {
        theme: 'snow'
    });

    quill.root.innerHTML = '<?= addslashes($content); ?>';

    document.addEventListener('DOMContentLoaded', (event) => {
        var form = document.querySelector('#editForm');
        form.onsubmit = function(e) {
            var description = document.querySelector('#hidden-description');
            description.value = quill.root.innerHTML;
        };
    });
    </script>

    <?php include 'footer.php'; ?>

    <?php include 'scripts.php'; ?>

</body>

</html>
