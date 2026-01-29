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
    // Store the requested URL for redirect after login (validate it's a local path)
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    // Only store relative URLs that don't start with // (protocol-relative) or contain ://
    if (!empty($request_uri) && strpos($request_uri, '//') !== 0 && strpos($request_uri, '://') === false) {
        $_SESSION['redirect_after_login'] = $request_uri;
    }
    
    header('Location: login.php');
    exit();
}

// Optional: Check session timeout (30 minutes of inactivity)
$session_timeout = 30 * 60; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // Store redirect URL before destroying session
    $redirect_url = $_SESSION['redirect_after_login'] ?? '';
    
    // Session has expired
    session_unset();
    session_destroy();
    
    // Start new session to store redirect URL
    session_start();
    if (!empty($redirect_url)) {
        $_SESSION['redirect_after_login'] = $redirect_url;
    }
    
    header('Location: login.php?expired=1');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
