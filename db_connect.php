<?php

// Database configuration (use environment variables)
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_password = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'deposits_portal';

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If connection fails, allow execution to continue so UI can be viewed (for verification)
    // In production, you might want to die() here, but for now we log it.
    error_log("Connection failed: " . $e->getMessage());
    // Do not echo the error to avoid breaking HTML output if it happens before header
}
?>