<?php
/**
 * Valenti Atelier - Global Configuration
 * Course Project: SEN 803 Software Technology
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Brand & Store Identity
define('BRAND_NAME', 'Valenti Atelier');
define('BRAND_SLOGAN', 'Artisanal Modern Tailoring & Contemporary Aesthetics');
define('CURRENCY_SYMBOL', '£');
define('CURRENCY_CODE', 'GBP');

// Business & Order Parameters
define('TAX_RATE', 0.075); // 7.5% Tax
define('FLAT_SHIPPING_RATE', 15.00);
define('FREE_SHIPPING_THRESHOLD', 150.00);

// Paths
define('BASE_PATH', dirname(__DIR__));

// Database Configuration: 'mysql' (Default for XAMPP) or 'sqlite'
define('DB_DRIVER', 'mysql');

// MySQL / MariaDB Settings (XAMPP Defaults)
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'valenti_atelier');
define('DB_USER', 'root');
define('DB_PASS', '');

// SQLite Portability Fallback Path
define('DB_SQLITE_PATH', BASE_PATH . '/database/valenti_atelier.db');
define('DB_PATH', DB_SQLITE_PATH); // Backwards compatibility

// Ensure database directory exists
if (!is_dir(BASE_PATH . '/database')) {
    mkdir(BASE_PATH . '/database', 0777, true);
}

// Ensure upload directory exists
if (!is_dir(BASE_PATH . '/assets/images/products')) {
    mkdir(BASE_PATH . '/assets/images/products', 0777, true);
}
