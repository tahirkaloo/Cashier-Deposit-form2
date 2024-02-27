<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
  <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/Logo+files/logo-no-background.png" alt="Logo" height="100">
  <a class="navbar-brand" href="#">Deposit Portal</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item <?php if(!$isLoggedIn) echo 'active'; ?>">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="cashierdeposit.php">Cashier Deposit</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="history.php">History</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="supervisor.php">Supervisor</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="about.html">About</a>
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

