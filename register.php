<?php
session_start();
require_once 'db_connect.php';

// Set the timezone
date_default_timezone_set('UTC');

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to the home page or any other desired page
    exit;
}

$error = false;
$successMessage = '';
$errorMessage = '';

// Check if the registration form is submitted
if (isset($_POST['register'])) {
    // Get the form inputs
    if (isset($_POST['name'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
        $name = $_POST['name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // Validate the form inputs
        if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $error = true;
            $errorMessage = "All fields are required.";
        } elseif ($password != $confirmPassword) {
            $error = true;
            $errorMessage = "Passwords do not match.";
        } else {
            if (defined('DEMO_MODE') && DEMO_MODE) {
                 // Simulate successful registration in Demo Mode
                 $successMessage = "Demo Mode: Registration simulated. You can now login with any username/password.";
            } else {
                // Check if the username or the email already exists in the database
                $checkStmt = $pdo->prepare("SELECT 1 FROM users WHERE username = ? OR email = ?");
                $checkStmt->execute([$username, $email]);

                if ($checkStmt->rowCount() > 0) {
                    $error = true;
                    $errorMessage = "Username or email already exists.";
                } else {
                    // Hash the password using password_hash()
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Insert the user into the database
                    $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)");

                    if ($stmt->execute([$name, $username, $email, $hashedPassword])) {
                        $successMessage = "Registration successful. You can now login.";

                        // Optionally, you can redirect the user to the login page here
                    } else {
                        $errorMessage = "Something went wrong. Please try again later.";
                        error_log("Error executing prepared statement: " . json_encode($stmt->errorInfo()));
                    }
                }
            }
        }
    } else {
        $error = true;
        $errorMessage = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Deposits Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="auth-wrapper">
        <div class="glass-panel auth-box animate-fade-up">
            <a href="index.php">
                <img src="images/logo-no-background.png" alt="Logo" class="auth-logo">
            </a>
            <h2 class="mb-4">Create Account</h2>
            <p class="text-muted mb-4">Join our secure deposits platform</p>

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

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-floating form-floating-custom mb-3">
                    <input type="text" class="form-control" name="name" id="name" required placeholder="Full Name">
                    <label for="name">Full Name</label>
                </div>

                <div class="form-floating form-floating-custom mb-3">
                    <input type="text" class="form-control" name="username" id="username" required placeholder="Username" maxlength="5">
                    <label for="username">Username (Max 5 chars)</label>
                </div>

                <div class="form-floating form-floating-custom mb-3">
                    <input type="email" class="form-control" name="email" id="email" required placeholder="Email Address">
                    <label for="email">Email Address</label>
                </div>

                <div class="form-floating form-floating-custom mb-3">
                    <input type="password" class="form-control" name="password" id="password" autocomplete="new-password" required placeholder="Password">
                    <label for="password">Password</label>
                </div>

                <div class="form-floating form-floating-custom mb-4">
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" autocomplete="new-password" required placeholder="Confirm Password">
                    <label for="confirm_password">Confirm Password</label>
                </div>

                <button class="btn btn-gradient btn-lg mb-4" type="submit" name="register">
                    Register Now <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="mt-3">
                <span class="text-muted">Already have an account?</span>
                <a href="login.php" class="btn-link-custom ms-1">Sign In</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
