<?php
/**
 * Database connection using PDO
 * This file provides a PDO connection for database operations.
 */

require_once __DIR__ . '/config.php';

try {
    $pdo = getDbConnection();
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}
?>
