<?php
/**
 * CynTour Application Core
 * 
 * Main application bootstrap class that handles configuration,
 * database connections, and core functionality.
 * 
 * @package CynTour
 * @version 2.0
 */

namespace CynTour\Core;

class Application
{
    /** @var Application Singleton instance */
    private static ?Application $instance = null;
    
    /** @var array Configuration settings */
    private array $config = [];
    
    /** @var \PDO|null PDO connection */
    private ?\PDO $pdo = null;
    
    /** @var \mysqli|null MySQLi connection */
    private ?\mysqli $mysqli = null;
    
    /** @var bool Database initialized flag */
    private bool $dbInitialized = false;
    
    /** @var string Base path of application */
    private string $basePath;
    
    /**
     * Private constructor for singleton pattern
     */
    private function __construct()
    {
        $this->basePath = dirname(__DIR__);
        $this->loadConfig();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load configuration from environment or defaults
     */
    private function loadConfig(): void
    {
        $this->config = [
            'db' => [
                'host'     => getenv('DB_HOST') ?: 'localhost',
                'database' => getenv('DB_NAME') ?: 'barqvkxs_cyn',
                'username' => getenv('DB_USER') ?: 'barqvkxs_cyn',
                'password' => getenv('DB_PASS') ?: '_(Rd-R+{_y#?',
                'charset'  => 'utf8mb4'
            ],
            'app' => [
                'name'    => 'CynTour',
                'version' => '2.0',
                'debug'   => getenv('APP_DEBUG') ?: false,
                'url'     => getenv('APP_URL') ?: ''
            ],
            'session' => [
                'timeout' => 30 * 60 // 30 minutes
            ]
        ];
    }
    
    /**
     * Get configuration value
     */
    public function config(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    /**
     * Get base path
     */
    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
    
    /**
     * Get PDO database connection
     * 
     * @return \PDO
     * @throws \PDOException
     */
    public function getPdo(): \PDO
    {
        if ($this->pdo === null) {
            $db = $this->config['db'];
            $dsn = "mysql:host={$db['host']};dbname={$db['database']};charset={$db['charset']}";
            
            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            try {
                $this->pdo = new \PDO($dsn, $db['username'], $db['password'], $options);
            } catch (\PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                throw new \PDOException('Database connection failed. Please check your configuration.');
            }
            
            // Initialize database tables if needed
            $this->getMysqli();
        }
        
        return $this->pdo;
    }
    
    /**
     * Get MySQLi database connection
     * 
     * @return \mysqli
     * @throws \Exception
     */
    public function getMysqli(): \mysqli
    {
        if ($this->mysqli === null) {
            $db = $this->config['db'];
            
            $this->mysqli = new \mysqli(
                $db['host'],
                $db['username'],
                $db['password'],
                $db['database']
            );
            
            if ($this->mysqli->connect_error) {
                error_log('Database connection failed: ' . $this->mysqli->connect_error);
                throw new \Exception('Database connection failed. Please check your configuration.');
            }
            
            $this->mysqli->set_charset($db['charset']);
            
            // Initialize database tables if needed
            if (!$this->dbInitialized) {
                $this->initializeDatabaseTables();
                $this->dbInitialized = true;
            }
        }
        
        return $this->mysqli;
    }
    
    /**
     * Check if tables exist and initialize database if needed
     * Uses file-based locking to prevent race conditions
     * 
     * @return bool True if tables exist or were created successfully
     */
    private function initializeDatabaseTables(): bool
    {
        // Check if the users table exists (if it does, database is already initialized)
        $result = $this->mysqli->query("SHOW TABLES LIKE 'users'");
        if ($result && $result->num_rows > 0) {
            return true;
        }
        
        // Use file-based locking to prevent concurrent initialization
        $lockFile = sys_get_temp_dir() . '/cyntour_db_init.lock';
        $fp = fopen($lockFile, 'c');
        
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            // Another process is initializing, wait for it
            flock($fp, LOCK_EX);
            fclose($fp);
            // Recheck if tables were created by another process
            $result = $this->mysqli->query("SHOW TABLES LIKE 'users'");
            return ($result && $result->num_rows > 0);
        }
        
        try {
            // Double-check after acquiring lock
            $result = $this->mysqli->query("SHOW TABLES LIKE 'users'");
            if ($result && $result->num_rows > 0) {
                flock($fp, LOCK_UN);
                fclose($fp);
                return true;
            }
            
            // Get the schema file path
            $schemaFile = $this->basePath('database/schema.sql');
            
            if (!file_exists($schemaFile)) {
                error_log('Database schema file not found: ' . $schemaFile);
                flock($fp, LOCK_UN);
                fclose($fp);
                return false;
            }
            
            // Read schema
            $schema = file_get_contents($schemaFile);
            
            // Remove the CREATE DATABASE and USE statements
            $schema = preg_replace('/^--.*$/m', '', $schema);
            $schema = preg_replace('/CREATE\s+DATABASE.*?;/is', '', $schema);
            $schema = preg_replace('/USE\s+[\w_]+\s*;/is', '', $schema);
            
            // Use mysqli_multi_query for better handling of multiple statements
            if ($this->mysqli->multi_query($schema)) {
                // Process all result sets
                do {
                    if ($result = $this->mysqli->store_result()) {
                        $result->free();
                    }
                } while ($this->mysqli->more_results() && $this->mysqli->next_result());
                
                // Check for errors
                if ($this->mysqli->errno) {
                    throw new \Exception('SQL execution error: ' . $this->mysqli->error);
                }
                
                error_log('Database tables created successfully');
                flock($fp, LOCK_UN);
                fclose($fp);
                return true;
            } else {
                throw new \Exception('Failed to execute schema: ' . $this->mysqli->error);
            }
            
        } catch (\Exception $e) {
            error_log('Error creating database tables: ' . $e->getMessage());
            flock($fp, LOCK_UN);
            fclose($fp);
            return false;
        }
    }
    
    /**
     * Start session with proper configuration
     */
    public function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
            return true;
        }
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            return true;
        }
        return false;
    }
    
    /**
     * Get current user data
     */
    public function getUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Get display name for current user
     */
    public function getDisplayName(): string
    {
        if (isset($_SESSION['user']['first_name'])) {
            return htmlspecialchars($_SESSION['user']['first_name']);
        }
        if (isset($_SESSION['username'])) {
            return htmlspecialchars($_SESSION['username']);
        }
        return 'User';
    }
}
