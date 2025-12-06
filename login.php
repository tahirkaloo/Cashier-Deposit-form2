<?php
// It is recommended to have 'display_errors' off in a production environment,
// but 'display_startup_errors' can be left on for debugging purposes.
if (PHP_SAPI !== 'cli' && !in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'), true)) {
    ini_set('display_errors', 0);
}
ini_set('display_startup_errors', 0);

session_start();
require_once 'db_connect.php';
// Include the logger.php file
require_once 'log.php';

// Try to connect using mysqli for this specific file, handling error gracefully
$conn = false;
if (!DEMO_MODE) {
    try {
        $conn = @mysqli_connect($db_host, $db_user, $db_password, $db_name);
    } catch (Exception $e) {
        $conn = false;
    }
}

if (!$conn && !DEMO_MODE) {
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
} elseif (!DEMO_MODE) {
    error_log("Connected to MySQL successfully");
}

// Set the timezone
date_default_timezone_set('UTC');

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to the home page or any other desired page
    exit;
}

$error = false;
$errorMessage = '';

// Function to log in the user
function loginUser($user)
{
    // Set session variables
    $_SESSION['loggedin'] = true;
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    // Redirect to the home page or any other desired page
    header("Location: index.php");
    exit;
}

// Check if the login form is submitted
if (isset($_POST['login'])) {

    // DEMO MODE LOGIN
    if (DEMO_MODE) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (!empty($username) && !empty($password)) {
            // Mock Login Success
            $mockUser = [
                'user_id' => 999,
                'username' => $username,
                'name' => 'Demo User',
                'role' => ($username === 'admin') ? 'admin' : 'user'
            ];
            loginUser($mockUser);
        } else {
             $error = true;
             $errorMessage = "All fields are required.";
        }
    }
    // REAL DB LOGIN
    else if (!$conn) {
         $error = true;
         $errorMessage = "Database connection unavailable.";
    } else {
        // Get the form inputs
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Validate the form inputs
        if (empty($username) || empty($password)) {
            $error = true;
            $errorMessage = "All fields are required.";
        } else {
            // Check if the user exists in the database
            $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Call the logAction() function to log the action
            logAction($username.' ' . $_SESSION['user_id'] . 'logged in');


            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);

                // Check if the password is correct
                if (password_verify($password, $user['password'])) {
                    loginUser($user);
                } else {
                    $error = true;
                    $errorMessage = "Invalid username or password.";
                }
            } else {
                $error = true;
                logAction('Someone tried to log in, but failed');
                $errorMessage = "Invalid username or password.";
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Deposits Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php showDemoBanner(); ?>

    <div class="auth-wrapper">
        <div class="glass-panel auth-box animate-fade-up">
            <a href="index.php">
                <img src="images/logo-no-background.png" alt="Logo" class="auth-logo">
            </a>
            <h2 class="mb-4">Welcome Back</h2>
            <p class="text-muted mb-4">Please sign in to your account</p>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-custom" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-floating form-floating-custom mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="username" required maxlength="5">
                    <label for="username">Username</label>
                </div>

                <div class="form-floating form-floating-custom mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="current-password" required>
                    <label for="password">Password</label>
                </div>

                <button type="submit" name="login" class="btn btn-gradient btn-lg mb-4">
                    Sign In <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="reset-password.php" class="btn-link-custom small">Forgot Password?</a>
                <a href="register.php" class="btn-link-custom small">Create Account</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
