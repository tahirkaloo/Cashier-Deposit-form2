<?php
session_start();
require_once 'db_connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

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

                // Example SQL update query:

                $updateSql = "UPDATE cashierdeposit SET deposit_type=?, cash_amount=?, check21_deposit_amount=?, check21_deposit_count=?, ceo_check_deposit_amount=?, ceo_check_deposit_count=?, manual_check_deposit_amount=?, manual_check_deposit_count=?, money_order_deposit_amount=?, money_order_deposit_count=?, credit_debit_cards_amount=?, credit_debit_cards_count=?, pre_deposit_amount=?, pre_deposit_count=?, total_amount=?, total_count=? WHERE id=?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                
                // Modify the type definition string to match the number of placeholders
                mysqli_stmt_bind_param($updateStmt, 'sdddddddddddddddssi', $_POST['DepositType'], $_POST['cash'], $_POST['check21DepositAmount'], $_POST['check21DepositCount'], $_POST['ceoCheckDepositAmount'], $_POST['ceoCheckDepositCount'], $_POST['manualCheckDepositAmount'], $_POST['manualCheckDepositCount'], $_POST['moneyOrderDepositAmount'], $_POST['moneyOrderDepositCount'], $_POST['creditDebitCardAmount'], $_POST['creditDebitCardCount'], $_POST['preDepositAmount'], $_POST['preDepositCount'], $_POST['totalAmount'], $_POST['totalCount'], $_POST['id']);
                  





                mysqli_stmt_execute($updateStmt);


                // Handle success or error message after updating the record
                if (mysqli_stmt_affected_rows($updateStmt) > 0) {
                    $success_message = "Record updated successfully.";
                } else {
                    $error_message = "Error updating record: " . mysqli_error($conn);
                }

                // Close prepared statement
                mysqli_stmt_close($updateStmt);
            } else {
                // Handle the case when 'total_amount' is not provided in the form
                $error_message = "Total amount is required.";
            }
        } else {
            $error_message = "Invalid submission ID.";
        }
    } else {
        $error_message = "Invalid request method.";
    }
}

// Redirect back to the edit page with appropriate messages
header("Location: editsubmission.php?id=$id&success_message=" . urlencode($success_message) . "&error_message=" . urlencode($error_message));
exit;
