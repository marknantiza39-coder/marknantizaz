<?php
/**
 * Lunar Moon Jewelry - System Configuration
 * Use this file to manage API keys and Database connections.
 */

// --- PAYREX CONFIGURATION ---
// Toggle between 'sk_test_' for demo and 'sk_live_' for real (use test for now!)
define('PAYREX_SECRET_KEY', 'sk_test_561dVLcxUpkWik7YoDxgac8c6wToMo4p');

// --- DATABASE CONFIGURATION ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Default for XAMPP
define('DB_PASS', '');          // Default for XAMPP is empty
define('DB_NAME', 'lunar_registration'); // Replace with your actual database name

// --- SITE SETTINGS ---
define('SITE_URL', 'http://localhost/lunarmoon/');

/**
 * Function to connect to your database easily
 */
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }
    return $conn;
}
?>