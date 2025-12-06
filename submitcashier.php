<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Set the timezone
date_default_timezone_set('America/New_York');

// Assign form values to variables
$deposittype = isset($_POST['DepositType']) ? $_POST['DepositType'] : null;
$drawerNumber = isset($_POST['DrawerNumber']) ? $_POST['DrawerNumber'] : null;
$username = isset($_POST['username']) ? $_POST['username'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$cashAmount = isset($_POST['Cash']) ? floatval($_POST['Cash']) : 0.0;
$check21DepositAmount = isset($_POST['Check21DepositAmount']) ? floatval($_POST['Check21DepositAmount']) : 0.0;
$check21DepositCount = isset($_POST['Check21DepositCount']) ? intval($_POST['Check21DepositCount']) : 0;
$ceoCheckDepositAmount = isset($_POST['CEOCheckDepositAmount']) ? floatval($_POST['CEOCheckDepositAmount']) : 0.0;
$ceoCheckDepositCount = isset($_POST['CEOCheckDepositCount']) ? intval($_POST['CEOCheckDepositCount']) : 0;
$manualCheckDepositAmount = isset($_POST['ManualCheckDepositAmount']) ? floatval($_POST['ManualCheckDepositAmount']) : 0.0;
$manualCheckDepositCount = isset($_POST['ManualCheckDepositCount']) ? intval($_POST['ManualCheckDepositCount']) : 0;
$moneyOrderAmount = isset($_POST['MoneyOrderAmount']) ? floatval($_POST['MoneyOrderAmount']) : 0.0;
$moneyOrderCount = isset($_POST['MoneyOrderCount']) ? intval($_POST['MoneyOrderCount']) : 0;
$creditDebitCardsAmount = isset($_POST['CreditDebitCardsAmount']) ? floatval($_POST['CreditDebitCardsAmount']) : 0.0;
$creditDebitCardsCount = isset($_POST['CreditDebitCardsCount']) ? intval($_POST['CreditDebitCardsCount']) : 0;
$preDepositsAmount = isset($_POST['PreDepositsAmount']) ? floatval($_POST['PreDepositsAmount']) : 0.0;
$preDepositsCount = isset($_POST['PreDepositsCount']) ? intval($_POST['PreDepositsCount']) : 0;

// Calculate total amount
$totalamount = $cashAmount + $check21DepositAmount + $ceoCheckDepositAmount + $manualCheckDepositAmount + $moneyOrderAmount + $creditDebitCardsAmount + $preDepositsAmount;

// Calculate total count
$totalcount = $check21DepositCount + $ceoCheckDepositCount + $manualCheckDepositCount + $moneyOrderCount + $creditDebitCardsCount + $preDepositsCount;

if (defined('DEMO_MODE') && DEMO_MODE) {
    // --- DEMO MODE ---
    // Simulate insertion delay
    sleep(1);
    $mockId = 999;

    // In a real app we might store this in session to display it on the next page,
    // but for now we just redirect to the view page which will show static mock data.

    // Redirect back to the cashier deposit page (view)
    header("Location: viewsubmission.php?id=" . $mockId);
    exit();

} else {
    // --- REAL DATABASE LOGIC ---
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Escape user inputs for security
    $safe_deposittype = mysqli_real_escape_string($conn, $deposittype);
    $safe_drawerNumber = mysqli_real_escape_string($conn, $drawerNumber);
    $safe_username = mysqli_real_escape_string($conn, $username);
    $safe_name = mysqli_real_escape_string($conn, $name);

    // Build the SQL query
    $sql = "INSERT INTO cashierdeposit (
        username, name, deposit_type, drawer_number,
        cash_amount,
        check21_deposit_amount, check21_deposit_count,
        ceo_check_deposit_amount, ceo_check_deposit_count,
        manual_check_deposit_amount, manual_check_deposit_count,
        money_order_deposit_amount, money_order_deposit_count,
        credit_debit_cards_amount, credit_debit_cards_count,
        pre_deposit_amount, pre_deposit_count,
        total_amount, total_count, verified
    ) VALUES (
        '$safe_username', '$safe_name', '$safe_deposittype', '$safe_drawerNumber',
        $cashAmount,
        $check21DepositAmount, $check21DepositCount,
        $ceoCheckDepositAmount, $ceoCheckDepositCount,
        $manualCheckDepositAmount, $manualCheckDepositCount,
        $moneyOrderAmount, $moneyOrderCount,
        $creditDebitCardsAmount, $creditDebitCardsCount,
        $preDepositsAmount, $preDepositsCount,
        $totalamount, $totalcount, 0
    )";

    // Attempt insert query execution
    if (mysqli_query($conn, $sql)) {
        echo "<br><span style='color: green; font-weight: bold; font-size: 24px;'>Thank you! Your deposit has been submitted for verification by a supervisor.</span><br>";
        echo "<br><span style='color: green; font-weight: bold; font-size: 24px;'>Your transaction ID is: " . mysqli_insert_id($conn) . "</span><br>";

        $newId = mysqli_insert_id($conn);
        mysqli_close($conn);

        header("Location: viewsubmission.php?id=" . $newId);
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        mysqli_close($conn);
    }
}
?>
