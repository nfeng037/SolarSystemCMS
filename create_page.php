<?php
// create_page.php

session_start();

require 'db_connect.php'; 
require 'check_access.php';

// Redirect to the login page if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$error = ''; // Initialize error message variable
$success = ''; // Initialize success message variable

// Get all categories for celestial bodies
$stmt = $pdo->query("SELECT category_id, category_name FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to check if the file is an image
function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension   = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $actual_mime_type        = getimagesize($temporary_path)['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

// Handle the celestial body creation form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category'];
    $imageFilename = ''; // Initialize image filename variable

    // Begin database transaction
    $pdo->beginTransaction();

    try {
        // Insert new celestial body into the database
        $stmt = $pdo->prepare("INSERT INTO celestial_bodies (name, description, category_id) VALUES (:name, :description, :category_id)");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':category_id' => $category_id
        ]);
        $celestial_body_id = $pdo->lastInsertId();

        // Insert a corresponding page into the pages table
        $stmt = $pdo->prepare("INSERT INTO pages (title, content, category_id, creator_id) VALUES (:title, :content, :category_id, :creator_id)");
        $stmt->execute([
            ':title' => $name,
            ':content' => $description,
            ':category_id' => $category_id,
            ':creator_id' => $_SESSION['user_id']
        ]);
        $page_id = $pdo->lastInsertId();

        // Handle the image upload if a file was provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = $_FILES['image'];
            $temporaryPath = $image['tmp_name'];
            $imageFilename = basename($image['name']);
            $newPath = 'uploads/' . $imageFilename;
        
            if (file_is_an_image($temporaryPath, $newPath)) {
                if (move_uploaded_file($temporaryPath, $newPath)) {
                    // Image uploaded successfully
                    // Insert image information into the images table with reference to the page
                    $stmt = $pdo->prepare("INSERT INTO images (page_id, file_name) VALUES (:page_id, :file_name)");
                    $stmt->execute([
                        ':page_id' => $page_id, // Use the page_id from the pages table
                        ':file_name' => $imageFilename
                    ]);
                    
                    // Update the celestial_bodies table with the image URL
                    $stmt = $pdo->prepare("UPDATE celestial_bodies SET image_url = :image_url WHERE celestial_body_id = :celestial_body_id");
                    $stmt->execute([
                        ':image_url' => $newPath, // The path to the uploaded image
                        ':celestial_body_id' => $celestial_body_id
                    ]);
                } else {
                    throw new Exception('The file could not be uploaded.');
                }
            } else {
                throw new Exception('The file is not a valid image.');
            }
        }

        // Commit the transaction
        $pdo->commit();
        $success = 'Celestial body and page created successfully.';

        // Redirect to the view page
        header("Location: view.php?page_id=" . $page_id);
        exit;

    } catch (Exception $e) {
        // An error occurred, rollback any database changes
        $pdo->rollBack();
        $error = 'Error creating the celestial body and page: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main>
        <h1>Create New Page</h1>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="create_page.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div>
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>">
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
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
</body>
</html>
