<?php
// Database configuration
$db_host = "127.0.0.1";
$db_user = "root";
$db_password = "11559933tk";
$db_name = "depositapplicationdb";

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If connection fails, display error message
    echo "Connection failed: " . $e->getMessage();
}

