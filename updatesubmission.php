<?php
session_start();
require_once 'db_connect.php';
// Include the logger.php file
require_once 'log.php';

// Call the logAction() function as needed in your code
logAction('Update Submission');

$error_message = ''; // Initialize error message variable

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $error_message = "You need to login to update submissions.";
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check authorization (logic copied from original, simplified)
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
    if ($role !== 'supervisor' && $role !== 'admin' && $role !== 'user') {
         $error_message = "You are not authorized to update submissions.";
    } else {
        // Retrieve form data
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $depositType = $_POST['DepositType'];
        $cashAmount = floatval($_POST['cash']);
        $check21DepositAmount = floatval($_POST['check21DepositAmount']);
        $check21DepositCount = intval($_POST['check21DepositCount']);
        $ceoCheckDepositAmount = floatval($_POST['ceoCheckDepositAmount']);
        $ceoCheckDepositCount = intval($_POST['ceoCheckDepositCount']);
        $manualCheckDepositAmount = floatval($_POST['manualCheckDepositAmount']);
        $manualCheckDepositCount = intval($_POST['manualCheckDepositCount']);
        $moneyOrderDepositAmount = floatval($_POST['moneyOrderDepositAmount']);
        $moneyOrderDepositCount = intval($_POST['moneyOrderDepositCount']);
        $creditDebitCardAmount = floatval($_POST['creditDebitCardAmount']);
        $creditDebitCardCount = intval($_POST['creditDebitCardCount']);
        $preDepositAmount = floatval($_POST['preDepositAmount']);
        $preDepositCount = intval($_POST['preDepositCount']);

        // Calculate total amount and total count
        $totalAmount = $cashAmount + $check21DepositAmount + $ceoCheckDepositAmount + $manualCheckDepositAmount + $moneyOrderDepositAmount + $creditDebitCardAmount + $preDepositAmount;
        $totalCount = $check21DepositCount + $ceoCheckDepositCount + $manualCheckDepositCount + $moneyOrderDepositCount + $creditDebitCardCount + $preDepositCount;

        if (defined('DEMO_MODE') && DEMO_MODE) {
            // --- DEMO MODE ---
            logAction('Update Submission ID (Demo): ' . $id . ' with Total Amount: ' . $totalAmount);
            sleep(1);
            header("Location: viewsubmission.php?id=$id");
            exit();
        } else {
            // --- REAL DATABASE LOGIC ---
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $sql = "UPDATE cashierdeposit SET deposit_type=?, cash_amount=?, check21_deposit_amount=?, check21_deposit_count=?, ceo_check_deposit_amount=?, ceo_check_deposit_count=?, manual_check_deposit_amount=?, manual_check_deposit_count=?, money_order_deposit_amount=?, money_order_deposit_count=?, credit_debit_cards_amount=?, credit_debit_cards_count=?, pre_deposit_amount=?, pre_deposit_count=?, total_amount=?, total_count=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sdddddddddddddddi", $depositType, $cashAmount, $check21DepositAmount, $check21DepositCount, $ceoCheckDepositAmount, $ceoCheckDepositCount, $manualCheckDepositAmount, $manualCheckDepositCount, $moneyOrderDepositAmount, $moneyOrderDepositCount, $creditDebitCardAmount, $creditDebitCardCount, $preDepositAmount, $preDepositCount, $totalAmount, $totalCount, $id);

            if (mysqli_stmt_execute($stmt)) {
                // Submission updated successfully
                logAction('Update Submission ID: ' . $id . ' with Total Amount: ' . $totalAmount . ' and Total Count: ' . $totalCount);
                header("Location: viewsubmission.php?id=$id"); // Redirect to a page to view submissions
                exit();
            } else {
                $error_message = "Error updating submission: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        }
    }
}

// If there's an error message, display it
if (!empty($error_message)) {
    echo "<div style='text-align: center; margin-top: 50px; margin-bottom: 50px;'>";
    echo "<p style='color: red; font-weight: bold; font-size: 24px; font-family: Arial, sans-serif;'>";
    echo "Error: $error_message";
    echo "</p>";
    echo "<a href='index.php'>Return to Dashboard</a>";
    echo "</div>";
}
?>
