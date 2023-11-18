<?php
// Only start the session if one hasn't already been started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Checks if the user role in the session matches the required role.
 *
 * @param string $requiredRole The role required to access a resource.
 * @return bool Returns true if the user has the required role, otherwise false.
 */
if (!function_exists('checkUserRole')) {
    function checkUserRole($requiredRole) {
        // Check if the session contains user id and role
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            // User is not logged in or role is not set in the session
            return false;
        }

        // Check if the role matches
        return $_SESSION['role'] === $requiredRole;
    }
}
?>
