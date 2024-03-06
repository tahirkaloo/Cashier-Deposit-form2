<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the user is a supervisor or admin
$isAdmin = ($_SESSION['role'] === 'admin');
$isSupervisor = ($_SESSION['role'] === 'supervisor');

// Fetch all submissions from the database
if ($isAdmin || $isSupervisor) {
    $sql = "SELECT * FROM cashierdeposit";
} else {
    $sql = "SELECT * FROM cashierdeposit WHERE username = '" . $_SESSION['username'] . "'";
}
$search = "";
$filterUser = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle search
    if (isset($_POST['search']) && !empty($_POST['search'])) {
        $search = mysqli_real_escape_string($conn, $_POST['search']);
        $sql .= " WHERE id LIKE '%$search%' OR username LIKE '%$search%' OR name LIKE '%$search%'";
    }
    // Handle user filter
    if (isset($_POST['user']) && !empty($_POST['user'])) {
        $filterUser = mysqli_real_escape_string($conn, $_POST['user']);
        $sql .= (strpos($sql, "WHERE") === false ? " WHERE" : " AND") . " username = '$filterUser'";
    }
    //Handle date filter
    if (isset($_POST['date']) && !empty($_POST['date'])) {
        $date = mysqli_real_escape_string($conn, $_POST['date']);
        $sql .= (strpos($sql, "WHERE") === false ? " WHERE" : " AND") . " DATE(created_at) = '$date'";
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
        <title>History</title>
        <!-- Add any CSS or Bootstrap here -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="styles.css">
        <style>
            /* Additional styles can be added here */
        </style>
    </head>
    <body>
    <!-- Navigation -->
    <?php include "navbar.php"; ?>
    <div class="container-fluid">
    <h1>History</h1>
    <!-- Filter and search form -->
    <form action="" method="post" class="mb-3">
        <div class="form-row bg-light rounded shadow animate__animated animate__fadeIn animate__faster text-dark">
            <?php
            // Check if the user is an admin or supervisor
            if ($isAdmin || $isSupervisor) {
            ?>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by ID, Username, or Name" value="<?php echo htmlspecialchars($search); ?>">
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
                            echo "<option value=\"" . $userRow['username'] . "\">" . $userRow['username'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <?php
            }
            ?>

            <div class="col-md-3">
                <input type="date" name="date" class="form-control" placeholder="Filter by Date" value="<?php echo htmlspecialchars($date); ?>">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="history.php" class="btn btn-secondary">Reset Filters</a>
            </div>
        </div>
    </form>

    <div class="container-fluid bg-light rounded shadow animate__animated animate__fadeIn animate__faster text-dark">
    <?php if (mysqli_num_rows($result) > 0) : ?>
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
                    <th>Action</th>
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
                        <td><?php echo $row['total_amount']; ?></td>
                        <td><?php echo $row['total_count']; ?></td>
                        <td><?php echo ($row['verified'] ? '<span style="color: green">Yes</span>' : '<span style="color: red">No</span>'); ?></td> 
                        <td>
                        <a href="viewsubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View</a>
                        <?php if (!$row['verified'] || $isAdmin || $isSupervisor): ?>
                            <a href="editsubmission.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                            <a href="deletesubmission.php?id=<?php echo $row['id']; ?>&table=cashierdeposit" class="btn btn-danger">Delete</a>
                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</div>  
</body>
<?php include 'footer.php'; ?>
</html>

<?php
mysqli_close($conn);
}   