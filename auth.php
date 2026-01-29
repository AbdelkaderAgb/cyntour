<?php
/**
 * CynTour - Authentication Guard
 * 
 * Include this file at the top of any page that requires authentication.
 * Redirects to login page if user is not authenticated.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is authenticated
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    // Store the requested URL for redirect after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
    
    header('Location: login.php');
    exit();
}

// Optional: Check session timeout (30 minutes of inactivity)
$sessionTimeout = 30 * 60; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionTimeout)) {
    // Session has expired
    session_unset();
    session_destroy();
    
    header('Location: login.php?expired=1');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
