<?php
require_once __DIR__ . '/config.php';

/**
 * Get PDO Database Connection
 * @return PDO
 */
function get_db_connection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In case the DB does not exist yet (e.g. during initial setup), we connect to the server without DB name
            if ($e->getCode() == 1049) { // Unknown database error code
                try {
                    $dsnWithoutDb = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
                    $pdo = new PDO($dsnWithoutDb, DB_USER, DB_PASS, $options);
                    return $pdo;
                } catch (PDOException $ex) {
                    die("Database connection failed: " . $ex->getMessage());
                }
            }
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}
?>
