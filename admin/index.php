<?php
session_start();
require_once '../db_connect.php';

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}



// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php"); // Redirect to the login page
  exit;
}

// If the user is logged in, retrieve the name from the session
$name = $_SESSION['name'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

//Check if the user is admin and show error if not
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../accessdenied.html");
  exit;
}



// Simulated total response counts for demonstration
$totalMileageResponses = 10;

//Get the total number of contact form responses from the database
$sql = "SELECT COUNT(*) as total_contact_responses FROM contactresponses";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$totalContactResponses = $row['total_contact_responses'];


//Get the total number of users from the database
$sql = "SELECT COUNT(*) as total_users FROM users";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$totalusers = $row['total_users'];
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel</title>
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    /* Customize admin panel styles */
    .admin-container {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
      text-align: center;
    }

    .admin-header {
      margin-bottom: 30px;
    }

    .admin-title {
      font-size: 24px;
      font-weight: bold;
    }

    .admin-content {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .admin-card {
      width: 200px;
      height: 200px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 20px;
      background-color: #f0f0f0;
      border-radius: 8px;
      cursor: pointer;
    }

    .admin-card:hover {
      background-color: #e0e0e0;
    }

    .card-icon img {
      max-width: 100px;
      max-height: 100px;
    }

    .card-title {
      margin-top: 10px;
      font-size: 16px;
      font-weight: bold;
    }

    .admin-metrics {
      margin-top: 30px;
      display: flex;
      justify-content: center;
      gap: 30px;
    }

    .admin-metric {
      padding: 10px 20px;
      background-color: #f0f0f0;
      border-radius: 8px;
    }

    .admin-metric-label {
      font-size: 14px;
      font-weight: bold;
    }

    .admin-metric-value {
      font-size: 24px;
      font-weight: bold;
    }
  </style>
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZG3WQ5G3CH"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-ZG3WQ5G3CH');
</script>
<body>
  <?php include 'admin-navbar.php'; ?>

  <div class="admin-container">
    <div class="admin-header">
      <h1 class="admin-title">Admin Panel</h1>
    </div>
    <div class="admin-content">
      <div class="admin-card" onclick="window.location.href='adminlog.php';">
        <div class="card-icon">
          <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/admin-card-localmileageresponses.gif" alt="Mileage Icon">
        </div>
        <div class="card-title">Logs</div>
      </div>
      <div class="admin-card" onclick="window.location.href='contact_responses.php';">
        <div class="card-icon">
          <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/Admin+Contact+Us+index.gif" alt="Contact Icon">
        </div>
        <div class="card-title">Contact Form Responses</div>
      </div>
      <div class="admin-card" onclick="window.location.href='manage-users.php';">
        <div class="card-icon">
          <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/Admin+manage+users.gif" alt="Manage Users Icon">
        </div>
        <div class="card-title">Manage Users</div>
      </div>
      <!-- Add more cards for other response pages -->

    <div class="admin-metrics">
      <div class="admin-metric">
        <p class="admin-metric-label">Mileage Responses</p>
        <p class="admin-metric-value"><?php echo $totalMileageResponses; ?></p>
      </div>
      <div class="admin-metric">
        <p class="admin-metric-label">Contact Form Responses</p>
        <p class="admin-metric-value"><?php echo $totalContactResponses; ?></p>
      </div>
      <div class="admin-metric">
        <p class="admin-metric-label">Total Users</p>
        <p class="admin-metric-value"><?php echo $totalusers; ?></p>
      </div>
      <!-- Add more metrics for other response pages -->
    </div>
  </div>
  </div>
</body>
</html>

