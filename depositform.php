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

// Default filter: Today's date
if (!isset($_POST['date']) || empty($_POST['date'])) {
    $date = date('Y-m-d');
    $sql .= " AND DATE(created_at) = '$date'";
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

// Handle deposit type filter
$deposit_type = '';
if (isset($_POST['deposit_type']) && !empty($_POST['deposit_type'])) {
    $deposit_type = mysqli_real_escape_string($conn, $_POST['deposit_type']);
    $sql .= " AND deposit_type = '$deposit_type'";
    $sql_dcs .= " AND deposit_type = '$deposit_type'";
} else {
    $deposit_type = 'End of the Day';
    $sql .= " AND deposit_type = 'End of the Day'";
    $sql_dcs .= " AND deposit_type = 'End of the Day'";
}

//Handle date filter
if (isset($_POST['date']) && !empty($_POST['date'])) {
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $sql .= " AND DATE(date) = '$date'";
    $sql_dcs .= " AND DATE(date) = '$date'"; 
} else {
    $date = date('Y-m-d');
    $sql .= " AND DATE(date) = '$date'";
    $sql_dcs .= " AND DATE(date) = '$date'";
}

// Fetch data for the third table
$sqlce = "SELECT * FROM coinexchange WHERE deposit_type = '$deposit_type' AND DATE(date) = '$date'";


$resultce = mysqli_query($conn, $sqlce);

if (!$resultce) {
    echo "Error: " . $sqlce . "<br>" . mysqli_error($conn);
    exit;
}


mysqli_close($conn);
?>

<!-- Your HTML code for displaying the tables... -->


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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>


<body>
<?php include 'navbar.php'; ?>
<div class="container-fluid bg-light rounded shadow animate__animated animate__fadeIn animate__faster text-dark mb-5">

<!-- Filter and search form -->
<form action="" method="post" class="mb-3 mt-4" id="filterForm">
    <div class="form-row">
        <div class="col-md-2">
            <input type="date" name="date" class="form-control" placeholder="Filter by Date" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d'); ?>">
        </div>
        <div class="col-md-5">
            <div class="form-inline">
                <select name="deposit_type" class="form-control mr-4 w-50">
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
                    <td id="totalamountcdt"><?php echo $cashierRow['cash_amount'] + $cashierRow['check21_deposit_amount']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Coin Exchange</th>
                <td></td>
                <td></td>
                <td><?php
                        $totalbillamountexchanged = 0;
                        mysqli_data_seek($resultce, 0); // Reset result pointer
                        while ($row = mysqli_fetch_assoc($resultce)) {
                            $totalbillamountexchanged += $row['bill_amount_exchanged'];
                        }
                        echo $totalbillamountexchanged;
                    ?>
                </td>
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
                            // Extract the cents part from the cash amount and add it to the total coin amount
                            $cents = explode('.', number_format($row['cash_amount'], 2))[1];
                            $totalCoinAmount += $cents;
                        }
                        
                        // Convert the total coin amount to dollars and cents
                        $totalCoinAmountInDollars = $totalCoinAmount / 100; // Convert cents to dollars
                        
                        // Calculate the total amount of coins exchanged in dollars and cents
                        $totalCoinAmountExchanged = $totalCoinAmountInDollars - $totalbillamountexchanged;

                        // Display the result in dollars and cents format
                        echo number_format($totalCoinAmountExchanged, 2); // Format the result to display dollars and cents
                        ?>
                    </td>


                    <td>
                        <?php
                            $totalBillAmount = 0;
                            mysqli_data_seek($result, 0); // Reset result pointer
                            while ($row = mysqli_fetch_assoc($result)) {
                                if (!empty($row['cash_amount'])) {
                                    $totalBillAmount += floor($row['cash_amount']);
                                }
                            }
                            
                            $totalba = $totalBillAmount + $totalbillamountexchanged;

                            echo $totalba;
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
                                $totalAmount += $row['cash_amount'] + $row['check21_deposit_amount'];
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

    <button id="printcdf" class="btn btn-primary" onclick="printcdf()">Print Cashier Deposit Form</button>
</div>
<br>
<br>

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
                    <td id="totalamountdcs"><?php echo ($row['cash_amount'] ?? 0) + ($row['check21_deposit_amount'] ?? 0) + ($row['ceo_check_deposit_amount'] ?? 0) + ($row['manual_check_deposit_amount'] ?? 0);?></td>                    <td id="moneyorderdepositamountdcs"><?php echo $row['money_order_deposit_amount']; ?></td>
                    <td id="totalcashandcheckdcs"><?php echo $row['cash_amount'] + $row['check21_deposit_amount']; ?></td>
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
                            $totalTotalAmount += $row['cash_amount'] + $row['check21_deposit_amount'] + $row['ceo_check_deposit_amount'] + $row['manual_check_deposit_amount'];
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
                            $totalTotalCashAndCheck += $row['cash_amount'] + $row['check21_deposit_amount'];
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

    <button id="printdcs" class="btn btn-primary">Print all to Laserfiche</button>
</div>
<?php endif; ?>
</div>
</body>
<!-- Footer -->
<?php include 'footer.php'; ?>

<script>
    document.getElementById('printcdf').addEventListener('click', function() {
        var cashierDepositTableDiv = document.getElementById('cashierDepositTableDiv');
        var dailyCStableDiv = document.getElementById('dailyCStableDiv');
        var filterForm = document.getElementById('filterForm');
        var printButton = document.getElementById('printcdf');
        filterForm.style.display = 'none';
        printButton.style.display = 'none';
        dailyCStableDiv.style.display = 'none';
        window.print();
        filterForm.style.display = 'block';
        printButton.style.display = 'block';
        cashierDepositTableDiv.style.display = 'block';
        dailyCStableDiv.style.display = 'block';
    });

    document.getElementById('printdcs').addEventListener('click', function() {
        var printButtoncdf = document.getElementById('printcdf');
        var dailyCStableDiv = document.getElementById('dailyCStableDiv');
        var filterForm = document.getElementById('filterForm');
        var printButton = document.getElementById('printdcs');
        filterForm.style.display = 'none';
        printButton.style.display = 'none';
        printButtoncdf.style.display = 'none';
        dailyCStableDiv.style.display = 'block';
        window.print();
        printButton.style.display = 'block';
        filterForm.style.display = 'block';
    });
</script>


</html>