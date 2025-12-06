<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Define $isAdmin and $isSupervisor based on the user's role
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$isSupervisor = (isset($_SESSION['role']) && $_SESSION['role'] === 'supervisor');

if (!$isAdmin && !$isSupervisor) {
    header("Location: accessdenied.html");
    exit;
}

// Initialize data array
$submissions = [];
$distinctUsers = [];

$search = isset($_POST['search']) ? $_POST['search'] : '';
$filterUser = isset($_POST['user']) ? $_POST['user'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : '';

if (defined('DEMO_MODE') && DEMO_MODE) {
    // --- DEMO MODE MOCK DATA ---
    $submissions = [
        [
            'id' => 101,
            'created_at' => date('Y-m-d H:i:s'),
            'username' => 'demo_cashier1',
            'name' => 'Alice Smith',
            'deposit_type' => 'End of the Day',
            'drawer_number' => '1001',
            'cash_amount' => 500.00,
            'check21_deposit_amount' => 100.00,
            'check21_deposit_count' => 2,
            'ceo_check_deposit_amount' => 0,
            'ceo_check_deposit_count' => 0,
            'manual_check_deposit_amount' => 0,
            'manual_check_deposit_count' => 0,
            'money_order_deposit_amount' => 0,
            'money_order_deposit_count' => 0,
            'credit_debit_cards_amount' => 250.00,
            'credit_debit_cards_count' => 5,
            'pre_deposit_amount' => 0,
            'pre_deposit_count' => 0,
            'total_amount' => 850.00,
            'total_count' => 7,
            'verified' => 0
        ],
        [
            'id' => 102,
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours')),
            'username' => 'demo_cashier2',
            'name' => 'Bob Jones',
            'deposit_type' => 'Mid Day',
            'drawer_number' => '1002',
            'cash_amount' => 120.00,
            'check21_deposit_amount' => 0,
            'check21_deposit_count' => 0,
            'ceo_check_deposit_amount' => 0,
            'ceo_check_deposit_count' => 0,
            'manual_check_deposit_amount' => 0,
            'manual_check_deposit_count' => 0,
            'money_order_deposit_amount' => 0,
            'money_order_deposit_count' => 0,
            'credit_debit_cards_amount' => 50.00,
            'credit_debit_cards_count' => 1,
            'pre_deposit_amount' => 0,
            'pre_deposit_count' => 0,
            'total_amount' => 170.00,
            'total_count' => 1,
            'verified' => 0
        ]
    ];

    $distinctUsers = [['username' => 'demo_cashier1'], ['username' => 'demo_cashier2']];

} else {
    // --- REAL DATABASE LOGIC ---
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM cashierdeposit WHERE verified = 0";

    // Handle search
    if (!empty($search)) {
        $safeSearch = mysqli_real_escape_string($conn, $search);
        $sql .= " AND (id LIKE '%$safeSearch%' OR username LIKE '%$safeSearch%' OR name LIKE '%$safeSearch%')";
    }
    // Handle user filter
    if (!empty($filterUser)) {
        $safeUser = mysqli_real_escape_string($conn, $filterUser);
        $sql .= " AND username = '$safeUser'";
    }
    //Handle date filter
    if (!empty($date)) {
        if (strtotime($date)) {
             $safeDate = mysqli_real_escape_string($conn, $date);
            $sql .= " AND DATE(created_at) = '$safeDate'";
        }
    }

    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $submissions[] = $row;
        }
    } else {
         echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Fetch distinct users
    $userQuery = "SELECT DISTINCT username FROM cashierdeposit";
    $userResult = mysqli_query($conn, $userQuery);
    if ($userResult) {
        while ($userRow = mysqli_fetch_assoc($userResult)) {
            $distinctUsers[] = $userRow;
        }
    }

    mysqli_close($conn);
}
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

<div class="container-wrapper">
    <h1>Supervisor Interface</h1>

    <h2>List of Unverified Submissions:</h2>

    <!-- Filter and search form -->
    <div class="mb-3 bg-light rounded shadow animate__animated animate__fadeIn animate__faster text-dark ">
    <form action="" method="post" class="mb-3">
        <div class="form-row p-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by ID, Username, or Name" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select name="user" class="form-control">
                    <option value="">Filter by User</option>
                    <?php
                    foreach ($distinctUsers as $userRow) {
                        echo "<option value=\"" . htmlspecialchars($userRow['username']) . "\"" . ($filterUser == $userRow['username'] ? " selected" : "") . ">" . htmlspecialchars($userRow['username']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" placeholder="Filter by Date" value="<?php echo htmlspecialchars($date); ?>">
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
        <?php foreach ($submissions as $row) : ?>
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
                <td colspan="9"></td>
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
                <td colspan="9"></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
</body>
</html>
