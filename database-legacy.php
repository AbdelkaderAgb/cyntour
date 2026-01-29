<?php
/**
 * Database connection using PDO
 * This file provides a PDO connection for database operations.
 */

require_once __DIR__ . '/config.php';

try {
    $conn = getDbConnection();
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
