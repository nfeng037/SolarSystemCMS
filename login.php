<?php
session_start();
require 'db_connect.php'; // 您的数据库连接脚本

// 初始化错误信息变量
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 准备 SQL 语句来获取用户信息
    $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    // 检查是否找到了用户
    if ($stmt->rowCount() > 0) {
        // 获取用户信息
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 验证密码是否正确
        if (password_verify($password, $user['password'])) {
            // 密码正确，则设置会话变量
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // 重定向到 admin.php
            header("Location: admin.php");
            exit;
        } else {
            $error = 'The password you entered was not valid.';
        }
    } else {
        $error = 'No account found with that username.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <!-- 导航栏 -->
        <?php include 'navbar.php'; ?>
    </header>
    <main>
        <form action="login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="submit" value="Login">
        </form>
        <?php if (!empty($error)): ?>
        <p><?php echo $error; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
