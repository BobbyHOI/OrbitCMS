<?php 
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..');
$dotenv->load();

// GEO API Configuration
define('GEO_API_KEY', $_SERVER['GEO_API_KEY']);
define('GEO_API_URL', $_SERVER['GEO_API_URL']);

// Application Constants
if (!defined('BASE_URL')) {
    define('BASE_URL', $_SERVER['BASE_URL']);
}

// Database Configuration
define('DB_HOST', $_SERVER['DB_HOST']);
define('DB_USER', $_SERVER['DB_USER']);
define('DB_PASS', $_SERVER['DB_PASS']);
define('DB_NAME', $_SERVER['DB_NAME']);

// API Configuration for Calendarific
define('CALENDARIFIC_API_KEY', $_SERVER['CALENDARIFIC_API_KEY']);
define('CALENDARIFIC_API_URL', $_SERVER['CALENDARIFIC_API_URL']);

// SMTP Configuration
define('SMTP_HOST', $_SERVER['SMTP_HOST']);
define('SMTP_USERNAME', $_SERVER['SMTP_USERNAME']);
define('SMTP_PASSWORD', $_SERVER['SMTP_PASSWORD']);
define('SMTP_PORT', (int)$_SERVER['SMTP_PORT']);
define('SMTP_FROM_EMAIL', $_SERVER['SMTP_FROM_EMAIL']);
define('SMTP_FROM_NAME', $_SERVER['SMTP_FROM_NAME']);

// Establish database connection.
try {
    if (!isset($dbh)) {
        $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
    }
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}
?>