<?php
// log.php

// Function to log actions
function logAction($action) {
//Define the log file path
$logFile = '/var/www/html/actionlog.txt';
    
// Check if the session is active
if (session_status() === PHP_SESSION_NONE) {
session_start();
}
    
// Check if user ID and name are set in session
$userId = $_SESSION['user_id'] ?? 'Unknown';
$name = $_SESSION['name'] ?? 'Unknown';
    
// Get the current timestamp
$timestamp = date('Y-m-d H:i:s');
    
// Format the log entry
$logEntry = "{$timestamp} - User ID: {$userId} - Name: {$name} - Action: {$action}\n";
    
// Open or create the log file
$fileHandle = fopen($logFile, 'a');
    
// Check if fopen was successful
if ($fileHandle === false) {
// Log an error if fopen failed
error_log('Failed to open actionlog.txt for writing.');
return;
}
    
// Write the log entry to the file
$writeResult = fwrite($fileHandle, $logEntry);
    
// Check if fwrite was successful
if ($writeResult === false) {
error_log("Failed to write to actionlog.txt: {$logEntry}");
}
    
// Close the file
fclose($fileHandle);
}
