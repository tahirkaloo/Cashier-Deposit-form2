<?php
session_start();
require_once 'db_connect.php';

// Initialize $date variable
$date = date('Y-m-d');

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

// Initialize SQL query
$sql = "SELECT * FROM cashierdeposit WHERE 1=1"; // 1=1 always true, acts as a placeholder

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

// Reset the SQL query to get data for the second table
$sql .= ";"; // End the first query
$resultdcs = mysqli_query($conn, $sql);

if (!$resultdcs) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    exit;
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
            <input type="date" name="date" class="form-control" placeholder="Filter by Date" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d'); ?>">
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
<div id="cashierDepositTable">
    <h2>Cashier Deposit Form</h2>
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
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $row['created_at']; ?></td>
                    <td><?php echo $row['deposit_type']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td id="coinAmount"><?php echo '.' . explode('.', number_format($row['cash_amount'], 2))[1]; ?></td>
                    <td><?php echo floor($row['cash_amount']); ?></td>
                    <td><?php echo $row['cash_amount']; ?></td>
                    <td><?php echo $row['check21_deposit_amount']; ?></td>
                    <td><?php echo $row['total_amount']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Coin Exchange</th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <!-- Get the bill amount exchnaged for selected date from coinexchangetable  -->
                    <?php
                    $sql = "SELECT bill_amount_exchanged FROM coinexchange WHERE date = '$date'";

                    $result = mysqli_query($conn, $sql);

                    if (!$result) {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                        exit;
                    }

                    $row = mysqli_fetch_assoc($result);

                    $bill_amount = $row['bill_amount_exchanged'];
                    ?>
                    <td><?php echo $bill_amount; ?></td>

                </td>
            <tr>
                <th>Total</th>
                    <td></td>
                    <td></td>
                    <td><?php echo floor($CoinAmount); ?></td>
                    <td><?php echo floor($bill_amount + $row['bill_amount_exchanged']); ?></td>
                </td>
            </tr>
        </tfoot>
    </table>
    <button id="printcdf" class="btn btn-secondary">Print Cashier Deposit Form</button>
</div>




<div id="dailyCStable">
    <h2>Daily CS table</h2>
    <table id="dailycstable" class="table table-striped table-condensed table-hover animate__animated animate__fadeIn animate__faster table-responsive">
        <thead>
            <tr>
                <th>Cashier</th>
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
                    <td><?php echo $row['cash_amount']; ?></td>
                    <td><?php echo $row['check21_deposit_count']; ?></td>
                    <td><?php echo $row['check21_deposit_amount']; ?></td>
                    <td><?php echo $row['ceo_check_deposit_amount']; ?></td>
                    <td><?php echo $row['manual_check_deposit_amount']; ?></td>
                    <td><?php echo $row['cash_amount'] + $row['check21_deposit_amount']; ?></td>
                    <td><?php echo $row['money_order_deposit_amount']; ?></td>
                    <td><?php echo $row['total_amount']; ?></td>
                    <td><?php echo $row['credit_debit_cards_amount']; ?></td>
                    <td><?php echo $row['pre_deposit_amount']; ?></td>
                    <td><?php echo $row['total_amount']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>




<?php endif; ?>

</body>


<script>
    window.onbeforeprint = function() {
        var cashierDepositTable = document.getElementById('cashierDepositTable');
        var dailyCStable = document.getElementById('dailyCStable');
        var printButton = document.getElementById('printButton');
        printButton.style.display = 'none';
        dailyCStable.style.display = 'none';
    }

    function printcdf() {
        window.print();
    }

    window.onafterprint = function() {
        var cashierDepositTable = document.getElementById('cashierDepositTable');
        var dailyCStable = document.getElementById('dailyCStable');
        var printButton = document.getElementById('printButton');
        printButton.style.display = 'block';
        dailyCStable.style.display = 'block';
    }

    var printcdfButton = document.getElementById('printcdf');
    printcdfButton.addEventListener('click', printcdf);
</script>




</script>


</html>
