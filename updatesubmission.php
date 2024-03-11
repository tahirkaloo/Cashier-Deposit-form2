<?php
session_start();
require_once 'db_connect.php';

$error_message = ''; // Initialize error message variable

// Check if the user is logged in and is a supervisor or admin
if (!isset($_SESSION['user_id'])) {
    // User is not logged in
    $error_message = "You need to login to update submissions.";
} elseif ($_SESSION['role'] !== 'supervisor' && $_SESSION['role'] !== 'admin') {
    // User is not a supervisor or admin
    $error_message = "You are not authorized to update submissions.";
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // User is authorized and the form has been submitted

    // Retrieve form data
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $depositType = $_POST['DepositType'];
    $cashAmount = $_POST['cash'];
    $check21DepositAmount = $_POST['check21DepositAmount'];
    $check21DepositCount = $_POST['check21DepositCount'];
    $ceoCheckDepositAmount = $_POST['ceoCheckDepositAmount'];
    $ceoCheckDepositCount = $_POST['ceoCheckDepositCount'];
    $manualCheckDepositAmount = $_POST['manualCheckDepositAmount'];
    $manualCheckDepositCount = $_POST['manualCheckDepositCount'];
    $moneyOrderDepositAmount = $_POST['moneyOrderDepositAmount'];
    $moneyOrderDepositCount = $_POST['moneyOrderDepositCount'];
    $creditDebitCardAmount = $_POST['creditDebitCardAmount'];
    $creditDebitCardCount = $_POST['creditDebitCardCount'];
    $preDepositAmount = $_POST['preDepositAmount'];
    $preDepositCount = $_POST['preDepositCount'];

    // Calculate total amount and total count
    $totalAmount = $cashAmount + $check21DepositAmount + $ceoCheckDepositAmount + $manualCheckDepositAmount + $moneyOrderDepositAmount + $creditDebitCardAmount + $preDepositAmount;
    $totalCount = $check21DepositCount + $ceoCheckDepositCount + $manualCheckDepositCount + $moneyOrderDepositCount + $creditDebitCardCount + $preDepositCount;

    // Update the submission in the database
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "UPDATE cashierdeposit SET deposit_type=?, cash_amount=?, check21_deposit_amount=?, check21_deposit_count=?, ceo_check_deposit_amount=?, ceo_check_deposit_count=?, manual_check_deposit_amount=?, manual_check_deposit_count=?, money_order_deposit_amount=?, money_order_deposit_count=?, credit_debit_cards_amount=?, credit_debit_cards_count=?, pre_deposit_amount=?, pre_deposit_count=?, total_amount=?, total_count=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sdddddddddddddddi", $depositType, $cashAmount, $check21DepositAmount, $check21DepositCount, $ceoCheckDepositAmount, $ceoCheckDepositCount, $manualCheckDepositAmount, $manualCheckDepositCount, $moneyOrderDepositAmount, $moneyOrderDepositCount, $creditDebitCardAmount, $creditDebitCardCount, $preDepositAmount, $preDepositCount, $totalAmount, $totalCount, $id);

    if (mysqli_stmt_execute($stmt)) {
        // Submission updated successfully
        header("Location: viewsubmission.php?id=$id"); // Redirect to a page to view submissions
        exit();
    } else {
        $error_message = "Error updating submission: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

// If there's an error message, display it
if (!empty($error_message)) {
    echo "<p>Error: $error_message</p>";
}