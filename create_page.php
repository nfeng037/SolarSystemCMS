<?php
// create_page.php

session_start();

require 'db_connect.php'; // 数据库连接
require 'check_access.php'; // 用户角色检查

// 如果用户未登录或不是管理员，则重定向到登录页面
if (!isset($_SESSION['user_id']) || !checkUserRole('admin')) {
    header("Location: login.php");
    exit;
}

$error = ''; // 初始化错误消息变量
$success = ''; // 初始化成功消息变量

// 获取所有分类
$stmt = $pdo->query("SELECT category_id, category_name FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = $_POST['category'];

    // 处理图片上传
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $imagePath = 'uploads/' . basename($image['name']);
        $imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        // 检查文件类型是否为图片
        if (in_array(strtolower($imageExtension), $allowedTypes) && getimagesize($image['tmp_name'])) {
            if (move_uploaded_file($image['tmp_name'], $imagePath)) {
                // 图片上传成功，设置图片名称以便保存到数据库
                $imageName = $image['name'];
            } else {
                $error = 'There was an error uploading the file.';
            }
        } else {
            $error = 'Please upload a valid image file.';
        }
    }

    // 如果没有错误，则插入新页面到数据库
    if (!$error) {
        try {
            $pdo->beginTransaction();
            
            // 插入新页面
            $stmt = $pdo->prepare("INSERT INTO pages (title, content, category_id) VALUES (:title, :content, :category_id )");
            $stmt->execute([':title' => $title, ':content' => $content, ':category_id' => $category_id, ]);
            $pageId = $pdo->lastInsertId();

            // 如果上传了图片，将图片信息插入到 images 表
            if ($imageName) {
                $stmt = $pdo->prepare("INSERT INTO images (page_id, file_name) VALUES (:page_id, :file_name)");
                $stmt->execute([':page_id' => $pageId, ':file_name' => $imageName]);
            }

            $pdo->commit();
            $success = 'Page created successfully!';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Database error: ' . $e->getMessage();
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
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main>
        <h1>Create New Page</h1>
        
        <?php if ($error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="create_page.php" method="post" enctype="multipart/form-data">
            <label for="title">Name:</label>
            <input type="text" id="title" name="title" required>
            
            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>">
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="content">Description:</label>
            <textarea id="content" name="content" required></textarea>
            
            <label for="image">Upload Image (optional):</label>
            <input type="file" id="image" name="image">
            
            <input type="submit" value="Create Page">
        </form>
    </main>

    <!-- ... footer ... -->
</body>
</html>
