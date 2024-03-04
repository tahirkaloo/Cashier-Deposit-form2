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

// Fetch all submissions from the database
$sql = "SELECT * FROM cashierdeposit WHERE DATE(created_at) = CURDATE()";

$filterUser = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Handle date filter
    if (isset($_POST['date']) && !empty($_POST['date'])) {
        $date = mysqli_real_escape_string($conn, $_POST['date']);
        $sql .= " AND DATE(created_at) = '$date'";
    }

    //Handle Deposit type filter
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width", initial-scale="1.0">
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
        <div class="col-md-3">
          <input type="date" name="date" class="form-control" placeholder="Filter by Date" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d'); ?>">
        </div>
        <div class="col-md-3">
            <select name="deposit_type" class="form-control">
                <option value="">Filter by Deposit Type</option>
                <option value="End Of the Day" <?php echo isset($_POST['deposit_type']) && $_POST['deposit_type'] === 'End Of the Day' ? 'selected' : ''; ?>>End Of the Day</option>
                <option value="Mid day" <?php echo isset($_POST['deposit_type']) && $_POST['deposit_type'] === 'Mid day' ? 'selected' : ''; ?>>Mid Day</option>
            </select>
        </div>                    
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="summaryforms.php" class="btn btn-secondary">Reset Filters</a>
        </div>
    </div>
</form>

<?php if (mysqli_num_rows($result) > 0) : ?>
<div id="cashierDepositTable">
    <h2>Cashier Deposit Form - EOD</h2>
    <table id="cashierDepositTable">
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
                    <td><?php echo $row['username']; ?></td>
                     <td><?php echo '.' . explode('.', number_format($row['cash_amount'], 2))[1]; ?></td>
                    <td><?php echo floor($row['cash_amount']); ?></td>
                    <td><?php echo $row['cash_amount']; ?></td>
                    <td><?php echo $row['check21_deposit_amount']; ?></td>
                    <td><?php echo $row['total_amount']; ?></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="6">
                    <input type="number" name="Coin Exchanged for Dollar Bills" id="Coin Exchanged for Dollar Bills"></input>
                </td>
            </tr>
        </tbody>
        <!-- Add your footer here if needed -->
    </table>
</div>
<?php endif; ?>

<?php if (mysqli_num_rows($result) > 0) : ?>
<div id="dailyCStable">
    <h2>Daily CS table</h2>
    <table id="dailycstable">
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
                <th>Third Party Payments</th>
                <th>Total Deposit</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['cash_amount']; ?></td>
                    <td><?php echo $row['check21_deposit_count']; ?></td>
                    <td><?php echo $row['check21_deposit_amount']; ?></td>
                    <td><?php echo $row['ceo_check_deposit_amount']; ?></td>
                    <td><?php echo $row['manual_check_deposit_amount']; ?></td>
                    <td><?php echo $row['total_amount']; ?></td>
                    <td><?php echo $row['money_order_deposit_amount']; ?></td>
                    <td><?php echo $row['total_amount']; ?></td>
                    <td><?php echo $row['credit_debit_cards_amount']; ?></td>
                    <td><?php echo $row['pre_deposit_amount']; ?></td>
                    <td><?php echo $row['third_party_payments']; ?></td>
                    <td><?php echo $row['total_amount']; ?></td>
                </tr>
            <?php endwhile; ?>




<?php endif; ?>

</body>
</html>
