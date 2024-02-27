<?php
session_start();
require_once 'db_connect.php'; 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Assign form values to variables
$deposittype = isset($_POST['DepositType']) ? $_POST['DepositType'] : null;
$username = isset($_POST['username']) ? $_POST['username'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$cashAmount = isset($_POST['Cash']) ? $_POST['Cash'] : null;
$check21DepositAmount = isset($_POST['Check21DepositAmount']) ? $_POST['Check21DepositAmount'] : null;
$check21DepositCount = isset($_POST['Check21DepositCount']) ? $_POST['Check21DepositCount'] : null;
$ceoCheckDepositAmount = isset($_POST['CEOCheckDepositAmount']) ? $_POST['CEOCheckDepositAmount'] : null;
$ceoCheckDepositCount = isset($_POST['CEOCheckDepositCount']) ? $_POST['CEOCheckDepositCount'] : null;
$manualCheckDepositAmount = isset($_POST['ManualCheckDepositAmount']) ? $_POST['ManualCheckDepositAmount'] : null;
$manualCheckDepositCount = isset($_POST['ManualCheckDepositCount']) ? $_POST['ManualCheckDepositCount'] : null;
$moneyOrderAmount = isset($_POST['MoneyOrderAmount']) ? $_POST['MoneyOrderAmount'] : null;
$moneyOrderCount = isset($_POST['MoneyOrderCount']) ? $_POST['MoneyOrderCount'] : null;
$creditDebitCardsAmount = isset($_POST['CreditDebitCardsAmount']) ? $_POST['CreditDebitCardsAmount'] : null;
$creditDebitCardsCount = isset($_POST['CreditDebitCardsCount']) ? $_POST['CreditDebitCardsCount'] : null;
$preDepositsAmount = isset($_POST['PreDepositsAmount']) ? $_POST['PreDepositsAmount'] : null;
$preDepositsCount = isset($_POST['PreDepositsCount']) ? $_POST['PreDepositsCount'] : null;

// Escape user inputs for security
$deposittype = mysqli_real_escape_string($conn, $deposittype);
$username = mysqli_real_escape_string($conn, $username);
$name = mysqli_real_escape_string($conn, $name);
$cashAmount = !empty($cashAmount) ? mysqli_real_escape_string($conn, $cashAmount) : 'NULL';
$check21DepositAmount = !empty($check21DepositAmount) ? mysqli_real_escape_string($conn, $check21DepositAmount) : 'NULL';
$check21DepositCount = !empty($check21DepositCount) ? mysqli_real_escape_string($conn, $check21DepositCount) : 'NULL';
$ceoCheckDepositAmount = !empty($ceoCheckDepositAmount) ? mysqli_real_escape_string($conn, $ceoCheckDepositAmount) : 'NULL';
$ceoCheckDepositCount = !empty($ceoCheckDepositCount) ? mysqli_real_escape_string($conn, $ceoCheckDepositCount) : 'NULL';
$manualCheckDepositAmount = !empty($manualCheckDepositAmount) ? mysqli_real_escape_string($conn, $manualCheckDepositAmount) : 'NULL';
$manualCheckDepositCount = !empty($manualCheckDepositCount) ? mysqli_real_escape_string($conn, $manualCheckDepositCount) : 'NULL';
$moneyOrderAmount = !empty($moneyOrderAmount) ? mysqli_real_escape_string($conn, $moneyOrderAmount) : 'NULL';
$moneyOrderCount = !empty($moneyOrderCount) ? mysqli_real_escape_string($conn, $moneyOrderCount) : 'NULL';
$creditDebitCardsAmount = !empty($creditDebitCardsAmount) ? mysqli_real_escape_string($conn, $creditDebitCardsAmount) : 'NULL';
$creditDebitCardsCount = !empty($creditDebitCardsCount) ? mysqli_real_escape_string($conn, $creditDebitCardsCount) : 'NULL';
$preDepositsAmount = !empty($preDepositsAmount) ? mysqli_real_escape_string($conn, $preDepositsAmount) : 'NULL';
$preDepositsCount = !empty($preDepositsCount) ? mysqli_real_escape_string($conn, $preDepositsCount) : 'NULL';

// Build the SQL query
$sql = "INSERT INTO cashierdeposit (username, name, deposit_type, cash_amount, check21_deposit_amount, check21_deposit_count, ceo_check_deposit_amount, ceo_check_deposit_count, manual_check_deposit_amount, manual_check_deposit_count, money_order_deposit_amount, money_order_deposit_count, credit_debit_cards_amount, credit_debit_cards_count, pre_deposit_amount, pre_deposit_count, verified) 
        VALUES ('$username', '$name', '$deposittype', $cashAmount, $check21DepositAmount, $check21DepositCount, $ceoCheckDepositAmount, $ceoCheckDepositCount, $manualCheckDepositAmount, $manualCheckDepositCount, $moneyOrderAmount, $moneyOrderCount, $creditDebitCardsAmount, $creditDebitCardsCount, $preDepositsAmount, $preDepositsCount, 0)";

// Attempt insert query execution
if (mysqli_query($conn, $sql)) {
    echo "<br><span style='color: green; font-weight: bold; font-size: 24px;'>Thank you! Your deposit has been submitted for verification by a supervisor.</span><br>";
    echo "<br><span style='color: green; font-weight: bold; font-size: 24px;'>Your transaction ID is: " . mysqli_insert_id($conn) . "</span><br>";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);

//show countdown on the page
echo "<br><span style='color: red; font-weight: bold; font-size: 24px;'>Redirecting in 3 seconds...</span><br>";

// Redirect back to the cashier deposit page
header("Refresh: 3; url=cashierdeposit.php");
exit();


