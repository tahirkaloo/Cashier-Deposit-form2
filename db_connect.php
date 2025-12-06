<?php
require_once 'config.php';

// If we are NOT in demo mode, establish the connections normally
if (!DEMO_MODE) {
    try {
        // Create a PDO instance
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
        // Set PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        // This shouldn't happen if config.php check passed, but just in case
        error_log("Connection failed: " . $e->getMessage());
    }
} else {
    // In Demo Mode, $pdo is null or a dummy if needed.
    // We will handle checks in individual files.
    $pdo = null;
}
?>
