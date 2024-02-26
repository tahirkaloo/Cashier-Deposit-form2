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
        <style>
            /* Additional styles can be added here */
        </style>
    </head>
    <body>
        <!-- Navigation -->
        <?php include "navbar.php"; ?>

        <div class="container">
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
                        <th>Cash Amount</th>
                        <th>Check21 Deposit Amount</th>
                        <th>Check21 Deposit Count</th>
                        <th>CEO Deposit Amount</th>
                        <th>CEO Deposit Count</th>
                        <th>Manual Check Deposit</th>
                        <th>Manual Check Deposit Count</th>
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
                            <td><?php echo $row['cash_amount']; ?></td>
                            <td><?php echo $row['check21_deposit_amount']; ?></td>
                            <td><?php echo $row['check21_deposit_count']; ?></td>
                            <td><?php echo $row['ceo_check_deposit_amount']; ?></td>
                            <td><?php echo $row['ceo_check_deposit_count']; ?></td>
                            <td><?php echo $row['manual_check_deposit_amount']; ?></td>
                            <td><?php echo $row['manual_check_deposit_count']; ?></td>
                            <td><a href="verifysubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Verify</a></td>
                            <td><a href="deletesubmission.php?id='<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a></td>
                            <td><a href="viewsubmission.php?id='<?php echo $row['id']; ?>" class="btn btn-primary">View</a></td>
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
