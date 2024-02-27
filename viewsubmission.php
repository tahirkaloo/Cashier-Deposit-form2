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
                    <td>Deposit type</td>
                    <td>
                        <?php echo isset($row['DepositType']) ? htmlspecialchars($row['DepositType']) : ''; ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>Cash</td>
                    <td>
                        <?php echo isset($row['Cash']) ? htmlspecialchars($row['Cash']) : ''; ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>Check 21 - Deposit</td>
                    <td>
                        <?php echo isset($row['Check21DepositAmount']) ? htmlspecialchars($row['Check21DepositAmount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['Check21DepositCount']) ? htmlspecialchars($row['Check21DepositCount']) : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td>CEO Check Deposit</td>
                    <td>
                        <?php echo isset($row['CEOCheckDepositAmount']) ? htmlspecialchars($row['CEOCheckDepositAmount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['CEOCheckDepositCount']) ? htmlspecialchars($row['CEOCheckDepositCount']) : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td>Manual Check Deposit</td>
                    <td>
                        <?php echo isset($row['ManualCheckDepositAmount']) ? htmlspecialchars($row['ManualCheckDepositAmount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['ManualCheckDepositCount']) ? htmlspecialchars($row['ManualCheckDepositCount']) : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td>Money Order</td>
                    <td>
                        <?php echo isset($row['MoneyOrderAmount']) ? htmlspecialchars($row['MoneyOrderAmount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['MoneyOrderCount']) ? htmlspecialchars($row['MoneyOrderCount']) : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td>Credit and Debit Cards</td>
                    <td>
                        <?php echo isset($row['CreditDebitCardsAmount']) ? htmlspecialchars($row['CreditDebitCardsAmount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['CreditDebitCardsCount']) ? htmlspecialchars($row['CreditDebitCardsCount']) : ''; ?>
                    </td>
                </tr>
                <tr>
                    <td>Pre-Deposits</td>
                    <td>
                        <?php echo isset($row['PreDepositsAmount']) ? htmlspecialchars($row['PreDepositsAmount']) : ''; ?>
                    </td>
                    <td>
                        <?php echo isset($row['PreDepositsCount']) ? htmlspecialchars($row['PreDepositsCount']) : ''; ?>
                    </td>
                </tr>
            </tbody>
    </table>
    </form>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
