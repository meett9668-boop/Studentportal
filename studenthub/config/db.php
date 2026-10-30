<?php
declare(strict_types=1);

// Application Configuration Settings
define('APP_NAME', 'Student Hub');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/studenthub');

// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'studenthub_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a singleton instance of the PDO database connection.
 *
 * @return PDO
 * @throws PDOException
 */
function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database Connection Failed: ' . $e->getMessage());
            // Show friendly message rather than revealing database credentials
            die('A database error occurred. Please ensure MySQL is running and the database is imported.');
        }
    }

    return $pdo;
}

// Global PDO shortcut for backward compatibility
$pdo = getDbConnection();

