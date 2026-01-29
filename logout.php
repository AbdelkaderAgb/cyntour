<?php
// Start output buffering to ensure headers can be sent
ob_start();
session_start(); // Start the session

require_once 'helpers.php';

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page using safe_redirect helper
safe_redirect('login.php');
?>