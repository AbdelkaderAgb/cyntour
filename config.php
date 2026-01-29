<?php
/**
 * Configuration file for Cyntour application
 * 
 * This file contains database credentials and connection helpers.
 * Tables are automatically created if they don't exist.
 * 
 * Database: barqvkxs_cyn
 * Username: barqvkxs_cyn
 */

// Database configuration
$db_config = [
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_NAME') ?: 'barqvkxs_cyn',
    'username' => getenv('DB_USER') ?: 'barqvkxs_cyn',
    'password' => getenv('DB_PASS') ?: '_(Rd-R+{_y#?',
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
 * Check if tables exist and initialize database if needed
 * Uses file-based locking to prevent race conditions
 * @param mysqli $conn
 * @return bool True if tables exist or were created successfully
 */
function initializeDatabaseTables($conn) {
    // Check if the users table exists (if it does, database is already initialized)
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result && $result->num_rows > 0) {
        return true; // Tables already exist
    }
    
    // Use file-based locking to prevent concurrent initialization
    $lockFile = sys_get_temp_dir() . '/cyntour_db_init.lock';
    $fp = fopen($lockFile, 'c');
    
    if (!flock($fp, LOCK_EX | LOCK_NB)) {
        // Another process is initializing, wait for it
        flock($fp, LOCK_EX);
        fclose($fp);
        // Recheck if tables were created by another process
        $result = $conn->query("SHOW TABLES LIKE 'users'");
        return ($result && $result->num_rows > 0);
    }
    
    try {
        // Double-check after acquiring lock
        $result = $conn->query("SHOW TABLES LIKE 'users'");
        if ($result && $result->num_rows > 0) {
            flock($fp, LOCK_UN);
            fclose($fp);
            return true;
        }
        
        // Get the schema file path
        $schemaFile = __DIR__ . '/database/schema.sql';
        
        if (!file_exists($schemaFile)) {
            error_log('Database schema file not found: ' . $schemaFile);
            flock($fp, LOCK_UN);
            fclose($fp);
            return false;
        }
        
        // Read schema
        $schema = file_get_contents($schemaFile);
        
        // Remove the CREATE DATABASE and USE statements since we're already connected
        $schema = preg_replace('/^--.*$/m', '', $schema); // Remove single-line comments
        $schema = preg_replace('/CREATE\s+DATABASE.*?;/is', '', $schema);
        $schema = preg_replace('/USE\s+[\w_]+\s*;/is', '', $schema);
        
        // Use mysqli_multi_query for better handling of multiple statements
        if ($conn->multi_query($schema)) {
            // Process all result sets
            do {
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->more_results() && $conn->next_result());
            
            // Check for errors in multi_query execution
            if ($conn->errno) {
                throw new Exception('SQL execution error: ' . $conn->error);
            }
            
            error_log('Database tables created successfully');
            flock($fp, LOCK_UN);
            fclose($fp);
            return true;
        } else {
            throw new Exception('Failed to execute schema: ' . $conn->error);
        }
        
    } catch (Exception $e) {
        error_log('Error creating database tables: ' . $e->getMessage());
        flock($fp, LOCK_UN);
        fclose($fp);
        return false;
    }
}

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
 * Tables are automatically created if they don't exist.
 * @return mysqli
 * @throws Exception
 */
function getMysqliConnection() {
    global $db_config;
    
    static $conn = null;
    static $initialized = false;
    
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
        
        // Initialize database tables if needed (only once per process)
        if (!$initialized) {
            initializeDatabaseTables($conn);
            $initialized = true;
        }
    }
    
    return $conn;
}
?>
