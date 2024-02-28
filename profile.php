<?php
session_start();
require_once 'db_connect.php';

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
} else {
    error_log("Connected to MySQL successfully");
}


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the user ID from the session
$userID = $_SESSION['user_id'];

// Retrieve the user's details from the database
$query = "SELECT username, email, role, name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $email = $row['email'];
    $role = $row['role'];
    $name = $row['name'];
} else {
    // Handle error if user data cannot be retrieved
    // Redirect or display an error message
    header("Location: error.php");
    exit;
}

$stmt->close();

// Password change form handling
if (isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    // Verify current password
    $verifyQuery = "SELECT password FROM users WHERE user_id = ?";
    $verifyStmt = $conn->prepare($verifyQuery);
    $verifyStmt->bind_param("i", $userID);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();

    if ($verifyResult->num_rows === 1) {
        $row = $verifyResult->fetch_assoc();
        $hashedPassword = $row['password'];

        // Verify the current password
        if (password_verify($currentPassword, $hashedPassword)) {
            // Update the password with the new one
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET password = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("si", $newHashedPassword, $userID);
            $updateStmt->execute();

            // Redirect to the profile page with a success message
            header("Location: profile.php?password_changed=true");
            exit;
        } else {
            // Display an error message if the current password is incorrect
            $errorMessage = "Current password is incorrect.";
        }
    } else {
        // Handle error if user data cannot be retrieved
        // Redirect or display an error message
        header("Location: error.php");
        exit;
    }

    $verifyStmt->close();
}

// Profile details update handling
if (isset($_POST['update_details'])) {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newName = $_POST['name'];

    // Update the user's details in the database
    $updateQuery = "UPDATE users SET username = ?, email = ?, name = ? WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssi", $newUsername, $newEmail, $newName, $userID);
    $updateStmt->execute();

    // Redirect to the profile page with a success message
    header("Location: profile.php?details_updated=true");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <!-- CSS only -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
<!-- Navigation -->
<?php include "navbar.php"; ?>
    <div class="container">
        <h1>User Profile</h1>

        <?php if (isset($_GET['password_changed']) && $_GET['password_changed'] === 'true') : ?>
            <div class="alert alert-success" role="alert">
                Password changed successfully!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['details_updated']) && $_GET['details_updated'] === 'true') : ?>
            <div class="alert alert-success" role="alert">
                Profile details updated successfully!
            </div>
        <?php endif; ?>

        <h3>Welcome, <?php echo $name; ?></h3>

        <h4>Profile Details</h4>
        <form method="POST" action="profile.php">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required maxlength="5">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <input type="text" class="form-control" id="role" name="role" value="<?php echo $role; ?>" readonly>
            </div>
            <?php if (isset($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>
            
            <button type="submit" class="btn btn-primary" name="update_details">Update Details</button>
        </form>

        <h4>Change Password</h4>
        <form method="POST" action="profile.php">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <?php if (isset($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary" name="change_password">Change Password</button>
        </form>
    </div>
</body>
</html>

