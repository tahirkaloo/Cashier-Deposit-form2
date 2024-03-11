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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';
    $filterUser = isset($_POST['user']) ? mysqli_real_escape_string($conn, $_POST['user']) : '';
    $date = isset($_POST['date']) ? mysqli_real_escape_string($conn, $_POST['date']) : '';

    // Handle search
    if (!empty($search)) {
        $sql .= " AND (id LIKE '%$search%' OR username LIKE '%$search%' OR name LIKE '%$search%')";
    }
    // Handle user filter
    if (!empty($filterUser)) {
        $sql .= " AND username = '$filterUser'";
    }
    //Handle date filter
    if (!empty($date)) {
        // Check if the date format submitted matches the expected format
        if (strtotime($date)) {
            $sql .= " AND DATE(created_at) = '$date'";
        } else {
            echo "Error: Invalid date format.";
        }
    }
}

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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <!-- Navigation -->
    <?php include "navbar.php"; ?>

    <div class="container-wrapper">
        <h1>Supervisor Interface</h1>
        <?php if (!$isAdmin && !$isSupervisor) {
            header("Location: accessdenied.html");
            exit; // Make sure to exit after redirecting
        } else { ?>
            <h2>List of Unverified Submissions:</h2>
            
            <!-- Filter and search form -->
            <div class="mb-3 bg-light rounded shadow animate__animated animate__fadeIn animate__faster text-dark ">
            <form action="" method="post" class="mb-3">
                <div class="form-row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search by ID, Username, or Name" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="user" class="form-control">
                            <option value="">Filter by User</option>
                            <?php
                            // Fetch distinct usernames from the database for user dropdown
                            $userQuery = "SELECT DISTINCT username FROM cashierdeposit";
                            $userResult = mysqli_query($conn, $userQuery);
                            if ($userResult && mysqli_num_rows($userResult) > 0) {
                                while ($userRow = mysqli_fetch_assoc($userResult)) {
                                    echo "<option value=\"" . $userRow['username'] . "\"" . ($filterUser == $userRow['username'] ? " selected" : "") . ">" . $userRow['username'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date" class="form-control" placeholder="Filter by Date" value="<?php echo isset($date) ? htmlspecialchars($date) : ''; ?>">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="supervisor.php" class="btn btn-secondary">Reset Filters</a>
                    </div>
                </div>
            </form>
            </div>

            <!-- Table of unverified submissions -->
            <div class="table-responsive  mt-5 bg-light rounded shadow animate__animated animate__fadeIn animate__faster text-dark mb-5">
            <table class="table table-striped table-condensed table-bordered table-hover animate__animated animate__fadeIn animate__faster table-responsive">
                <thead>
                <tr>
                    <th>Submission ID</th>
                    <th>Date/time</th>
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
                </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
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
                        <td><strong>$<?php echo $row['total_amount']; ?></strong></td>
                        <td><strong><?php echo $row['total_count']; ?></strong></td>
                        <td><strong><?php echo ($row['verified'] ? '<span style="color: green">Yes</span>' : '<span style="color: red">No</span>'); ?></strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <a href="verifysubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-lg btn-primary">Verify</a>
                        </td>
                        <td>
                            <a href="editsubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                        </td>
                        <td>
                            <a href="deletesubmission.php?id=<?php echo $row['id']; ?>&table=cashierdeposit" class="btn btn-danger">Delete</a>
                        </td>
                        <td>
                            <a href="viewsubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-success">View</a>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php } 
    }
    mysqli_close($conn);
    ?>
    </div>
    </body>
    </html>
