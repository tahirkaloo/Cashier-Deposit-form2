<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the user is a supervisor or admin and redirect accordingly
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$isSupervisor = (isset($_SESSION['role']) && $_SESSION['role'] === 'supervisor');

if (!$isAdmin && !$isSupervisor) {
    header("Location: accessdenied.html");
    exit;
}

// If the user is logged in, retrieve the name from the session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown';

// Initialize data arrays
$cashierDeposits = [];
$dailyCSData = [];
$coinExchangeData = [];

// Determine filters
$filterDate = isset($_POST['date']) && !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d');
$filterDepositType = isset($_POST['deposit_type']) ? $_POST['deposit_type'] : '';

// --- DATA FETCHING ---

if (defined('DEMO_MODE') && DEMO_MODE) {
    // --- DEMO MODE MOCK DATA ---
    $cashierDeposits = [
        [
            'created_at' => date('Y-m-d H:i:s'),
            'deposit_type' => 'End Of the Day',
            'name' => 'demo_user',
            'cash_amount' => 150.50,
            'check21_deposit_amount' => 50.00
        ],
        [
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'deposit_type' => 'Mid day',
            'name' => 'Jane Doe',
            'cash_amount' => 200.00,
            'check21_deposit_amount' => 75.25
        ]
    ];

    // Filter Mock Data roughly based on inputs to simulate functionality
    if ($filterDepositType) {
        $cashierDeposits = array_filter($cashierDeposits, function($row) use ($filterDepositType) {
            return $row['deposit_type'] === $filterDepositType;
        });
    }

    $dailyCSData = [
         [
            'name' => 'demo_user',
            'created_at' => date('Y-m-d H:i:s'),
            'cash_amount' => 150.50,
            'check21_deposit_count' => 2,
            'check21_deposit_amount' => 50.00,
            'ceo_check_deposit_amount' => 0,
            'manual_check_deposit_amount' => 0,
            'money_order_deposit_amount' => 0,
            'credit_debit_cards_amount' => 120.00,
            'pre_deposit_amount' => 0,
            'total_amount' => 320.50
        ]
    ];

    $coinExchangeData = [
        ['bill_amount_exchanged' => 20]
    ];

} else {
    // --- REAL DATABASE LOGIC ---
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Build Query for Cashier Deposits
    $sql = "SELECT * FROM cashierdeposit WHERE 1=1";

    // Date Filter
    $safeDate = mysqli_real_escape_string($conn, $filterDate);
    $sql .= " AND DATE(created_at) = '$safeDate'";

    // Deposit Type Filter
    if (!empty($filterDepositType)) {
        $safeType = mysqli_real_escape_string($conn, $filterDepositType);
        $sql .= " AND deposit_type = '$safeType'";
    }

    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cashierDeposits[] = $row;
        }
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Clone data for Daily CS (Logic in original file seemed to imply they are the same query source)
    $dailyCSData = $cashierDeposits;

    // Coin Exchange Query
    // Logic from original: defaults to 'End of the Day' if not set
    $ceType = !empty($filterDepositType) ? $filterDepositType : 'End of the Day';
    $safeCeType = mysqli_real_escape_string($conn, $ceType);

    // Note: Original code used 'date' column for coinexchange, but 'created_at' for cashierdeposit.
    // Assuming 'date' column exists in coinexchange.
    $sqlce = "SELECT * FROM coinexchange WHERE deposit_type = '$safeCeType' AND DATE(date) = '$safeDate'";

    $resultce = mysqli_query($conn, $sqlce);
    if ($resultce) {
        while ($row = mysqli_fetch_assoc($resultce)) {
            $coinExchangeData[] = $row;
        }
    } else {
        // echo "Error: " . $sqlce . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

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

<!-- Demo Mode Warning -->
<?php if (defined('DEMO_MODE') && DEMO_MODE): ?>
<div class="alert alert-warning text-center fw-bold" role="alert">
    <i class="fa fa-exclamation-triangle"></i> Demo Mode: Database Connection Failed. Showing Mock Data.
</div>
<?php endif; ?>

<div class="container-fluid bg-light rounded shadow animate__animated animate__fadeIn animate__faster text-dark mb-5">

<!-- Filter and search form -->
<form action="" method="post" class="mb-3 mt-4" id="filterForm">
    <div class="form-row">
        <div class="col-md-2">
            <input type="date" name="date" class="form-control" placeholder="Filter by Date" value="<?php echo htmlspecialchars($filterDate); ?>">
        </div>
        <div class="col-md-5">
            <div class="form-inline">
                <select name="deposit_type" class="form-control mr-4 w-50">
                    <option value="">Filter by Deposit Type</option>
                    <option value="End Of the Day" <?php echo $filterDepositType === 'End Of the Day' ? 'selected' : ''; ?>>End Of the Day</option>
                    <option value="Mid day" <?php echo $filterDepositType === 'Mid day' ? 'selected' : ''; ?>>Mid Day</option>
                </select>
                <button type="submit" class="btn btn-primary mr-2">Apply Filters</button>
                <a href="depositform.php" class="btn btn-secondary">Reset Filters</a>
            </div>
        </div>
    </div>
</form>

<?php if (!empty($cashierDeposits)) : ?>
<div id="cashierDepositTableDiv">
    <h2>Cashier Deposit Form</h2>

    <!-- Cashier Deposit Form table -->
    <table id="cashierDepositTable" class="table table-striped table-condensed table-hover animate__animated animate__fadeIn animate__faster table-responsive">
        <thead>
            <tr>
                <th id="date">Date</th>
                <th id="depositType">Deposit Type</th>
                <th id="name">Cashier</th>
                <th id="coinAmount">Coin Amount</th>
                <th id="billAmount">Bill Amount</th>
                <th id="cashAmount">Cash Amount</th>
                <th id="checkAmount">Check Amount</th>
                <th id="totalAmount">Total Deposit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cashierDeposits as $cashierRow) : ?>
                <tr>
                    <td><?php echo $cashierRow['created_at']; ?></td>
                    <td><?php echo $cashierRow['deposit_type']; ?></td>
                    <td><?php echo $cashierRow['name']; ?></td>
                    <td id="coinAmountcdt"><?php echo '.' . (isset(explode('.', number_format($cashierRow['cash_amount'], 2))[1]) ? explode('.', number_format($cashierRow['cash_amount'], 2))[1] : '00'); ?></td>
                    <td id="billAmountcdt"><?php echo floor($cashierRow['cash_amount']); ?></td>
                    <td id="cashAmountcdt"><?php echo $cashierRow['cash_amount']; ?></td>
                    <td id="checkAmountcdt"><?php echo $cashierRow['check21_deposit_amount']; ?></td>
                    <td id="totalamountcdt"><?php echo $cashierRow['cash_amount'] + $cashierRow['check21_deposit_amount']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th id="Coin Exchange">Coin Exchange</th>
                <td></td>
                <td></td>
                <td><?php
                        $totalbillamountexchanged = 0;
                        foreach ($coinExchangeData as $row) {
                            $totalbillamountexchanged += $row['bill_amount_exchanged'];
                        }
                        echo $totalbillamountexchanged;
                    ?>
                </td>
                <td></td>
            </tr>
            <tr>
                <th id="Total">Total</th>
                    <td><?php echo isset($filterDepositType) && !empty($filterDepositType) ? $filterDepositType : 'End of the Day'; ?></td>
                    <td>Supervisor: <?php echo isset($name) ? $name : ''; ?></td>
                    <td>
                        <?php
                        $totalCoinAmount = 0;
                        foreach ($cashierDeposits as $row) {
                            // Extract the cents part from the cash amount and add it to the total coin amount
                            $parts = explode('.', number_format($row['cash_amount'], 2));
                            $cents = isset($parts[1]) ? $parts[1] : 0;
                            $totalCoinAmount += $cents;
                        }

                        // Convert the total coin amount to dollars and cents
                        $totalCoinAmountInDollars = $totalCoinAmount / 100; // Convert cents to dollars

                        // Calculate the total amount of coins exchanged in dollars and cents
                        $totalCoinAmountExchanged = $totalCoinAmountInDollars - $totalbillamountexchanged;

                        // Display the result in dollars and cents format
                        echo number_format($totalCoinAmountExchanged, 2);
                        ?>
                    </td>


                    <td>
                        <?php
                            $totalBillAmount = 0;
                            foreach ($cashierDeposits as $row) {
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
                            foreach ($cashierDeposits as $row) {
                                $totalCashAmount += $row['cash_amount'];
                            }
                            echo $totalCashAmount;
                        ?>
                    </td>
                    <td>
                        <?php
                            $totalCheckAmount = 0;
                            foreach ($cashierDeposits as $row) {
                                $totalCheckAmount += $row['check21_deposit_amount'];
                            }
                            echo $totalCheckAmount;
                        ?>
                    </td>
                    <td>
                        <?php
                            $totalAmount = 0;
                            foreach ($cashierDeposits as $row) {
                                $totalAmount += $row['cash_amount'] + $row['check21_deposit_amount'];
                            }
                            echo $totalAmount;
                        ?>
                    </td>
            </tr>
            <tr>
                <th id="Deposit Bag Number">Deposit Bag Number</th>
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
                <th id="Cashier">Cashier</th>
                <th id="Date/time">Date/time</th>
                <th id="CashAmount">Cash Amount</th>
                <th id="Check21DepositCount">Check 21 - Item Count</th>
                <th id="Check21DepositAmount">Check 21 - Deposit Amount</th>
                <th id="CEODepositAmount">CEO Deposit Amount</th>
                <th id="ManualDepositAmount">Manual Deposit Amount</th>
                <th id="GrandTotalDepositAmount">Grand Total Deposit Amount</th>
                <th id="MoneyOrder">Money Order</th>
                <th id="Total of Cash & Check">Total of Cash & Check</th>
                <th id="CreditCard">Credit Card (Credit Cards + Debit Cards)</th>
                <th id="DepositTicket">Deposit Ticket (Pre-Deposited Funds)</th>
                <th id="TotalDeposit">Total Deposit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dailyCSData as $row) : ?>
                <tr>
                    <td id="cashierdcs"><?php echo $row['name']; ?></td>
                    <td id="datetimedcs"><?php echo $row['created_at']; ?></td>
                    <td id="cashamountdcs"><?php echo $row['cash_amount']; ?></td>
                    <td id="check21depositcountdcs"><?php echo isset($row['check21_deposit_count']) ? $row['check21_deposit_count'] : 0; ?></td>
                    <td id="check21depositamountdcs"><?php echo isset($row['check21_deposit_amount']) ? $row['check21_deposit_amount'] : 0; ?></td>
                    <td id="ceocheckdepositamountdcs"><?php echo isset($row['ceo_check_deposit_amount']) ? $row['ceo_check_deposit_amount'] : 0; ?></td>
                    <td id="manualcheckdepositamountdcs"><?php echo isset($row['manual_check_deposit_amount']) ? $row['manual_check_deposit_amount'] : 0; ?></td>
                    <td id="totalamountdcs"><?php echo ($row['cash_amount'] ?? 0) + ($row['check21_deposit_amount'] ?? 0) + ($row['ceo_check_deposit_amount'] ?? 0) + ($row['manual_check_deposit_amount'] ?? 0);?></td>
                    <td id="moneyorderdepositamountdcs"><?php echo isset($row['money_order_deposit_amount']) ? $row['money_order_deposit_amount'] : 0; ?></td>
                    <td id="totalcashandcheckdcs"><?php echo ($row['cash_amount'] ?? 0) + ($row['check21_deposit_amount'] ?? 0); ?></td>
                    <td id="creditcarddepositamountdcs"><?php echo isset($row['credit_debit_cards_amount']) ? $row['credit_debit_cards_amount'] : 0; ?></td>
                    <td id="predepositamountdcs"><?php echo isset($row['pre_deposit_amount']) ? $row['pre_deposit_amount'] : 0; ?></td>
                    <td id="grandtotalamountdcs"><?php echo isset($row['total_amount']) ? $row['total_amount'] : 0; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <td><?php echo date('Y-m-d H:i:s'); ?></td>
                <td>
                    <?php
                        $totalCashAmount = 0;
                        foreach ($dailyCSData as $row) {
                            $totalCashAmount += $row['cash_amount'];
                        }
                        echo $totalCashAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalCheckDepositCount = 0;
                        foreach ($dailyCSData as $row) {
                            $totalCheckDepositCount += isset($row['check21_deposit_count']) ? $row['check21_deposit_count'] : 0;
                        }
                        echo $totalCheckDepositCount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalCheckDepositAmount = 0;
                        foreach ($dailyCSData as $row) {
                            $totalCheckDepositAmount += isset($row['check21_deposit_amount']) ? $row['check21_deposit_amount'] : 0;
                        }
                        echo $totalCheckDepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalCEODepositAmount = 0;
                        foreach ($dailyCSData as $row) {
                            $totalCEODepositAmount += isset($row['ceo_check_deposit_amount']) ? $row['ceo_check_deposit_amount'] : 0;
                        }
                        echo $totalCEODepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalManualDepositAmount = 0;
                        foreach ($dailyCSData as $row) {
                            $totalManualDepositAmount += isset($row['manual_check_deposit_amount']) ? $row['manual_check_deposit_amount'] : 0;
                        }
                        echo $totalManualDepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalTotalAmount = 0;
                        foreach ($dailyCSData as $row) {
                            $totalTotalAmount += ($row['cash_amount'] ?? 0) + ($row['check21_deposit_amount'] ?? 0) + ($row['ceo_check_deposit_amount'] ?? 0) + ($row['manual_check_deposit_amount'] ?? 0);
                        }
                        echo $totalTotalAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalMoneyOrderDepositAmount = 0;
                        foreach ($dailyCSData as $row) {
                            $totalMoneyOrderDepositAmount += isset($row['money_order_deposit_amount']) ? $row['money_order_deposit_amount'] : 0;
                        }
                        echo $totalMoneyOrderDepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalTotalCashAndCheck = 0;
                        foreach ($dailyCSData as $row) {
                            $totalTotalCashAndCheck += ($row['cash_amount'] ?? 0) + ($row['check21_deposit_amount'] ?? 0);
                        }
                        echo $totalTotalCashAndCheck;
                    ?>
                </td>
                <td>
                    <?php
                        $totalCreditCardDepositAmount = 0;
                        foreach ($dailyCSData as $row) {
                            $totalCreditCardDepositAmount += isset($row['credit_debit_cards_amount']) ? $row['credit_debit_cards_amount'] : 0;
                        }
                        echo $totalCreditCardDepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalPreDepositAmount = 0;
                        foreach ($dailyCSData as $row) {
                            $totalPreDepositAmount += isset($row['pre_deposit_amount']) ? $row['pre_deposit_amount'] : 0;
                        }
                        echo $totalPreDepositAmount;
                    ?>
                </td>
                <td>
                    <?php
                        $totalDeposit = 0;
                        foreach ($dailyCSData as $row) {
                            $totalDeposit += isset($row['total_amount']) ? $row['total_amount'] : 0;
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


</body>
</html>