<?php
require_once 'log.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Call the logAction() function to log the action when a user opens up current page
$queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
logAction('visited ' . $_SERVER['REQUEST_URI'] . $queryString);
?>

<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="images/logo-no-background.png" alt="Logo" width="45" height="45" class="d-inline-block align-text-top me-2" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
      Deposits Portal
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="fas fa-bars" style="color: var(--secondary-color);"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="fas fa-home me-1"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="cashierdeposit.php">
            <i class="fas fa-cash-register me-1"></i> Deposit
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="history.php">
            <i class="fas fa-history me-1"></i> History
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="coinexchange.php">
            <i class="fas fa-coins me-1"></i> Exchange
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
             More
          </a>
          <ul class="dropdown-menu border-0 shadow-lg" aria-labelledby="moreDropdown">
            <li><a class="dropdown-item" href="supervisor.php"><i class="fas fa-user-secret me-2"></i> Supervisor</a></li>
            <li><a class="dropdown-item" href="depositform.php"><i class="fas fa-file-invoice-dollar me-2"></i> Forms</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="about.php"><i class="fas fa-info-circle me-2"></i> About</a></li>
            <li><a class="dropdown-item" href="contact.php"><i class="fas fa-envelope me-2"></i> Contact</a></li>
          </ul>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
        <?php if ($isLoggedIn) { ?>
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] == "admin") { ?>
            <li class="nav-item">
              <a class="nav-link" href="admin">
                <span class="badge bg-danger rounded-pill"><i class="fas fa-shield-alt"></i> Admin</span>
              </a>
            </li>
          <?php } ?>
          <li class="nav-item dropdown">
             <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px;">
                    <?php echo substr($_SESSION['username'] ?? 'U', 0, 1); ?>
                </div>
                <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
             </a>
             <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
             </ul>
          </li>
        <?php } else { ?>
          <li class="nav-item">
            <a class="btn btn-outline-primary rounded-pill px-4 me-2" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-primary rounded-pill px-4" href="register.php">Register</a>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>
