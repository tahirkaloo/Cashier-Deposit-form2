<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the user is a supervisor or admin and redirect accordingly
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$isSupervisor = (isset($_SESSION['role']) && $_SESSION['role'] === 'supervisor');

if (!$isAdmin && !$isSupervisor) {
    header("Location: accessdenied.html");
    exit;
}

// Initialize variables
$date = date('Y-m-d');
$bill_amount_exchanged = 0;
$deposit_type = 'End of the Day';
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
$errorMessage = '';
$successMessage = '';
$exchanges = [];

if (defined('DEMO_MODE') && DEMO_MODE) {
    // --- DEMO MODE MOCK DATA ---
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
         // Simulate successful insertion
         $successMessage = "Coin Exchange recorded successfully (Demo Mode)";
    }

    // Mock Data for Table
    $exchanges = [
        [
            'id' => 1,
            'date' => date('Y-m-d'),
            'name' => 'demo_user',
            'username' => 'demo',
            'deposit_type' => 'End of the Day',
            'bill_amount_exchanged' => 20.00
        ],
        [
            'id' => 2,
            'date' => date('Y-m-d', strtotime('-1 day')),
            'name' => 'Jane Doe',
            'username' => 'jane',
            'deposit_type' => 'Mid day',
            'bill_amount_exchanged' => 50.00
        ]
    ];

} else {
    // --- REAL DATABASE LOGIC ---
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Validate and sanitize input
      $date = mysqli_real_escape_string($conn, $_POST['date']);
      $deposit_type = mysqli_real_escape_string($conn, $_POST['deposit_type']);
      $bill_amount_exchanged = mysqli_real_escape_string($conn, $_POST['bill_amount_exchanged']);

      // Check if an entry already exists for the given date and deposit type
      $checkSql = "SELECT * FROM coinexchange WHERE date = '$date' AND deposit_type = '$deposit_type'";
      $checkResult = mysqli_query($conn, $checkSql);

      if ($checkResult && mysqli_num_rows($checkResult) > 0) {
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
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $exchanges[] = $row;
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Coin Exchange | Deposits Portal</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <?php include 'navbar.php'; ?>

  <!-- Demo Mode Warning -->
    <?php if (defined('DEMO_MODE') && DEMO_MODE): ?>
    <div class="alert alert-warning text-center fw-bold" role="alert">
        <i class="fa fa-exclamation-triangle"></i> Demo Mode: Database Connection Failed. Showing Mock Data.
    </div>
    <?php endif; ?>

  <div class="container mt-5 mb-5">

    <div class="glass-panel p-4 animate-fade-up mb-5">
        <h2 class="mb-4"><i class="fas fa-coins text-primary me-2"></i>New Coin Exchange</h2>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger alert-custom" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success alert-custom" role="alert">
                 <i class="fas fa-check-circle me-2"></i> <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
          <div class="row">
            <div class="col-md-3 mb-3">
               <div class="form-floating form-floating-custom">
                  <input type="date" id="date" name="date" class="form-control" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d'); ?>">
                  <label for="date">Date</label>
               </div>
            </div>
            <div class="col-md-4 mb-3">
               <div class="form-floating form-floating-custom">
                  <select id="deposit_type" name="deposit_type" class="form-select">
                    <option value="End of the Day">End of the Day</option>
                    <option value="Mid day">Mid day</option>
                  </select>
                  <label for="deposit_type">Deposit Type</label>
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <div class="form-floating form-floating-custom">
                  <input type="number" id="bill_amount_exchanged" name="bill_amount_exchanged" class="form-control" required step="0.01" placeholder="0.00">
                  <label for="bill_amount_exchanged">Bill Amount</label>
               </div>
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-center">
              <button type="submit" class="btn btn-gradient w-100 h-75">Submit</button>
            </div>
          </div>
        </form>
    </div>

    <div class="glass-panel p-4 animate-fade-up">
        <h3 class="mb-4"><i class="fas fa-list text-primary me-2"></i>Exchange History</h3>
        <div class="table-responsive rounded shadow-sm">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Name</th>
              <th>Username</th>
              <th>Deposit Type</th>
              <th>Bill Amount</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody class="bg-white">
            <?php foreach ($exchanges as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['deposit_type']); ?></td>
                    <td>$<?php echo htmlspecialchars($row['bill_amount_exchanged']); ?></td>
                    <td>
                        <a href="deletesubmission.php?id=<?php echo $row['id']; ?>&table=coinexchange" class="btn btn-danger btn-sm text-white" onclick="return confirm('Are you sure you want to delete this item?');">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
