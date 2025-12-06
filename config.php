<?php
// config.php - Configuration and Demo Mode detection

// Default Database Credentials
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_password = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'deposits_portal';

// Attempt to connect to MySQL to see if it's available
// We use mysqli for this check as it's simple
mysqli_report(MYSQLI_REPORT_OFF); // Disable exceptions for this check
$test_conn = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

if ($test_conn) {
    define('DEMO_MODE', false);
    mysqli_close($test_conn);
} else {
    // Connection failed, enable Demo Mode
    define('DEMO_MODE', true);
}

// Helper function to show Demo Banner
function showDemoBanner() {
    if (defined('DEMO_MODE') && DEMO_MODE) {
        echo '<div class="alert alert-warning text-center m-0 fw-bold" role="alert" style="border-radius: 0;">
                <i class="fas fa-exclamation-triangle me-2"></i> Demo Mode: Database Connection Failed. Showing Mock Data.
              </div>';
    }
}
?>
