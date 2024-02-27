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

// Ensure an ID is provided
if (!isset($_GET['id'])) {
    exit("Submission ID not provided.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false) {
    exit("Invalid submission ID.");
}

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

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

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
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <!-- Display existing data for editing -->
        <!-- Example: -->
        <div class="form-group">
            <label for="cash">Cash Amount</label>
            <input type="text" class="form-control" id="cash" name="cash" value="<?php echo $row['Cash']; ?>">
        </div>
        <div class="form-group">
            <label for="check21DepositAmount">Check 21 Deposit Amount</label>
            <input type="text" class="form-control" id="check21DepositAmount" name="check21DepositAmount" value="<?php echo $row['Check21DepositAmount']; ?>">
        </div>
        <!-- Add more fields for other data to be edited -->
        <button type="submit" class="btn btn-primary">Submit Changes</button>
    </form>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
