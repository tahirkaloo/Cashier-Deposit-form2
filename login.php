<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
require_once 'db_connect.php';
// Include the logger.php file
require_once 'log.php';

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
} else {
    error_log("Connected to MySQL successfully");
}

// Set the timezone
date_default_timezone_set('UTC');

// Do reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
        logAction($username . ' ' . $_SESSION['user_id'] . 'logged in');


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
?>

<!DOCTYPE html>
<html>
<head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        /* Additional CSS styles for login page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        .navbar {
            background-color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            color: #fff;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 10px;
        }

        .navbar a:first-child {
            margin-left: 0;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #fff;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo img {
            width: 100px;
            height: auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #f5f5f5;
        }

        .form-group input:focus {
            outline: none;
            background-color: #e1e1e1;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        .error-message {
            color: red;
            margin-top: 5px;
        }

        .login-button {
            display: block;
            width: 100%;
            padding: 12px 0;
            margin-top: 20px;
            background-color: #6c63ff;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #524bc7;
            cursor: pointer;
            box-shadow: 0 0 20px #6c63ff;
        }

        .login-button:not(:hover) {
            background-color: #6c63ff; /* Change the background color when not hovering */
        }

        .resetpassword-button {
            text-align: center;
            margin-top: 20px;
        }

        .resetpassword-button a {
            color: #6c63ff;
            text-decoration: none;
        }

        .resetpassword-button a:hover {
            text-decoration: underline;
            text-shadow: 0 0 20px #6c63ff;
        }

        .register-button {
            text-align: center;
            margin-top: 20px;
        }

        .register-button a {
            color: #6c63ff;
            text-decoration: none;
        }

        .register-button a:hover {
            text-decoration: underline;
            text-shadow: 0 0 20px #6c63ff;
        }


    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
    </div>
    <div class="container">
        <div class="logo">
            <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/Logo+files/logo-black.png" alt="Logo">
        </div>
        <h2 style="text-align: center; color: #6c63ff;">Login</h2>
        <?php
        if ($error) {
            echo '<div class="error-message">' . $errorMessage . '</div>';
        }
        ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" autocomplete="username" required maxlength="5">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
            </div>
            <div class="form-group">
                <input type="submit" name="login" value="Login" class="login-button">
            </div>
        </form>
        <div class="resetpassword-button">
            <a href="reset-password.php">Forgot Password? Reset it here</a>
        </div>
        <div class="register-button">
            <a href="register.php">Don't have an account? Register here</a>
        </div>
    </div>
</body>
</html>
