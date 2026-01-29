<?php
/**
 * CynTour Bootstrap File
 * 
 * This file initializes the application, loads required files,
 * and starts the session. Include this file at the top of every page.
 * 
 * @package CynTour
 * @version 2.0
 */

// Define the base path constant
define('CYNTOUR_BASE_PATH', dirname(__FILE__));

// Error reporting (disable in production)
if (getenv('APP_DEBUG') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Load the autoloader
require_once CYNTOUR_BASE_PATH . '/core/autoload.php';

// Initialize the application
$app = \CynTour\Core\Application::getInstance();

// Start session
$app->startSession();
