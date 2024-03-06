<?php
session_start();
require_once 'db_connect.php';

$error_message = ''; // Initialize error message variable

// Check if the user is logged in and is a supervisor or admin
if (!isset($_SESSION['user_id'])) {
    // User is not logged in
    $error_message = "You need to login to edit submissions.";
} elseif ($_SESSION['role'] !== 'supervisor' && $_SESSION['role'] !== 'admin') {
    // User is not a supervisor or admin
    $error_message = "You are not authorized to edit submissions.";
} else {
    // User is authorized, proceed with database connection and submission editing
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Sanitize and validate submission ID
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === false) {
        // Invalid submission ID
        $error_message = "Invalid submission ID.";
    } else {
        // Fetch submission details from the database
        $sql = "SELECT * FROM cashierdeposit WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        $row = mysqli_fetch_assoc($result);

        // Check if the record is verified
        if ($row['verified'] == 1 && $_SESSION['role'] !== 'supervisor' && $_SESSION['role'] !== 'admin') {
            // Record is verified, only supervisors or admins can edit verified records
            $error_message = "You are not authorized to edit verified submissions.";
        } else {
            // No error, proceed to display the form for editing
            mysqli_stmt_close($stmt);
        }
    }

    // Close the database connection
    mysqli_close($conn);
}

