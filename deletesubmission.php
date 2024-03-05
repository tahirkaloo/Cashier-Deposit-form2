<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the user is a supervisor or admin
$isAdmin = ($_SESSION['role'] === 'admin');
$isSupervisor = ($_SESSION['role'] === 'supervisor');

if (!$isAdmin && !$isSupervisor) {
    header("Location: accessdenied.html");
    exit;
}

// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the submission ID is provided in the URL
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $submission_id = $_GET['id'];
    $table = $_GET['table']; // Get the table name from the URL

    // Use prepared statement to delete the submission from the specified table
    $sql = "DELETE FROM $table WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $submission_id); // Assuming 'id' is an integer
        if (mysqli_stmt_execute($stmt)) {
            // Record deleted successfully
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt); // Debug statement
        }
    } else {
        echo "Error preparing statement: " . mysqli_error($conn); // Debug statement
    }
} else {
    echo "Invalid submission ID.";
}

mysqli_close($conn);
