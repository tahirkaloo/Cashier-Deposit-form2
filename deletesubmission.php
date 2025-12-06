<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the table name is provided in the URL
if (!isset($_GET['table'])) {
    die("Table name not provided.");
}

// Check if the submission ID is provided in the URL
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $submission_id = $_GET['id'];
    $table = $_GET['table']; // Get the table name from the URL

    // Basic sanitization for table name to prevent SQL injection if used in query
    // Although in Demo Mode we don't query, it's good practice.
    $allowed_tables = ['cashierdeposit', 'coinexchange', 'users']; // Add allowed tables
    if (!in_array($table, $allowed_tables)) {
        die("Invalid table name.");
    }

    if (defined('DEMO_MODE') && DEMO_MODE) {
        // --- DEMO MODE ---
        sleep(1);
        // Redirect back to the previous page
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
             // Fallback
            if ($table === 'coinexchange') {
                header('Location: coinexchange.php');
            } else {
                header('Location: supervisor.php');
            }
        }
        exit;
    } else {
        // --- REAL DATABASE LOGIC ---
        $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Use prepared statement to delete the submission from the specified table
        // Note: Table name cannot be bound in prepared statement, hence the whitelist check above is crucial.
        $sql = "DELETE FROM $table WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $submission_id); // Assuming 'id' is an integer
            if (mysqli_stmt_execute($stmt)) {
                // Record deleted successfully
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                echo "Error executing statement: " . mysqli_stmt_error($stmt);
            }
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
} else {
    echo "Invalid submission ID.";
}
?>
