<?php
// log.php

// Function to log actions
function logAction($action) {
    $logFile = 'action_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Unknown'; // Assuming you have user authentication
    $name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown';

    // Format the log entry
    $logEntry = "$timestamp - User ID: $userId - Name: $name - Action: $action\n";

    // Open or create the log file
    $fileHandle = fopen($logFile, 'a');

    // Write the log entry to the file
    fwrite($fileHandle, $logEntry);

    // Close the file
    fclose($fileHandle);
}