// If there's an error message, display it
if (!empty($error_message)) {
    echo "<p>Error: $error_message</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Submission</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
<!-- Navigation -->
<?php include "navbar.php"; ?>

<!-- Content -->
<div class="container">
    <h1>Edit Submission</h1>
    <form action="updatesubmission.php" method="post">
    <div class="container">
        <!-- Display the error message if it's present -->
        <?php if (isset($_GET['error_message'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_GET['error_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Your form and other content here -->
    </div>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <!-- Display existing data for editing -->
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th>Item</th>
                    <th>Amount</th>
                    <th>Item Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Deposit type</td>
                    <td>
                        <select name="DepositType" class="form-control">
                        <option value="<?php echo $row['deposit_type']; ?>"><?php echo $row['deposit_type']; ?></option>
                        <option value="endoftheday">End of Day</option>
                        <option value="midday">Mid Day</option>
                    </td>
                </tr>
                <tr>
                    <td>Cash</td>
                    <td><input type="number" class="form-control" id="cash" name="cash" value="<?php echo $row['cash_amount']; ?>" step="0.01"> </td>
                </tr>
                <tr>
                    <td>Check 21 - Deposit</td>
                    <td><input type="number" class="form-control" id="check21DepositAmount" name="check21DepositAmount" value="<?php echo $row['check21_deposit_amount']; ?>" step="0.01"></td>
                    <td><input type="number" class="form-control" id="check21DepositCount" name="check21DepositCount" value="<?php echo $row['check21_deposit_count']; ?>" step="1"></td>
                </tr>
                <tr>
                    <td>CEO Check Deposit</td>
                    <td><input type="number" class="form-control" id="ceoCheckDepositAmount" name="ceoCheckDepositAmount" value="<?php echo $row['ceo_check_deposit_amount']; ?>" step="0.01"></td>
                    <td><input type="number" class="form-control" id="ceoCheckDepositCount" name="ceoCheckDepositCount" value="<?php echo $row['ceo_check_deposit_count']; ?>" step="1"></td>
                </tr>
                <tr>
                    <td>Manual Check Deposit</td>
                    <td><input type="number" class="form-control" id="manualCheckDepositAmount" name="manualCheckDepositAmount" value="<?php echo $row['manual_check_deposit_amount']; ?>" step="0.01"></td>
                    <td><input type="number" class="form-control" id="manualCheckDepositCount" name="manualCheckDepositCount" value="<?php echo $row['manual_check_deposit_count']; ?>" step="1"></td> 
                </tr>
                <tr>
                    <td>Money Order</td>
                    <td><input type="number" class="form-control" id="moneyOrderDepositAmount" name="moneyOrderDepositAmount" value="<?php echo $row['money_order_deposit_amount']; ?>" step="0.01"></td>
                    <td><input type="number" class="form-control" id="moneyOrderDepositCount" name="moneyOrderDepositCount" value="<?php echo $row['money_order_deposit_count']; ?>" step="1"></td>
                </tr>
                <tr>
                    <td>Credit and Debit Cards</td>
                    <td><input type="number" class="form-control" id="creditDebitCardAmount" name="creditDebitCardAmount" value="<?php echo $row['credit_debit_cards_amount']; ?>" step="0.01"></td>
                    <td><input type="number" class="form-control" id="creditDebitCardCount" name="creditDebitCardCount" value="<?php echo $row['credit_debit_cards_count']; ?>" step="1"></td>
                </tr>
                <tr>
                    <td>Pre-Deposits</td>
                    <td><input type="number" class="form-control" id="preDepositAmount" name="preDepositAmount" value="<?php echo $row['pre_deposit_amount']; ?>" step="0.01"></td>
                    <td><input type="number" class="form-control" id="preDepositCount" name="preDepositCount" value="<?php echo $row['pre_deposit_count']; ?>" step="1"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total">
                        <td>Total</td>
                        <input type="hidden" id="totalAmountInput" name="totalAmount" value="<?php echo $row['total_amount']; ?>">
                        <td><span id="totalAmount"><?php echo $row['total_amount']; ?></span></td>
                        <td><span id="totalCount"><?php echo $row['total_count']; ?></span></td>
                    </tr>
            </tfoot>
        </table>

        <button type="submit" class="btn btn-primary">Submit Changes</button>
    </form>
</div>

<!-- JavaScript -->
<script>
    // Calculate total amount and total count
    function calculateTotal() {
        var cash = parseFloat(document.getElementById("cash").value) || 0;
        var check21DepositAmount = parseFloat(document.getElementById("check21DepositAmount").value) || 0;
        var ceoCheckDepositAmount = parseFloat(document.getElementById("ceoCheckDepositAmount").value) || 0;
        var manualCheckDepositAmount = parseFloat(document.getElementById("manualCheckDepositAmount").value) || 0;
        var moneyOrderDepositAmount = parseFloat(document.getElementById("moneyOrderDepositAmount").value) || 0;
        var creditDebitCardAmount = parseFloat(document.getElementById("creditDebitCardAmount").value) || 0;
        var preDepositAmount = parseFloat(document.getElementById("preDepositAmount").value) || 0;

        var check21DepositCount = parseInt(document.getElementById("check21DepositCount").value) || 0;
        var ceoCheckDepositCount = parseInt(document.getElementById("ceoCheckDepositCount").value) || 0;
        var manualCheckDepositCount = parseInt(document.getElementById("manualCheckDepositCount").value) || 0;
        var moneyOrderDepositCount = parseInt(document.getElementById("moneyOrderDepositCount").value) || 0;
        var creditDebitCardCount = parseInt(document.getElementById("creditDebitCardCount").value) || 0;
        var preDepositCount = parseInt(document.getElementById("preDepositCount").value) || 0;

        var totalAmount = cash + check21DepositAmount + ceoCheckDepositAmount + manualCheckDepositAmount + moneyOrderDepositAmount + creditDebitCardAmount + preDepositAmount;
        var totalCount = check21DepositCount + ceoCheckDepositCount + manualCheckDepositCount + moneyOrderDepositCount + creditDebitCardCount + preDepositCount;

        document.getElementById("totalAmount").textContent = totalAmount.toFixed(2);
        document.getElementById("totalCount").textContent = totalCount;
    }

    // Call calculateTotal function when inputs change
    var inputs = document.querySelectorAll('input[type="number"]');
    inputs.forEach(function(input) {
        input.addEventListener('input', calculateTotal);
    });


    // Update total amount input field
    document.getElementById("totalAmountInput").value = totalAmount.toFixed(2);

    // Update displayed total amount and total count
    document.getElementById("totalAmount").textContent = totalAmount.toFixed(2);
    document.getElementById("totalCount").textContent = totalCount;

</script>

</body>
</html>