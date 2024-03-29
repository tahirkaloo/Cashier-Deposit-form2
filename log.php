<?php
// Include database connection parameters
require_once 'db_connect.php';

// Function to log actions
function logAction($action) {
    global $pdo; // Access the PDO connection object defined in db_connect.php
    
    // Check if the session is active
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    try {
        // Get user ID and name from session
        $userId = $_SESSION['user_id'] ?? null;
        $name = $_SESSION['name'] ?? 'Unknown';
        
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
