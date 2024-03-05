<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the user is a supervisor or admin and redirect accordingly
$isAdmin = ($_SESSION['role'] === 'admin');
$isSupervisor = ($_SESSION['role'] === 'supervisor');

if (!$isAdmin && !$isSupervisor) {
    header("Location: accessdenied.html");
    exit;
}

// If the user is logged in, retrieve the name from the session
$username = $_SESSION['username'];
$name = $_SESSION['name'];


// Initialize SQL query
$sql = "SELECT * FROM cashierdeposit WHERE 1=1";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle date filter
    if (isset($_POST['date']) && !empty($_POST['date'])) {
        $date = mysqli_real_escape_string($conn, $_POST['date']);
        $sql .= " AND DATE(created_at) = '$date'";
    }

    // Handle Deposit type filter
    if (isset($_POST['deposit_type']) && !empty($_POST['deposit_type'])) {
        $deposit_type = mysqli_real_escape_string($conn, $_POST['deposit_type']);
        $sql .= " AND deposit_type = '$deposit_type'";
    }
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    exit;
}

// Fetch data for the second table
$sql_dcs = $sql . ";"; // Clone the original query for the second table
$resultdcs = mysqli_query($conn, $sql_dcs);

if (!$resultdcs) {
    echo "Error: " . $sql_dcs . "<br>" . mysqli_error($conn);
    exit;
}

// Your existing code for fetching coin exchange data and assigning bill_amount_exchanged...

mysqli_close($conn);
?>

<!-- Your HTML code for displaying the tables... -->



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cashier Summary</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<h2>Cashier Deposit Form</h2>
<h3>Avenity System</h3>

<!-- Filter and search form -->
<form action="" method="post" class="mb-3">
    <div class="form-row">
        <div class="col-md-2">
            <input type="date" name="date" class="form-control" placeholder="Filter by Date" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>">
        </div>
        <div class="col-md-6">
            <div class="form-inline">
                <select name="deposit_type" class="form-control mr-4">
                    <option value="">Filter by Deposit Type</option>
                    <option value="End Of the Day" <?php echo isset($_POST['deposit_type']) && $_POST['deposit_type'] === 'End Of the Day' ? 'selected' : ''; ?>>End Of the Day</option>
                    <option value="Mid day" <?php echo isset($_POST['deposit_type']) && $_POST['deposit_type'] === 'Mid day' ? 'selected' : ''; ?>>Mid Day</option>
                </select>
                <button type="submit" class="btn btn-primary mr-2">Apply Filters</button>
                <a href="depositform.php" class="btn btn-secondary">Reset Filters</a>
            </div>
        </div>
    </div>
</form>

