<?php
/**
 * Configuration Example file for Cyntour application
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to config.php
 * 2. Update the credentials below with your actual database settings
 * 3. Never commit config.php with real credentials to version control
 * 
 * Alternatively, set environment variables:
 * - DB_HOST: Database host (e.g., localhost)
 * - DB_NAME: Database name
 * - DB_USER: Database username
 * - DB_PASS: Database password
 */

// Database configuration
// Use environment variables if available, otherwise use defaults
$db_config = [
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_NAME') ?: 'your_database_name',
    'username' => getenv('DB_USER') ?: 'your_username',
    'password' => getenv('DB_PASS') ?: 'your_password',
    'charset'  => 'utf8mb4'
];

// DSN for PDO connection
$db_dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};charset={$db_config['charset']}";

// PDO options for better error handling and security
$db_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

/**
 * Get PDO database connection
 * @return PDO
 * @throws PDOException
 */
function getDbConnection() {
    global $db_dsn, $db_config, $db_options;
    
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO($db_dsn, $db_config['username'], $db_config['password'], $db_options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new PDOException('Database connection failed. Please check your configuration.');
        }
    }
    
    return $pdo;
}

/**
 * Get MySQLi database connection
 * @return mysqli
 * @throws Exception
 */
function getMysqliConnection() {
    global $db_config;
    
    static $conn = null;
    
    if ($conn === null) {
        $conn = new mysqli(
            $db_config['host'],
            $db_config['username'],
            $db_config['password'],
            $db_config['database']
        );
        
        if ($conn->connect_error) {
            error_log('Database connection failed: ' . $conn->connect_error);
            throw new Exception('Database connection failed. Please check your configuration.');
        }
        
        $conn->set_charset($db_config['charset']);
    }
    
    return $conn;
}
?>
