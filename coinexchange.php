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
$name = $_SESSION['name'];
$username = $_SESSION['username'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $bill_amount_exchanged = mysqli_real_escape_string($conn, $_POST['bill_amount_exchanged']);

    // Insert data into the database
    $sql = "INSERT INTO coinexchange (date, bill_amount_exchanged, name, username) VALUES ('$date', '$bill_amount_exchanged', '$name', '$username')";
    if (mysqli_query($conn, $sql)) {
        echo "Coin exchange record added successfully";
        // Redirect to a confirmation page or back to the same page
        // header("Location: confirmation.php");
        // exit;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
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
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-5">
    <h1 class="mb-4">Coin Exchange</h1>
    <form method="post" action="">
      <div class="form-row">
        <div class="col-md-2">
          <label for="date">Date:</label>
          <input type="date" id="date" name="date" class="form-control" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d'); ?>">
        </div>
        <div class="col-md-6">
          <label for="bill_amount_exchanged">Bill Amount:</label>
          <input type="number" id="bill_amount_exchanged" name="bill_amount_exchanged" class="form-control" required>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary mt-md-4">Submit</button>
        </div>
      </div>
    </form>
  </div>

  <div class="container mt-5">
    <table class="table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Name</th>
          <th>Username</th>
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