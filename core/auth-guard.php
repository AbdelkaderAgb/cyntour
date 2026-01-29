<?php
/**
 * CynTour - Authentication Guard Middleware
 * 
 * Include this file at the top of any page that requires authentication.
 * Redirects to login page if user is not authenticated.
 * 
 * @package CynTour
 * @version 2.0
 */

// Load bootstrap if not already loaded
if (!defined('CYNTOUR_BASE_PATH')) {
    require_once dirname(__FILE__) . '/bootstrap.php';
}

use CynTour\Core\Application;
use CynTour\Core\Helper;

$app = Application::getInstance();

// Check if user is authenticated
if (!$app->isAuthenticated()) {
    // Store the requested URL for redirect after login (validate it's a local path)
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    // Only store relative URLs that don't start with // (protocol-relative) or contain ://
    if (!empty($request_uri) && strpos($request_uri, '//') !== 0 && strpos($request_uri, '://') === false) {
        $_SESSION['redirect_after_login'] = $request_uri;
    }
    
    Helper::redirect('login.php');
}

// Optional: Check session timeout (30 minutes of inactivity)
$session_timeout = $app->config('session.timeout', 30 * 60);
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
    
    Helper::redirect('login.php?expired=1');
}

// Update last activity time
$_SESSION['last_activity'] = time();
