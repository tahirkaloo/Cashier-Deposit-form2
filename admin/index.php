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


//Get the total number of logs from the database
$sql = "SELECT COUNT(*) as total_logs FROM logs";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$totalLogs = $row['total_logs'];

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Customize admin panel styles */
        body {
            background-color: #f8f9fa;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .admin-title {
            font-size: 36px;
            font-weight: bold;
            color: #333;
        }

        .admin-cards {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }

        .admin-card {
            width: 300px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .admin-card:hover {
            transform: translateY(-5px);
        }

        .card-icon {
            text-align: center;
            margin-bottom: 20px;
        }

        .card-icon img {
            max-width: 150px;
        }

        .card-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .admin-card-link {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .admin-metrics {
            display: flex;
            justify-content: space-around;
            gap: 30px;
        }

        .admin-metric {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .admin-metric:hover {
            transform: translateY(-5px);
        }

        .admin-metric-label {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .admin-metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <?php include 'admin-navbar.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Welcome to Admin Panel</h1>
        </div>
        <div class="admin-cards">
            <a href="adminlog.php" class="admin-card">
                <div class="card-icon">
                    <img src="../images/AWS images/adminmileage.gif" alt="Logs Icon">
                </div>
                <div class="card-title">View Logs</div>
            </a>
            <a href="contact_responses.php" class="admin-card">
                <div class="card-icon">
                    <img src="../images/AWS images/admincontactus.gif" alt="Contact Icon">
                </div>
                <div class="card-title">Contact Responses</div>
            </a>
            <a href="manage-users.php" class="admin-card">
                <div class="card-icon">
                    <img src="../images/AWS images/adminmanageusers.gif" alt="Manage Users Icon">
                </div>
                <div class="card-title">Manage Users</div>
            </a>
      </div>
        <div class="admin-metrics">
            <div class="admin-metric">
                <p class="admin-metric-label">Total Logs</p>
                <p class="admin-metric-value"><?php echo $totalLogs; ?></p>
            </div>
            <div class="admin-metric">
                <p class="admin-metric-label">Contact Form Responses</p>
                <p class="admin-metric-value"><?php echo $totalContactResponses; ?></p>
            </div>
            <div class="admin-metric">
                <p class="admin-metric-label">Total Users</p>
                <p class="admin-metric-value"><?php echo $totalusers; ?></p>
            </div>
        </div>
    </div>
</body>

</html>

