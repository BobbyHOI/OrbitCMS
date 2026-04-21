<?php
/**
 * OrbitCMS - Legacy SQL Support
 */
try {
    // Standardize to use constants from includes/config.php if available
    if (defined('DB_HOST')) {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    } else {
        // Fallback for standalone use
        $db = new mysqli("127.0.0.1", "root", "", "orbitcms");
    }
    
    if ($db->connect_error) {
        throw new Exception("Connection failed: " . $db->connect_error);
    }
} catch (Exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}
?>