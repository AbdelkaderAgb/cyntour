<?php
/**
 * Configuration file for Cyntour application
 * 
 * This file now loads the new core system while providing backward compatibility.
 * 
 * Database: barqvkxs_cyn
 * Username: barqvkxs_cyn
 */

// Load the new autoloader which handles everything
require_once __DIR__ . '/core/autoload.php';

// Database configuration - these variables are kept for backward compatibility
// but the actual config is managed by the Application class
$db_config = [
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_NAME') ?: 'barqvkxs_cyn',
    'username' => getenv('DB_USER') ?: 'barqvkxs_cyn',
    'password' => getenv('DB_PASS') ?: '_(Rd-R+{_y#?',
    'charset'  => 'utf8mb4'
];

// DSN for PDO connection (backward compatibility)
$db_dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};charset={$db_config['charset']}";

// PDO options for better error handling and security
$db_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

/**
 * Legacy function - Check if tables exist and initialize database if needed
 * @deprecated Use Application::getInstance()->getMysqli() instead
 * @param mysqli $conn
 * @return bool True if tables exist or were created successfully
 */
function initializeDatabaseTables($conn) {
    // This is now handled by the Application class
    return true;
}

/**
 * Get PDO database connection
 * @deprecated Use Application::getInstance()->getPdo() instead
 * @return PDO
 * @throws PDOException
 */
if (!function_exists('getDbConnection')) {
    function getDbConnection() {
        return \CynTour\Core\Application::getInstance()->getPdo();
    }
}

/**
 * Get MySQLi database connection
 * @deprecated Use Application::getInstance()->getMysqli() instead
 * @return mysqli
 * @throws Exception
 */
if (!function_exists('getMysqliConnection')) {
    function getMysqliConnection() {
        return \CynTour\Core\Application::getInstance()->getMysqli();
    }
}
?>
