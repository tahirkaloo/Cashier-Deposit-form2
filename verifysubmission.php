<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
        $submission_id = $_GET['id'];
        
        echo "Submission ID: " . $submission_id; // Debug statement
        
        // Use prepared statement to verify the submission
        $sql = "UPDATE cashierdeposit SET verified = 1 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $submission_id); // Assuming 'id' is an integer
            if (mysqli_stmt_execute($stmt)) {
                // Record verified successfully
                header("Location: supervisor.php");
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
}