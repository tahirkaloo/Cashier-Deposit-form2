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

// Check if the user is a supervisor or admin and redirect accordingly
$isAdmin = ($_SESSION['role'] === 'admin');
$isSupervisor = ($_SESSION['role'] === 'supervisor');

if (!$isAdmin && !$isSupervisor) {
    header("Location: accessdenied.html");
    exit;
}

// Initialize variables
$date = date('Y-m-d');
$bill_amount_exchanged = 0;
$deposit_type = 'End of the Day';
$name = $_SESSION['name'];
$username = $_SESSION['username'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate and sanitize input
  $date = mysqli_real_escape_string($conn, $_POST['date']);
  $deposit_type = mysqli_real_escape_string($conn, $_POST['deposit_type']);
  $bill_amount_exchanged = mysqli_real_escape_string($conn, $_POST['bill_amount_exchanged']);

  // Check if an entry already exists for the given date and deposit type
  $checkSql = "SELECT * FROM coinexchange WHERE date = '$date' AND deposit_type = '$deposit_type'";
  $checkResult = mysqli_query($conn, $checkSql);

  if (mysqli_num_rows($checkResult) > 0) {
      // Entry already exists, show error message
      $errorMessage = "An entry already exists for the selected date and deposit type.";
  } else {
      // Insert data into the database
      $sql = "INSERT INTO coinexchange (date, deposit_type, bill_amount_exchanged, name, username) VALUES ('$date', '$deposit_type', '$bill_amount_exchanged', '$name', '$username')";
      if (mysqli_query($conn, $sql)) {
           $successMessage = "Coin Exchange recorded successfully";
      } else {
          $errorMessage = "Error: " . $sql . "<br>" . mysqli_error($conn);
      }
  }
}


//Show data in table
$sql = "SELECT * FROM coinexchange";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    exit;
}


// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Coin Exchange</title>
  <!-- Add any CSS or Bootstrap here -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles.css">
</head>
<style>
  .success-message {
    color: green;
    margin-top: 5px;
    background-color: #f5f5f5;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 20px;
  }
.error-message {
    color: red;
    margin-top: 5px;
    background-color: #f5f5f5;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 20px;
  }
</style>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-5 bg-light rounded shadow animate__animated animate__fadeIn animate__faster text-dark mb-5">
    <h1 class="mb-4">Coin Exchange</h1>
    <form method="post" action="">
      <div class="form-row">
        <div class="col-md-2">
          <label for="date">Date:</label>
          <input type="date" id="date" name="date" class="form-control" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d'); ?>">
        </div>
        <div class="col-md-4">
          <label for="deposit_type">Deposit Type:</label>
          <select id="deposit_type" name="deposit_type" class="form-control">
            <option value="End of the Day">End of the Day</option>
            <option value="Mid day">Mid day</option>
          </select>
        </div>
        <div class="col-md-3">
          <label for="bill_amount_exchanged">Bill Amount:</label>
          <input type="number" id="bill_amount_exchanged" name="bill_amount_exchanged" class="form-control" required step="0.01">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-lg btn-primary mt-md-4">Submit</button>
        </div>
      </div>
    </form>
  </div>

  <?php if (!empty($errorMessage)): ?>
            <div class="error-message" style="color: red"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php endif; ?>

  <div class="container mt-5 bg-light rounded shadow animate__animated animate__fadeIn animate__faster text-dark mb-5">
    <table class="table table-striped table-bordered table-hover mt-5">
      <thead>
        <tr>
          <th>Date</th>
          <th>Name</th>
          <th>Username</th>
          <th>Deposit Type</th>
          <th>Bill Amount</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['date'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['deposit_type'] . "</td>";
            echo "<td>" . $row['bill_amount_exchanged'] . "</td>";
            echo "<td><a href='deletesubmission.php?id=" . $row['id'] . "&table=coinexchange' class='btn btn-danger'>Delete</a></td>";            echo "</tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
<?php include 'footer.php'; ?>
</html>