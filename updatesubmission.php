<?php
session_start();
require_once 'db_connect.php';

$error_message = ''; // Initialize error message variable
$success_message = ''; // Initialize success message variable

// Check if the user is logged in and is a supervisor or admin
if (!isset($_SESSION['user_id'])) {
    // User is not logged in
    $error_message = "You need to login to update submissions.";
} elseif ($_SESSION['role'] !== 'supervisor' && $_SESSION['role'] !== 'admin') {
    // User is not a supervisor or admin
    $error_message = "You are not authorized to update submissions.";
} else {
    // User is authorized, proceed with database connection and submission update
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if ID is set and valid
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
            $id = $_POST['id'];

            // Fetch submission details from the form
            // Example:
            // $depositType = $_POST['DepositType'];
            // $cashAmount = $_POST['cash'];
            // $check21DepositAmount = $_POST['check21DepositAmount'];
            // $check21DepositCount = $_POST['check21DepositCount'];

            // Validate and sanitize form inputs if needed


            // Update the record in the database
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Sanitize and validate form inputs
            if (isset($_POST['DepositType']) && !empty($_POST['DepositType'])) {
                $depositType = $_POST['DepositType'];
            } else {
                // Handle the case when 'deposit_type' is not provided in the form
                $error_message = "Deposit type is required.";
            }

            // Check if total amount is provided
            if (isset($_POST['totalAmount']) && !empty($_POST['totalAmount'])) {
                $totalAmount = $_POST['totalAmount'];
            } else {
                // Handle the case when 'total_amount' is not provided in the form
                $error_message = "Total amount is required.";
            }

            // Example SQL update query:
            $updateSql = "UPDATE cashierdeposit SET deposit_type=?, total_amount=? WHERE id=?";
            $updateStmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "sii", $depositType, $totalAmount, $id);

            mysqli_stmt_execute($updateStmt);

            // Handle success or error message after updating the record
            if (mysqli_stmt_affected_rows($updateStmt) > 0) {
                $success_message = "Record updated successfully.";
            } else {
                $error_message = "Error updating record: " . mysqli_error($conn);
            }

            // Close prepared statement
            mysqli_stmt_close($updateStmt);
            mysqli_close($conn);
        } else {
            $error_message = "Invalid submission ID.";
        }
    } else {
        $error_message = "Invalid request method.";
    }
}

// If there's an error message, display it
if (!empty($error_message)) {
    echo "<p>Error: $error_message</p>";
}

// If there's a success message, display it
if (!empty($success_message)) {
    echo "<p>Success: $success_message</p>";
}
