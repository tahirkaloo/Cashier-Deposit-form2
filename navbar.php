<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require_once 'log.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Call the logAction() function to log the action when a user opens up current page
logAction('visited ' . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING']);

?>

<!-- JavaScript Bundle with Popper -->
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
  <img src="image.php?image=logo-no-background.png" alt="Logo" height="100" width="100">
  <a class="navbar-brand" href="#">Deposits Portal</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
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
        <a class="nav-link" href="about.php">About</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="contact.php">Contact</a>
      </li>

      <?php if ($isLoggedIn) { ?>

            <?php if($_SESSION['role'] == "admin") { ?>
                <li class="nav-item">
                  <a class="nav-link" href="admin">Admin</a>
                </li>
              <?php } ?>

        <li class="nav-item">
            <a class="nav-link" href="profile.php">Profile</a>
          </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>
        <?php } else { ?>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="register.php">Register</a>
          </li>
      <?php } ?>
    </ul>
  </div>
</nav>
