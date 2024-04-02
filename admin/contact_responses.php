<?php
session_start();
require_once '../db_connect.php';

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
    exit; // Exit if connection fails
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: 404.html");
    exit;
}

// Get all the responses from the database
$sql = "SELECT * FROM contactresponses";
$result = mysqli_query($conn, $sql);
$responses = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_free_result($result);

// Check if the request method is POST for deleting a response
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id']) && is_numeric($_POST['id'])) {
        $response_id = $_POST['id'];
        
        // Use prepared statement to delete the response from the database
        $sql = "DELETE FROM contactresponses WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $response_id); // Assuming 'id' is an integer
            if (mysqli_stmt_execute($stmt)) {
                // Record deleted successfully
                header("Location: contact_responses.php");
                exit;
            } else {
                echo "Error executing statement: " . mysqli_stmt_error($stmt);
            }
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid response ID.";
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Contact Responses</title>
  <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
  <?php include 'admin-navbar.php'; ?>
  <div class="admin-container">
    <div class="admin-header">
      <h1>Contact Responses</h1>
    </div>
    <div class="admin-content">
      <?php foreach ($responses as $response): ?>
        <div class="response-card">
          <div class="response-details">
            <p><strong>Name:</strong> <?php echo $response['name']; ?></p>
            <p><strong>Email:</strong> <?php echo $response['email']; ?></p>
            <p><strong>Message:</strong> <?php echo $response['message']; ?></p>
          </div>
          <div class="response-actions">
            <form method="post">
              <input type="hidden" name="id" value="<?php echo $response['id']; ?>">
              <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            <button class="btn btn-primary" onclick="url = 'mailto:<?php echo $response['email']; ?>'; window.location.href = url;">Email response</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
