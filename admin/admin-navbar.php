<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <style>
        /* CSS styles for the admin navbar */
        .admin-navbar {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            color: #fff;
        }

        .admin-navbar .logo img {
            height: 50px;
        }

        .admin-navbar ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .admin-navbar ul li {
            margin-left: 10px;
        }

        .admin-navbar ul li a {
            color: #fff;
            text-decoration: none;
        }

        .admin-navbar ul li a:hover {
            color: #ddd;
        }
    </style>
</head>
<body>
    <div class="admin-navbar">
        <div class="logo">
            <a href="admin_dashboard.php">
                <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/Logo+files/logo-white.png" alt="Logo">
            </a>
        </div>
        <div class="menu">
            <ul>
                <li><a href="index.php" class="fa fa-home"> Dashboard</a></li>
                <li><a href="mileage_responses.php">Local Mileage Responses</a></li>
                <li><a href="contact_responses.php">Contact Form Responses</a></li>
                <li><a href="manage-users.php" class="fa fa-users"> Manage Users</a></li>
		        <li><a href="../index.html" class="fa fa-home"> Go back to Website</a></li>
		        <li><a href="../logout.php" class="fa fa-sign-out"> Logout</a></li>
            </ul>
        </div>
    </div>
</body>
</html>

