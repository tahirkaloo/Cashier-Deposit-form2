<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page
    exit;
}

// If the user is logged in, retrieve the name from the session
$username = $_SESSION['username'];
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cashier Deposit</title>
    <!-- CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        /* Additional styles can be added here */
        .total {
            font-weight: bold;
        }
    </style>
</head>
<body>
<!-- Navigation -->
<?php include "navbar.php"; ?>

<!-- Content -->
<div class="container">
    <h1>Cashier Form</h1>
    <form action="submitcashier.php" method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" readonly value="<?php echo $username; ?>">
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" readonly value="<?php echo $name; ?>">
        </div>
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
                        <option value="endoftheday">End of Day</option>
                        <option value="midday">Mid Day</option>
                    </td>
                </tr>
                <tr>
                    <td>Drawer Number</td>
                    <td><input type="number" name="DrawerNumber" class="form-control" step="1" minlength="4" maxlength="4"></td>
                </tr>
                <tr>
                    <td>Cash</td>
                    <td><input type="number" name="Cash" class="form-control amount" step="0.01"></td>
                </tr>
                <tr>
                    <td>Check 21 - Deposit</td>
                    <td><input type="number" name="Check21DepositAmount" class="form-control amount" step="0.01"></td>
                    <td><input type="number" name="Check21DepositCount" class="form-control count" step="1"></td>
                </tr>
                <tr>
                    <td>CEO Check Deposit</td>
                    <td><input type="number" name="CEOCheckDepositAmount" class="form-control amount" step="0.01"></td>
                    <td><input type="number" name="CEOCheckDepositCount" class="form-control count" step="1"></td>
                </tr>
                <tr>
                    <td>Manual Check Deposit</td>
                    <td><input type="number" name="ManualCheckDepositAmount" class="form-control amount" step="0.01"></td>
                    <td><input type="number" name="ManualCheckDepositCount" class="form-control count" step="1"></td>
                </tr>
                <tr>
                    <td>Money Order</td>
                    <td><input type="number" name="MoneyOrderAmount" class="form-control amount" step="0.01"></td>
                    <td><input type="number" name="MoneyOrderCount" class="form-control count" step="1"></td>
                </tr>
                <tr>
                    <td>Credit and Debit Cards</td>
                    <td><input type="number" name="CreditDebitCardsAmount" class="form-control amount" step="0.01"></td>
                    <td><input type="number" name="CreditDebitCardsCount" class="form-control count" step="1"></td>
                </tr>
                <tr>
                    <td>Pre-Deposits</td>
                    <td><input type="number" name="PreDepositsAmount" class="form-control amount" step="0.01"></td>
                    <td><input type="number" name="PreDepositsCount" class="form-control count" step="1"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total">
                    <td>Total</td>
                    <td><span id="totalAmount">$0.00</span>
                    <td><span id="totalCount">0</span>
                </td>
                </tr>
            </tfoot>
        </table>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        $('.amount, .count').on('input', function() {
            calculateTotal();
        });

        function calculateTotal() {
            var totalAmount = 0;
            var totalCount = 0;

            $('.amount').each(function() {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    totalAmount += value;
                }
            });

            $('.count').each(function() {
                var value = parseInt($(this).val());
                if (!isNaN(value)) {
                    totalCount += value;
                }
            });

            $('#totalAmount').text('$' + totalAmount.toFixed(2));
            $('#totalCount').text(totalCount);
        }
    });
</script>
</body>
</html>
