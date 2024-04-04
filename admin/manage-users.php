<?php
session_start();
require_once '../db_connect.php';

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

//Check if the user is admin and show error if not
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../404.html");
    exit;
}


// Check if the database connection is successful
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
// Do reporting
if (!$conn) {
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
} else {
    error_log("Connected to MySQL successfully");
}



// Check if the user is an admin
// Replace the condition below with your own logic to determine if the user is an admin
$isAdmin = ($_SESSION['role'] === 'admin');

// Check if the form is submitted for changing the user's role
if (isset($_POST['action']) && $_POST['action'] === 'changeRole') {
    $userId = $_POST['userId'];
    $role = $_POST['role'];

    // Perform the necessary database operations to update the user's role
    // Replace the following code with your own logic
    $query = "UPDATE users SET role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $role, $userId);
    $stmt->execute();
}

// Check if the form is submitted for resetting the user's password
if (isset($_POST['action']) && $_POST['action'] === 'resetPassword') {
    $userId = $_POST['userId'];

    // Generate a new password
    $newPassword = generateRandomPassword(); // Implement your own logic to generate a new password

    // Perform the necessary database operations to update the user's password
    // Replace the following code with your own logic
    $query = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the new password
    $stmt->bind_param("si", $hashedPassword, $userId);
    $stmt->execute();
}

// Check if the form is submitted for deleting the user's account
if (isset($_POST['action']) && $_POST['action'] === 'deleteAccount') {
    $userId = $_POST['userId'];

    // Perform the necessary database operations to delete the user's account
    // Replace the following code with your own logic
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

// Get all users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Helper function to generate a random password
function generateRandomPassword()
{
    // Implement your own logic to generate a random password
    // This is just a simple example
    $length = 8;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
    $bytes = random_bytes($length); // Use random_bytes for cryptographically secure random numbers
    $password = '';
    foreach (str_split($bytes, 1) as $byte) {
        $password .= $characters[ord($byte) % strlen($characters)];
    }
    return $password;
}
}

// Helper function to retrieve the user's email
function getUserEmail($userId)
{
    // Implement your own logic to retrieve the user's email
    // Replace the following code with your own logic
    global $conn; // Added to access the global connection variable
    $query = "SELECT email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['email'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        /* Additional CSS styles for manage-users page */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            display: inline-block;
        }

        .btn-action {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .btn-action:hover {
            opacity: 0.8;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            padding: 5px;
        }

        .dropdown-option {
            cursor: pointer;
            padding: 5px;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>

<body>
    <?php include 'admin-navbar.php'; ?>
    <div class="container">
        <h1>Manage Users</h1>
        
        <div id="password" class="container text-center mt-2 mb-3 p-2 shadow rounded bg-white text-dark w-50"><strong>
        <?php
        // Display the new password
        if (isset($newPassword)) {
            $query = "SELECT * FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $name = $row['name'];
            $username = $row['username'];
            echo "New password for the user - ".$name." (".$username.") is: " . $newPassword;
        }
        ?>
        </strong>
        </div>


            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <div class="dropdown" id="dropdown-<?php echo $user['user_id']; ?>">
                                <button class="btn-action"><?php echo $user['role']; ?></button>
                                <div class="dropdown-content">
                                    <div class="dropdown-option" onclick="changeRole(<?php echo $user['user_id']; ?>, 'admin')">Admin</div>
                                    <div class="dropdown-option" onclick="changeRole(<?php echo $user['user_id']; ?>, 'supervisor')">Supervisor</div>
                                    <div class="dropdown-option" onclick="changeRole(<?php echo $user['user_id']; ?>, 'Accounting')">Accounting</div>
                                    <div class="dropdown-option" onclick="changeRole(<?php echo $user['user_id']; ?>, 'user')">User</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form method="post" onsubmit="return confirm('Reset Password: Are you sure?');">
                                <input type="hidden" name="userId" value="<?php echo $user['user_id']; ?>">
                                <input type="hidden" name="action" value="resetPassword">
                                <button class="btn-action" type="submit">Reset Password</button>
                            </form>
                            <form method="post" onsubmit="return confirm('Delete Account: Are you sure?');">
                                <input type="hidden" name="userId" value="<?php echo $user['user_id']; ?>">
                                <input type="hidden" name="action" value="deleteAccount">
                                <button class="btn-action" type="submit">Delete Account</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <script>
                // JavaScript function to handle changing the user's role
                function changeRole(userId, role) {
                    document.getElementById("dropdown-" + userId).innerHTML = role;
                    document.getElementById("dropdown-" + userId).style.display = "block";

                    // Create a hidden form and submit it to change the user's role
                    var form = document.createElement("form");
                    form.method = "post";
                    form.action = "";
                    var inputUserId = document.createElement("input");
                    inputUserId.type = "hidden";
                    inputUserId.name = "userId";
                    inputUserId.value = userId;
                    var inputRole = document.createElement("input");
                    inputRole.type = "hidden";
                    inputRole.name = "role";
                    inputRole.value = role;
                    var inputAction = document.createElement("input");
                    inputAction.type = "hidden";
                    inputAction.name = "action";
                    inputAction.value = "changeRole";
                    form.appendChild(inputUserId);
                    form.appendChild(inputRole);
                    form.appendChild(inputAction);
                    document.body.appendChild(form);
                    form.submit();
                }
            </script>
    </div>

<footer>
<?php include '../footer.php'; ?>
</footer>
</body>
</html>