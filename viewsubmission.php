<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in and is a supervisor or admin
if (!isset($_SESSION['user_id'])) {
    // User is not logged in or is not a supervisor or admin
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Define $isAdmin based on the user's role
$isAdmin = ($_SESSION['role'] === 'admin');
$isSupervisor = ($_SESSION['role'] === 'supervisor');

// Fetch submission details from the database
$username = $_SESSION['username'];
$name = $_SESSION['name'];

// Sanitize the submission ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false) {
    // Invalid submission ID
    exit("Invalid submission ID.");
}

// Fetch submission details from the database
$sql = "SELECT * FROM cashierdeposit WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Fetch the row from the result set
$row = mysqli_fetch_assoc($result);

// Close statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<html>
<head>
    <title>View Submission</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<!-- Navigation -->
<?php include "navbar.php"; ?>

<!-- Content -->
<div class="container">
    <h1>View Submission</h1>
    <form>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" readonly value="<?php echo htmlspecialchars($username); ?>">
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" readonly value="<?php echo htmlspecialchars($name); ?>">
        </div>
    <table class="table table-striped">
    <thead class="thead-light">
                <tr>
                    <th>Item Name</th>
                    <th>Amount</th>
                    <th>Item Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Submission ID</td>
                    <td>
                        <?php echo isset($row['id']) ? htmlspecialchars($row['id']) : ''; ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>Date/time</td>
                    <td>
                        <?php echo isset($row['created_at']) ? htmlspecialchars($row['created_at']) : ''; ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>Deposit type</td>
                    <td>
                        <?php echo isset($row['deposit_type']) ? htmlspecialchars($row['deposit_type']) : ''; ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>Drawer Number</td>
                    <td>
                        <?php echo isset($row['drawer_number']) ? htmlspecialchars($row['drawer_number']) : ''; ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>Cash</td>
                    <td>
                        <?php echo isset($row['cash_amount']) ? htmlspecialchars($row['cash_amount']) : ''; ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>Check 21 - Deposit</td>
                    <td>
                        <?php echo isset($row['check21_deposit_amount']) ? htmlspecialchars($row['check21_deposit_amount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['check21_deposit_count']) ? htmlspecialchars($row['check21_deposit_count']) : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td>CEO Check Deposit</td>
                    <td>
                        <?php echo isset($row['ceo_check_deposit_amount']) ? htmlspecialchars($row['ceo_check_deposit_amount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['ceo_check_deposit_count']) ? htmlspecialchars($row['ceo_check_deposit_count']) : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td>Manual Check Deposit</td>
                    <td>
                        <?php echo isset($row['manual_check_deposit_amount']) ? htmlspecialchars($row['manual_check_deposit_amount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['manual_check_deposit_count']) ? htmlspecialchars($row['manual_check_deposit_count']) : ''; ?>
                    </td>
                </tr>

                <tr>
                    <td>Money Order</td>
                    <td>
                        <?php echo isset($row['money_order_deposit_amount']) ? htmlspecialchars($row['money_order_deposit_amount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['money_order_deposit_count']) ? htmlspecialchars($row['money_order_deposit_count']) : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td>Credit and Debit Cards</td>
                    <td>
                        <?php echo isset($row['credit_debit_cards_amount']) ? htmlspecialchars($row['credit_debit_cards_amount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['credit_debit_cards_count']) ? htmlspecialchars($row['credit_debit_cards_count']) : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td>Pre-Deposits</td>
                    <td>
                        <?php echo isset($row['pre_deposit_amount']) ? htmlspecialchars($row['pre_deposit_amount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['pre_deposit_count']) ? htmlspecialchars($row['pre_deposit_count']) : ''; ?>
                    </td>
                </tr>

                <tr>
                    <td><strong>Total</strong></td>
                    <td>
                        <strong><?php echo isset($row['total_amount']) ? htmlspecialchars($row['total_amount']) : ''; ?></strong>
                    </td>
                    <td>
                        <strong><?php echo isset($row['total_count']) ? htmlspecialchars($row['total_count']) : ''; ?></strong>
                    </td>
                </tr>
            </tbody>
    </table>
            <!-- Print Button -->
            <button type="button" onclick="printForm()" class="btn btn-lg btn-primary btn-block" id="printButton">Print Form</button>
    </form>
</div>

<!-- JavaScript -->
<script>
    function printForm() {
        window.print();
    }

</script>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
