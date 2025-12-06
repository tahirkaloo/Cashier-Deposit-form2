<?php
// Include database connection parameters
require_once 'db_connect.php';

// Function to log actions
function logAction($action) {
    global $pdo; // Access the PDO connection object defined in db_connect.php

    // Skip logging if in Demo Mode or if PDO is not available or not a valid object
    if ((defined('DEMO_MODE') && DEMO_MODE) || !isset($pdo) || !($pdo instanceof PDO)) {
        return;
    }

    try {
        // Get user ID and name from session (Session should be started by the parent page)
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown';

        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO logs (user_id, name, action, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");

        // Bind parameters and execute
        $stmt->execute([$userId, $name, $action]);
    } catch(PDOException $e) {
        // If connection fails, display error message
        error_log("Connection failed: " . $e->getMessage());
    }
}
?>
