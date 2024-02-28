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


// Fetch unverified submissions from the database
$sql = "SELECT * FROM cashierdeposit WHERE verified = 0";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Supervisor Interface</title>
        <!-- Add any CSS or Bootstrap here -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="styles.css">
        <style>
            /* Additional styles can be added here */
        </style>
    </head>
    <body>
    <!-- Navigation -->
    <?php include "navbar.php"; ?>

    <div class="container-wrapper">
        <h1>Supervisor Interface</h1>
        <?php if (!$isAdmin && !$isSupervisor) : ?>
        <h2>Access Denied</h2>
        <p>You do not have permission to access this page.</p>
        <?php else : ?>
        <h2>List of Unverified Submissions:</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Submission ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Deposit Type</th>
                    <th>Drawer Number</th>
                    <th>Cash Amount</th>
                    <th>Check21 Deposit Amount</th>
                    <th>Check21 Deposit Count</th>
                    <th>CEO Deposit Amount</th>
                    <th>CEO Deposit Count</th>
                    <th>Manual Check Deposit</th>
                    <th>Manual Check Deposit Count</th>
                    <th>Money Order Deposit Amount</th>
                    <th>Money Order Deposit Count</th>
                    <th>Credit and Debit Cards Amount</th>
                    <th>Credit and Debit Cards Count</th>
                    <th>Pre-Deposit Amount</th>
                    <th>Pre-Deposit Count</th>
                    <th>Total Amount</th>
                    <th>Total Count</th>
                    <th>Verified</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['deposit_type']; ?></td>
                        <td><?php echo $row['drawer_number']; ?></td>
                        <td><?php echo $row['cash_amount']; ?></td>
                        <td><?php echo $row['check21_deposit_amount']; ?></td>
                        <td><?php echo $row['check21_deposit_count']; ?></td>
                        <td><?php echo $row['ceo_check_deposit_amount']; ?></td>
                        <td><?php echo $row['ceo_check_deposit_count']; ?></td>
                        <td><?php echo $row['manual_check_deposit_amount']; ?></td>
                        <td><?php echo $row['manual_check_deposit_count']; ?></td>
                        <td><?php echo $row['money_order_deposit_amount']; ?></td>
                        <td><?php echo $row['money_order_deposit_count']; ?></td>
                        <td><?php echo $row['credit_debit_cards_amount']; ?></td>
                        <td><?php echo $row['credit_debit_cards_count']; ?></td>
                        <td><?php echo $row['pre_deposit_amount']; ?></td>
                        <td><?php echo $row['pre_deposit_count']; ?></td>
                        <td><?php echo $row['total_amount']; ?></td>
                        <td><?php echo $row['total_count']; ?></td>
                        <td><?php echo ($row['verified'] ? '<span style="color: green">Yes</span>' : '<span style="color: red">No</span>'); ?></td>
                        <td>
                            <a href="verifysubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Verify</a>
                            <a href="editsubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="deletesubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                            <a href="viewsubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-success">View</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php endif; // End if (!$isAdmin)
}

mysqli_close($conn);
?>
