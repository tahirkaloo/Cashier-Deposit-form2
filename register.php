<?php
session_start();
require_once 'db_connect.php';

// Set the timezone
date_default_timezone_set('UTC');

// Do reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.html"); // Redirect to the home page or any other desired page
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
            // Check if the user already exists in the database
            $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
            $checkStmt->execute([$username]);

            if ($checkStmt->rowCount() > 0) {
                $error = true;
                $errorMessage = "Username already exists. Please choose a different one.";
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
    } else {
        $error = true;
        $errorMessage = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        /* Additional CSS styles for registration page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #6c63ff;
            box-shadow: 0 0 10px rgba(108, 99, 255, 0.2);
        }

        .error-message {
            color: red;
            margin-top: 5px;
        }

        .success-message {
            color: green;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Login Navbar -->
    <div class="navbar">
        <div>
            <a href="index.html">Home</a>
        </div>
        <div>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        </div>
    </div>

    <div class="container">
        <h2>Registration</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required placeholder="Enter your name">
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required placeholder="Enter your windows username" maxlength="5">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required placeholder="Enter your email address">
                
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" autocomplete="password" required placeholder="Enter your password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" autocomplete="confirm_password" required placeholder="Confirm your password">
            </div>
            <div class="form-group">
                <button type="submit" name="register">Register</button>
            </div>
            <div class="form-group">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>

        </form>
    </div>

</body>
</html>
