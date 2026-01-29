<?php
/**
 * CynTour - Logout Handler
 * 
 * Securely destroys user session and clears authentication data.
 */

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'helpers.php';

// Clear remember me cookie first (always do this, regardless of database success)
if (isset($_COOKIE['remember_me'])) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    setcookie('remember_me', '', time() - 3600, '/', '', $secure, true);
}

// Clear remember me token from database if user was logged in
if (isset($_SESSION['user_id'])) {
    try {
        require_once 'config.php';
        $conn = getMysqliConnection();
        $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        error_log('Logout error clearing token: ' . $e->getMessage());
    }
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
safe_redirect('login.php');
?>