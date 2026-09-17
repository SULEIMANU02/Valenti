<?php
/**
 * Valenti Atelier - Database Connection (PDO)
 * Supports MySQL / MariaDB (XAMPP default) with automatic setup and SQLite fallback.
 */

require_once __DIR__ . '/config.php';

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $driver = defined('DB_DRIVER') ? strtolower(DB_DRIVER) : 'mysql';

    if ($driver === 'mysql') {
        try {
            $pdo = getMySQLConnection();
            return $pdo;
        } catch (PDOException $e) {
            error_log("Valenti Atelier: MySQL connection failed (" . $e->getMessage() . "). Falling back to SQLite.");
            // If MySQL is not running or fails, fallback to SQLite
            $pdo = getSQLiteConnection();
            return $pdo;
        }
    }

    $pdo = getSQLiteConnection();
    return $pdo;
}

function getMySQLConnection(): PDO {
    $host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
    $port = defined('DB_PORT') ? DB_PORT : '3306';
    $dbName = defined('DB_NAME') ? DB_NAME : 'valenti_atelier';
    $user = defined('DB_USER') ? DB_USER : 'root';
    $pass = defined('DB_PASS') ? DB_PASS : '';

    // Step 1: Connect to MySQL server to ensure the database exists
    $serverDsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $serverPdo = new PDO($serverDsn, $user, $pass, $options);
    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

    // Step 2: Connect to specific database
    $dbDsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
    $options[PDO::MYSQL_ATTR_MULTI_STATEMENTS] = true;
    $pdo = new PDO($dbDsn, $user, $pass, $options);

    // Step 3: Check if tables exist
    $checkStmt = $pdo->query("SHOW TABLES LIKE 'products'");
    $hasTables = $checkStmt->fetch();

    if (!$hasTables) {
        initMySQLDatabase($pdo);
    }

    return $pdo;
}

function initMySQLDatabase(PDO $pdo): void {
    $schemaFile = BASE_PATH . '/database/schema_mysql.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        $pdo->exec($sql);

        // Auto-seed initial data
        $seedFile = BASE_PATH . '/database/seed.php';
        if (file_exists($seedFile)) {
            require_once $seedFile;
            seedDatabase($pdo);
        }
    }
}

function getSQLiteConnection(): PDO {
    $dbFile = defined('DB_SQLITE_PATH') ? DB_SQLITE_PATH : BASE_PATH . '/database/valenti_atelier.db';
    $needsInit = !file_exists($dbFile) || filesize($dbFile) === 0;

    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON;');

    if ($needsInit) {
        initSQLiteDatabase($pdo);
    }

    return $pdo;
}

function initSQLiteDatabase(PDO $pdo): void {
    $schemaFile = BASE_PATH . '/database/schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        $pdo->exec($sql);

        // Auto-seed initial data
        $seedFile = BASE_PATH . '/database/seed.php';
        if (file_exists($seedFile)) {
            require_once $seedFile;
            seedDatabase($pdo);
        }
    }
}

// Backwards compatibility alias
function initDatabase(PDO $pdo): void {
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
        initMySQLDatabase($pdo);
    } else {
        initSQLiteDatabase($pdo);
    }
}
