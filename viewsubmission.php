<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch submission details from the session/user
$username = $_SESSION['username'];
$name = $_SESSION['name'];

// Sanitize the submission ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false) {
    exit("Invalid submission ID.");
}

$row = [];

if (defined('DEMO_MODE') && DEMO_MODE) {
    // --- DEMO MODE MOCK DATA ---
    $row = [
        'id' => $id,
        'created_at' => date('Y-m-d H:i:s'),
        'deposit_type' => 'End of the Day',
        'drawer_number' => '1001',
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
        'total_amount' => 320.50
    ];
} else {
    // --- REAL DATABASE LOGIC ---
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM cashierdeposit WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submission | Deposits Portal</title>
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
<div class="alert alert-warning text-center fw-bold mb-0" role="alert">
    <i class="fa fa-exclamation-triangle"></i> Demo Mode: Database Connection Failed. Showing Mock Data.
</div>
<?php endif; ?>

<!-- Content -->
<div class="container mt-5 mb-5">
    <div class="glass-panel p-5 animate-fade-up">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Submission Details</h1>
            <span class="badge bg-success rounded-pill px-3 py-2">ID: #<?php echo isset($row['id']) ? htmlspecialchars($row['id']) : ''; ?></span>
        </div>

        <form>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" class="form-control" id="username" name="username" readonly value="<?php echo htmlspecialchars($username); ?>">
                        <label for="username">Username</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating form-floating-custom mb-3">
                        <input type="text" class="form-control" id="name" name="name" readonly value="<?php echo htmlspecialchars($name); ?>">
                        <label for="name">Name</label>
                    </div>
                </div>
            </div>

            <div class="table-responsive rounded shadow-sm">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Item Name</th>
                        <th class="py-3 text-center">Item Count</th>
                        <th class="py-3 text-end">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr>
                        <td>Submission ID</td>
                        <td class="text-center"><?php echo isset($row['id']) ? htmlspecialchars($row['id']) : ''; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Date/Time</td>
                        <td class="text-center"><?php echo isset($row['created_at']) ? htmlspecialchars($row['created_at']) : ''; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Deposit Type</td>
                        <td class="text-center"><?php echo isset($row['deposit_type']) ? htmlspecialchars($row['deposit_type']) : ''; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Drawer Number</td>
                        <td class="text-center"><?php echo isset($row['drawer_number']) ? htmlspecialchars($row['drawer_number']) : ''; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Cash</td>
                        <td></td>
                        <td class="text-end fw-bold">$<?php echo isset($row['cash_amount']) ? number_format((float)$row['cash_amount'], 2) : '0.00'; ?></td>
                    </tr>
                    <tr>
                        <td>Check 21 - Deposit</td>
                        <td class="text-center"><?php echo isset($row['check21_deposit_count']) ? htmlspecialchars($row['check21_deposit_count']) : '0'; ?></td>
                        <td class="text-end">$<?php echo isset($row['check21_deposit_amount']) ? number_format((float)$row['check21_deposit_amount'], 2) : '0.00'; ?></td>
                    </tr>
                    <tr>
                        <td>CEO Check Deposit</td>
                        <td class="text-center"><?php echo isset($row['ceo_check_deposit_count']) ? htmlspecialchars($row['ceo_check_deposit_count']) : '0'; ?></td>
                        <td class="text-end">$<?php echo isset($row['ceo_check_deposit_amount']) ? number_format((float)$row['ceo_check_deposit_amount'], 2) : '0.00'; ?></td>
                    </tr>
                    <tr>
                        <td>Manual Check Deposit</td>
                        <td class="text-center"><?php echo isset($row['manual_check_deposit_count']) ? htmlspecialchars($row['manual_check_deposit_count']) : '0'; ?></td>
                        <td class="text-end">$<?php echo isset($row['manual_check_deposit_amount']) ? number_format((float)$row['manual_check_deposit_amount'], 2) : '0.00'; ?></td>
                    </tr>
                    <tr>
                        <td>Money Order</td>
                        <td class="text-center"><?php echo isset($row['money_order_deposit_count']) ? htmlspecialchars($row['money_order_deposit_count']) : '0'; ?></td>
                        <td class="text-end">$<?php echo isset($row['money_order_deposit_amount']) ? number_format((float)$row['money_order_deposit_amount'], 2) : '0.00'; ?></td>
                    </tr>
                    <tr>
                        <td>Credit and Debit Cards</td>
                        <td class="text-center"><?php echo isset($row['credit_debit_cards_count']) ? htmlspecialchars($row['credit_debit_cards_count']) : '0'; ?></td>
                        <td class="text-end">$<?php echo isset($row['credit_debit_cards_amount']) ? number_format((float)$row['credit_debit_cards_amount'], 2) : '0.00'; ?></td>
                    </tr>
                    <tr>
                        <td>Pre-Deposits</td>
                        <td class="text-center"><?php echo isset($row['pre_deposit_count']) ? htmlspecialchars($row['pre_deposit_count']) : '0'; ?></td>
                        <td class="text-end">$<?php echo isset($row['pre_deposit_amount']) ? number_format((float)$row['pre_deposit_amount'], 2) : '0.00'; ?></td>
                    </tr>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <td class="py-3"><strong>Total</strong></td>
                        <td class="text-center py-3"><strong><?php echo isset($row['total_count']) ? htmlspecialchars($row['total_count']) : '0'; ?></strong></td>
                        <td class="text-end py-3"><strong>$<?php echo isset($row['total_amount']) ? number_format((float)$row['total_amount'], 2) : '0.00'; ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            </div>

            <div class="row mt-4 d-print-none">
                <div class="col-md-6 mb-2">
                     <button type="button" onclick="printForm()" class="btn btn-lg btn-primary w-100" id="printButton">
                        <i class="fas fa-print me-2"></i> Print Form
                     </button>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="history.php" class="btn btn-lg btn-outline-secondary w-100" id="openHistoryButton">
                        <i class="fas fa-history me-2"></i> Return to History
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
    window.onbeforeprint = function() {
        var openHistoryButton = document.getElementById('openHistoryButton');
        var printButton = document.getElementById('printButton');
        var navbar = document.querySelector('nav');
        if(printButton) printButton.style.display = 'none';
        if(openHistoryButton) openHistoryButton.style.display = 'none';
        if(navbar) navbar.style.display = 'none';
    }

    function printForm() {
        window.print();
    }

    window.onafterprint = function() {
        var openHistoryButton = document.getElementById('openHistoryButton');
        var printButton = document.getElementById('printButton');
        var navbar = document.querySelector('nav');
        if(printButton) printButton.style.display = 'block';
        if(openHistoryButton) openHistoryButton.style.display = 'block';
        if(navbar) navbar.style.display = 'flex';
    }
</script>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
<?php include "footer.php"; ?>
</html>
