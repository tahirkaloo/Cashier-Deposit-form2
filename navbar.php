<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require_once 'log.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Call the logAction() function to log the action when a user opens up current page
logAction('visited ' . $_SERVER['REQUEST_URI']);

?>

<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="#">
            <img src="images/Logo/logo-no-background.png" alt="Logo" height="50" width="50">
            Deposits Portal
        </a>

        <!-- Toggler button for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cashierdeposit.php">Cashier Deposit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="history.php">History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="coinexchange.php">Coin Exchange</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="supervisor.php">Supervisor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="depositform.php">Deposit Forms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.html">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
            </ul>

            <!-- Right-aligned items -->
            <ul class="navbar-nav">
                <?php if ($isLoggedIn) { ?>
                <!-- Profile and Logout links for logged-in users -->
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
                <?php } else { ?>
                <!-- Login and Register links for guests -->
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
                <?php } ?>

                <?php if ($isLoggedIn && $_SESSION['role'] === 'admin') { ?>
                <!-- Admin link for admin users -->
                <li class="nav-item">
                    <a class="nav-link" href="admin">Admin</a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
