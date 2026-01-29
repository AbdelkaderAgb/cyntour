<?php
/**
 * Unified Components for CynTour Application
 * 
 * This file provides backward compatibility with the new core system.
 * All functions are delegated to the new core classes.
 * 
 * @deprecated Use core/autoload.php directly for new code
 */

// Load the new autoloader which provides all legacy functions
require_once dirname(__DIR__) . '/core/autoload.php';

// Initialize the application
$app = \CynTour\Core\Application::getInstance();
$app->startSession();

// Legacy functions are now defined in core/autoload.php
// They are available globally after this file is included
