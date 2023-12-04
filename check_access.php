<?php
// ccheck_access.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('checkUserRole')) {
    function checkUserRole($requiredRole) {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            return false;
        }

        return $_SESSION['role'] === $requiredRole;
    }
}
?>
