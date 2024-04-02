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
<html lang="en">
<head>
    <title>Cashier Deposit</title>
    <!-- CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="styles.css">
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
<div class="container bg-light rounded shadow animate__animated animate__fadeIn animate__faster text-dark mt-2">
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
                    <th>Item Count</th> 
                    <th>Amount</th> 
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Deposit type</td>
                    <td>
                        <select name="DepositType" class="form-control">
                            <option value="End of the day">End of Day</option>
                            <option value="Mid day">Mid Day</option>
                        </select>
                    </td>
                    <td></td> 
                </tr>
                <tr>
                    <td>Drawer Number</td>
                    <td><input type="text" pattern="\d{4}" title="Please enter your 4-digit drawer number from RevenueOne" name="DrawerNumber" class="form-control" required></td>
                </tr>
                <tr>
                    <td>Cash</td>
                    <td></td> 
                    <td><input type="number" name="Cash" class="form-control amount" step="0.01"></td>
                </tr>
                <tr>
                    <td>Check 21 - Deposit</td>
                    <td><input type="number" name="Check21DepositCount" class="form-control count" step="1"></td> 
                    <td><input type="number" name="Check21DepositAmount" class="form-control amount" step="0.01"></td> 
                </tr>
                <tr>
                    <td>CEO Check Deposit</td>
                    <td><input type="number" name="CEOCheckDepositCount" class="form-control count" step="1"></td> 
                    <td><input type="number" name="CEOCheckDepositAmount" class="form-control amount" step="0.01"></td> 
                </tr>
                <tr>
                    <td>Manual Check Deposit</td>
                    <td><input type="number" name="ManualCheckDepositCount" class="form-control count" step="1"></td> 
                    <td><input type="number" name="ManualCheckDepositAmount" class="form-control amount" step="0.01"></td> 
                </tr>
                <tr>
                    <td>Money Order</td>
                    <td><input type="number" name="MoneyOrderCount" class="form-control count" step="1"></td> 
                    <td><input type="number" name="MoneyOrderAmount" class="form-control amount" step="0.01"></td> 
                </tr>
                <tr>
                    <td>Credit and Debit Cards</td>
                    <td><input type="number" name="CreditDebitCardsCount" class="form-control count" step="1"></td> 
                    <td><input type="number" name="CreditDebitCardsAmount" class="form-control amount" step="0.01"></td> 
                </tr>
                <tr>
                    <td>Pre-Deposits</td>
                    <td><input type="number" name="PreDepositsCount" class="form-control count" step="1"></td> 
                    <td><input type="number" name="PreDepositsAmount" class="form-control amount" step="0.01"></td> 
                </tr>
            </tbody>
            <tfoot>
                <tr class="total">
                    <td>Total</td>
                    <td><span id="totalCount">0</span></td> 
                    <td><span id="totalAmount">$0.00</span></td> 
                </tr>
            </tfoot>
        </table>





        <!-- <table class="table">
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
                        <option value="End of the day">End of Day</option>
                        <option value="Mid day">Mid Day</option>
                    </td>
                </tr>
                <tr>
                    <td>Drawer Number</td>
                    <td><input type="text" pattern="\d{4}" title="Please enter your 4-digit drawer number from RevenueOne" name="DrawerNumber" class="form-control" required></td>
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
        </table> -->
        <button type="submit" class="btn btn-lg btn-primary btn-block">Submit</button>
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


    $(document).ready(function(){
    $('.amount').on('input', function() {
        var check21Amount = parseFloat($('input[name="Check21DepositAmount"]').val());
        var ceoAmount = parseFloat($('input[name="CEOCheckDepositAmount"]').val());
        var manualAmount = parseFloat($('input[name="ManualCheckDepositAmount"]').val());
        var moneyOrderAmount = parseFloat($('input[name="MoneyOrderAmount"]').val());
        var creditDebitAmount = parseFloat($('input[name="CreditDebitCardsAmount"]').val());
        var preDepositsAmount = parseFloat($('input[name="PreDepositsAmount"]').val());
        
        var check21CountInput = $('input[name="Check21DepositCount"]');
        var ceoCountInput = $('input[name="CEOCheckDepositCount"]');
        var manualCountInput = $('input[name="ManualCheckDepositCount"]');
        var moneyOrderCountInput = $('input[name="MoneyOrderCount"]');
        var creditDebitCountInput = $('input[name="CreditDebitCardsCount"]');
        var preDepositsCountInput = $('input[name="PreDepositsCount"]');
        
        if (!isNaN(check21Amount)) {
            check21CountInput.attr('required', 'required');
        } else {
            check21CountInput.removeAttr('required');
        }
        
        if (!isNaN(ceoAmount)) {
            ceoCountInput.attr('required', 'required');
        } else {
            ceoCountInput.removeAttr('required');
        }
        
        if (!isNaN(manualAmount)) {
            manualCountInput.attr('required', 'required');
        } else {
            manualCountInput.removeAttr('required');
        }
        
        if (!isNaN(moneyOrderAmount)) {
            moneyOrderCountInput.attr('required', 'required');
        } else {
            moneyOrderCountInput.removeAttr('required');
        }
        
        if (!isNaN(creditDebitAmount)) {
            creditDebitCountInput.attr('required', 'required');
        } else {
            creditDebitCountInput.removeAttr('required');
        }
        
        if (!isNaN(preDepositsAmount)) {
            preDepositsCountInput.attr('required', 'required');
        } else {
            preDepositsCountInput.removeAttr('required');
        }

    });
});


