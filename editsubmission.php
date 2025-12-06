<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error_message = '';
$row = [];

// Sanitize and validate submission ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false) {
    $error_message = "Invalid submission ID.";
}

if (defined('DEMO_MODE') && DEMO_MODE) {
    // --- DEMO MODE ---
    if ($id) {
        $row = [
            'id' => $id,
            'deposit_type' => 'End of the Day',
            'cash_amount' => 150.50,
            'check21_deposit_count' => 2,
            'check21_deposit_amount' => 50.00,
            'ceo_check_deposit_count' => 0,
            'ceo_check_deposit_amount' => 0.00,
            'manual_check_deposit_count' => 0,
            'manual_check_deposit_amount' => 0.00,
            'money_order_deposit_count' => 0,
            'money_order_deposit_amount' => 0.00,
            'credit_debit_cards_count' => 5,
            'credit_debit_cards_amount' => 120.00,
            'pre_deposit_count' => 0,
            'pre_deposit_amount' => 0.00,
            'total_count' => 7,
            'total_amount' => 320.50,
            'verified' => 0
        ];
    } else {
        $error_message = "Submission not found.";
    }

} else {
    // --- REAL DATABASE LOGIC ---
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if ($id) {
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

        // Check if the record exists and user is authorized to edit
        if ($row) {
            $isSupervisor = (isset($_SESSION['role']) && $_SESSION['role'] === 'supervisor');
            $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

            if ($isSupervisor || $isAdmin) {
                // User is supervisor or admin, proceed
            } else {
                // User is not supervisor or admin, check if the record is verified
                if ($row['verified'] == 1) {
                    $error_message = "You are not authorized to edit verified submissions.";
                    $row = []; // Clear row so form is not shown
                }
            }
        } else {
            $error_message = "Submission not found.";
        }

        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Submission | Deposits Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
<!-- Navigation -->
<?php include "navbar.php"; ?>

<!-- Demo Mode Warning -->
<?php if (defined('DEMO_MODE') && DEMO_MODE): ?>
<div class="alert alert-warning text-center fw-bold" role="alert">
    <i class="fa fa-exclamation-triangle"></i> Demo Mode: Database Connection Failed. Showing Mock Data.
</div>
<?php endif; ?>

<!-- Content -->
<div class="container mt-5 mb-5">

    <div class="glass-panel p-5 animate-fade-up">
        <h1 class="mb-4"><i class="fas fa-edit text-primary me-2"></i>Edit Submission</h1>

        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger alert-custom" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($row)): ?>
        <form action="updatesubmission.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

            <div class="table-responsive rounded shadow-sm">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Item</th>
                        <th class="py-3">Item Count</th>
                        <th class="py-3">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr>
                        <td>Deposit type</td>
                        <td>
                            <select name="DepositType" class="form-select">
                                <option value="<?php echo htmlspecialchars($row['deposit_type']); ?>"><?php echo htmlspecialchars($row['deposit_type']); ?></option>
                                <option value="End of the Day">End of the Day</option>
                                <option value="Mid day">Mid Day</option>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Cash</td>
                        <td></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="cash" name="cash" value="<?php echo $row['cash_amount']; ?>" step="0.01">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Check 21 - Deposit</td>
                        <td><input type="number" class="form-control" id="check21DepositCount" name="check21DepositCount" value="<?php echo $row['check21_deposit_count']; ?>" step="1"></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="check21DepositAmount" name="check21DepositAmount" value="<?php echo $row['check21_deposit_amount']; ?>" step="0.01">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>CEO Check Deposit</td>
                        <td><input type="number" class="form-control" id="ceoCheckDepositCount" name="ceoCheckDepositCount" value="<?php echo $row['ceo_check_deposit_count']; ?>" step="1"></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="ceoCheckDepositAmount" name="ceoCheckDepositAmount" value="<?php echo $row['ceo_check_deposit_amount']; ?>" step="0.01">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Manual Check Deposit</td>
                        <td><input type="number" class="form-control" id="manualCheckDepositCount" name="manualCheckDepositCount" value="<?php echo $row['manual_check_deposit_count']; ?>" step="1"></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="manualCheckDepositAmount" name="manualCheckDepositAmount" value="<?php echo $row['manual_check_deposit_amount']; ?>" step="0.01">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Money Order</td>
                        <td><input type="number" class="form-control" id="moneyOrderDepositCount" name="moneyOrderDepositCount" value="<?php echo $row['money_order_deposit_count']; ?>" step="1"></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="moneyOrderDepositAmount" name="moneyOrderDepositAmount" value="<?php echo $row['money_order_deposit_amount']; ?>" step="0.01">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Credit and Debit Cards</td>
                        <td><input type="number" class="form-control" id="creditDebitCardCount" name="creditDebitCardCount" value="<?php echo $row['credit_debit_cards_count']; ?>" step="1"></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="creditDebitCardAmount" name="creditDebitCardAmount" value="<?php echo $row['credit_debit_cards_amount']; ?>" step="0.01">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Pre-Deposits</td>
                        <td><input type="number" class="form-control" id="preDepositCount" name="preDepositCount" value="<?php echo $row['pre_deposit_count']; ?>" step="1"></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="preDepositAmount" name="preDepositAmount" value="<?php echo $row['pre_deposit_amount']; ?>" step="0.01">
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="table-dark">
                    <tr class="total">
                            <td class="py-3"><strong>Total</strong></td>
                            <td class="py-3"><strong><span id="totalCount"><?php echo $row['total_count']; ?></span></strong></td>
                            <td class="py-3"><strong>$<span id="totalAmount"><?php echo $row['total_amount']; ?></span></strong></td>
                    </tr>
                </tfoot>
            </table>
            </div>

            <button type="submit" class="btn btn-gradient btn-lg mt-4"><i class="fas fa-save me-2"></i> Submit Changes</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript -->
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
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

</script>

</body>
<?php include "footer.php"; ?>
</html>
