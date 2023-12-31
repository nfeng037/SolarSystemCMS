<?php
session_start();
require 'db_connect.php'; 

$error = '';
$pageTitle = "Login";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
 
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
 
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
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

<?php include 'header.php'; ?>

<body>

    <main class="login-container">
        <form action="login.php" method="post" class="login-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="submit" value="Login" class="btn btn-dark">
            <?php if (!empty($error)): ?>
            <p class="text-danger mt-2"><?= $error; ?></p>
            <?php endif; ?>
        </form>
    </main>
    <?php include 'scripts.php'; ?>

</body>

</html>