$(document).ready(function(){
    $('.amount, .count').on('input', function() {
        var check21AmountInput = $('input[name="Check21DepositAmount"]');
        var ceoAmountInput = $('input[name="CEOCheckDepositAmount"]');
        var manualAmountInput = $('input[name="ManualCheckDepositAmount"]');
        var moneyOrderAmountInput = $('input[name="MoneyOrderAmount"]');
        var creditDebitAmountInput = $('input[name="CreditDebitCardsAmount"]');
        var preDepositsAmountInput = $('input[name="PreDepositsAmount"]');
        
        var check21Count = parseInt($('input[name="Check21DepositCount"]').val());
        var ceoCount = parseInt($('input[name="CEOCheckDepositCount"]').val());
        var manualCount = parseInt($('input[name="ManualCheckDepositCount"]').val());
        var moneyOrderCount = parseInt($('input[name="MoneyOrderCount"]').val());
        var creditDebitCount = parseInt($('input[name="CreditDebitCardsCount"]').val());
        var preDepositsCount = parseInt($('input[name="PreDepositsCount"]').val());
        
        if (!isNaN(check21Count) && check21Count > 0) {
            check21AmountInput.attr('required', 'required');
        } else {
            check21AmountInput.removeAttr('required');
        }
        
        if (!isNaN(ceoCount) && ceoCount > 0) {
            ceoAmountInput.attr('required', 'required');
        } else {
            ceoAmountInput.removeAttr('required');
        }
        
        if (!isNaN(manualCount) && manualCount > 0) {
            manualAmountInput.attr('required', 'required');
        } else {
            manualAmountInput.removeAttr('required');
        }
        
        if (!isNaN(moneyOrderCount) && moneyOrderCount > 0) {
            moneyOrderAmountInput.attr('required', 'required');
        } else {
            moneyOrderAmountInput.removeAttr('required');
        }
        
        if (!isNaN(creditDebitCount) && creditDebitCount > 0) {
            creditDebitAmountInput.attr('required', 'required');
        } else {
            creditDebitAmountInput.removeAttr('required');
        }
        
        if (!isNaN(preDepositsCount) && preDepositsCount > 0) {
            preDepositsAmountInput.attr('required', 'required');
        } else {
            preDepositsAmountInput.removeAttr('required');
        }
    });
});
</script>


</body>
<?php include 'footer.php'; ?>
</html>