<?php if (mysqli_num_rows($result) > 0) : ?>
<div id="cashierDepositTableDiv">
    <h2>Cashier Deposit Form</h2>

    <!-- Cashier Deposit Form table -->
    <table id="cashierDepositTable" class="table table-striped table-condensed table-hover animate__animated animate__fadeIn animate__faster table-responsive">
        <thead>
            <tr>
                <th>Date</th>
                <th>Deposit Type</th>
                <th>Cashier</th>
                <th>Coin Amount</th>
                <th>Bill Amount</th>
                <th>Cash Amount</th>
                <th>Check Amount</th>
                <th>Total Deposit</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($cashierRow = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $cashierRow['created_at']; ?></td>
                    <td><?php echo $cashierRow['deposit_type']; ?></td>
                    <td><?php echo $cashierRow['name']; ?></td>
                    <td id="coinAmountcdt"><?php echo '.' . explode('.', number_format($cashierRow['cash_amount'], 2))[1]; ?></td>
                    <td id="billAmountcdt"><?php echo floor($cashierRow['cash_amount']); ?></td>
                    <td id="cashAmountcdt"><?php echo $cashierRow['cash_amount']; ?></td>
                    <td id="checkAmountcdt"><?php echo $cashierRow['check21_deposit_amount']; ?></td>
                    <td id="totalamountcdt"><?php echo $cashierRow['total_amount']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Coin Exchange</th>
                <td></td>
                <td></td>
                <td><?php echo isset($bill_amount_exchanged) ? $bill_amount_exchanged : ''; ?></td>
                <td></td>
            </tr>
            <tr>
                <th>Total</th>
                    <td><?php echo isset($deposit_type) ? $deposit_type : ''; ?></td>
                    <td>Supervisor: <?php echo isset($name) ? $name : ''; ?></td>
                    <td>
                        <?php
                            $totalCoinAmount = 0;
                            mysqli_data_seek($result, 0); // Reset result pointer
                            while ($row = mysqli_fetch_assoc($result)) {
                                $totalCoinAmount += $row['cash_amount'];
                            }
                            
                            echo '.' . explode('.', number_format($totalCoinAmount, 2))[1];
                        ?>
                    </td>
                    <td>
                        <?php
                            $totalBillAmount = 0;
                            mysqli_data_seek($resultdcs, 0); // Reset result pointer
                            while ($row = mysqli_fetch_assoc($resultdcs)) {
                                $totalBillAmount += $row['cash_amount'];
                            }
                            echo floor($totalBillAmount);
                        ?>
                    </td>
                    <td>
                        <?php
                            $totalCashAmount = 0;
                            mysqli_data_seek($result, 0); // Reset result pointer
                            while ($row = mysqli_fetch_assoc($result)) {
                                $totalCashAmount += $row['cash_amount'];
                            }
                            echo $totalCashAmount;
                        ?>
                    </td>
                    <td>
                        <?php
                            $totalCheckAmount = 0;
                            mysqli_data_seek($result, 0); // Reset result pointer
                            while ($row = mysqli_fetch_assoc($result)) {
                                $totalCheckAmount += $row['check21_deposit_amount'];
                            }
                            echo $totalCheckAmount;
                        ?>
                    </td>
                    <td>
                        <?php
                            $totalAmount = 0;
                            mysqli_data_seek($result, 0); // Reset result pointer
                            while ($row = mysqli_fetch_assoc($result)) {
                                $totalAmount += $row['total_amount'];
                            }
                            echo $totalAmount;
                        ?>
                    </td>
            </tr>
            <tr>
                <th>Deposit Bag Number</th>
                <td><input type="number" id="bagNumber" name="bagNumber" value="559168" class="form-control"></td>
        </tfoot>
    </table>

    <button id="printcdf" class="btn btn-secondary">Print Cashier Deposit Form</button>
</div>

<div id="dailyCStableDiv">
    <h2>Daily CS table</h2>
    <table id="dailyCStable" class="table table-striped table-condensed table-hover animate__animated animate__fadeIn animate__faster table-responsive">
        <thead>
            <tr>
                <th>Cashier</th>
                <th>Date/time</th>
                <th>Cash Amount</th>
                <th>Check 21 - Item Count</th>
                <th>Check 21 - Deposit Amount</th>
                <th>CEO Deposit Amount</th>
                <th>Manual Deposit Amount</th>
                <th>Grand Total Deposit Amount</th>
                <th>Money Order</th>
                <th>Total of Cash & Check</th>
                <th>Credit Card (Credit Cards + Debit Cards)</th>
                <th>Deposit Ticket (Pre-Deposited Funds)</th>
                <th>Total Deposit</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($resultdcs)) : ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td id="cashAmountdcs"><?php echo $row['cash_amount']; ?></td>
                    <td id="check21depositcountdcs"><?php echo $row['check21_deposit_count']; ?></td>
                    <td id="check21depositamountdcs"><?php echo $row['check21_deposit_amount']; ?></td>
                    <td id="ceocheckdepositamountdcs"><?php echo $row['ceo_check_deposit_amount']; ?></td>
                    <td id="manualcheckdepositamountdcs"><?php echo $row['manual_check_deposit_amount']; ?></td>
                    <td id="totalamountdcs"><?php echo $row['cash_amount'] + $row['check21_deposit_amount']; ?></td>
                    <td id="moneyorderdepositamountdcs"><?php echo $row['money_order_deposit_amount']; ?></td>
                    <td id="totalcashandcheckdcs"><?php echo $row['total_amount']; ?></td>
                    <td id="creditcarddepositamountdcs"><?php echo $row['credit_debit_cards_amount']; ?></td>
                    <td id="predepositamountdcs"><?php echo $row['pre_deposit_amount']; ?></td>
                    <td id="grandtotalamountdcs"><?php echo $row['total_amount']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <td><?php echo date('Y-m-d H:i:s'); ?></td>
                <td>
                    <?php
                        $totalCashAmount = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalCashAmount += $row['cash_amount'];
                        }
                        echo $totalCashAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalCheckDepositCount = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalCheckDepositCount += $row['check21_deposit_count'];
                        }
                        echo $totalCheckDepositCount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalCheckDepositAmount = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalCheckDepositAmount += $row['check21_deposit_amount'];
                        }
                        echo $totalCheckDepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalCEODepositAmount = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalCEODepositAmount += $row['ceo_check_deposit_amount'];
                        }
                        echo $totalCEODepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalManualDepositAmount = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalManualDepositAmount += $row['manual_check_deposit_amount'];
                        }
                        echo $totalManualDepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalTotalAmount = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalTotalAmount += $row['total_amount'];
                        }
                        echo $totalTotalAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalMoneyOrderDepositAmount = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalMoneyOrderDepositAmount += $row['money_order_deposit_amount'];
                        }
                        echo $totalMoneyOrderDepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalTotalCashAndCheck = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalTotalCashAndCheck += $row['total_amount'];
                        }
                        echo $totalTotalCashAndCheck;
                    ?>
                </td>
                <td>
                    <?php
                        $totalCreditCardDepositAmount = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalCreditCardDepositAmount += $row['credit_debit_cards_amount'];
                        }
                        echo $totalCreditCardDepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalPreDepositAmount = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalPreDepositAmount += $row['pre_deposit_amount'];
                        }
                        echo $totalPreDepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalDeposit = 0;
                        mysqli_data_seek($resultdcs, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultdcs)) {
                            $totalDeposit += $row['total_amount'];
                        }
                        echo $totalDeposit;
                    ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php endif; ?>


</body>

<script>

/**This function handles the before print event for the window. It hides the print button and daily CS table div.*/
window.onbeforeprint = function() {
    var cashierDepositTableDiv = document.getElementById('cashierDepositTableDiv');
    var dailyCStableDiv = document.getElementById('dailyCStableDiv');
    var printButton = document.getElementById('printcdf');
    printButton.style.display = 'none';
    dailyCStableDiv.style.display = 'none';
}

function printcdf() {
    window.print();
}

window.onafterprint = function() {
    var cashierDepositTableDiv = document.getElementById('cashierDepositTableDiv');
    var dailyCStableDiv = document.getElementById('dailyCStableDiv');
    var printButton = document.getElementById('printcdf');
    printButton.style.display = 'block';
    dailyCStableDiv.style.display = 'block';
}

var printcdfButton = document.getElementById('printcdf');
printcdfButton.addEventListener('click', printcdf);

</script>

</html>