<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard | Deposits Portal</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php include "navbar.php"; ?>

  <div class="container mt-5">
    <!-- Hero Section -->
    <div class="hero-section">
      <h1 class="hero-title">Welcome Back</h1>
      <p class="hero-subtitle">Streamline your financial operations with our advanced deposits management system. Select a module below to get started.</p>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
      <!-- Card 1: Cashier Deposit -->
      <a href="cashierdeposit.php" class="card-custom animate-fade-up" style="animation-delay: 0.1s;">
        <div class="card-image-wrapper">
          <div class="card-bg-image" style="background-image: url('images/cashregister.png');"></div>
        </div>
        <div class="card-content">
          <div class="card-icon-float">
            <i class="fas fa-cash-register"></i>
          </div>
          <div>
            <h3 class="card-title">Cashier Deposit</h3>
            <p class="text-muted small mt-2">Process new deposits securely.</p>
          </div>
          <div class="card-arrow">
            Go to Module <i class="fas fa-arrow-right ms-2"></i>
          </div>
        </div>
      </a>

      <!-- Card 2: History -->
      <a href="history.php" class="card-custom animate-fade-up" style="animation-delay: 0.2s;">
        <div class="card-image-wrapper">
          <div class="card-bg-image" style="background-image: url('images/history.png');"></div>
        </div>
        <div class="card-content">
          <div class="card-icon-float">
            <i class="fas fa-history"></i>
          </div>
          <div>
            <h3 class="card-title">History Logs</h3>
            <p class="text-muted small mt-2">View past transactions and logs.</p>
          </div>
          <div class="card-arrow">
            View History <i class="fas fa-arrow-right ms-2"></i>
          </div>
        </div>
      </a>

      <!-- Card 3: Coin Exchange -->
      <a href="coinexchange.php" class="card-custom animate-fade-up" style="animation-delay: 0.3s;">
        <div class="card-image-wrapper">
          <div class="card-bg-image" style="background-image: url('images/coinexchange.png');"></div>
        </div>
        <div class="card-content">
          <div class="card-icon-float">
            <i class="fas fa-coins"></i>
          </div>
          <div>
            <h3 class="card-title">Coin Exchange</h3>
            <p class="text-muted small mt-2">Manage coin exchange requests.</p>
          </div>
          <div class="card-arrow">
            Exchange Now <i class="fas fa-arrow-right ms-2"></i>
          </div>
        </div>
      </a>

      <!-- Card 4: Deposit Form -->
      <a href="depositform.php" class="card-custom animate-fade-up" style="animation-delay: 0.4s;">
        <div class="card-image-wrapper">
          <div class="card-bg-image" style="background-image: url('images/depositform.png');"></div>
        </div>
        <div class="card-content">
          <div class="card-icon-float">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
          <div>
            <h3 class="card-title">Deposit Forms</h3>
            <p class="text-muted small mt-2">Access and submit deposit forms.</p>
          </div>
          <div class="card-arrow">
            View Forms <i class="fas fa-arrow-right ms-2"></i>
          </div>
        </div>
      </a>

      <!-- Card 5: Supervisor -->
      <a href="supervisor.php" class="card-custom animate-fade-up" style="animation-delay: 0.5s;">
        <div class="card-image-wrapper">
          <div class="card-bg-image" style="background-image: url('images/supervisor.png');"></div>
        </div>
        <div class="card-content">
          <div class="card-icon-float">
            <i class="fas fa-user-tie"></i>
          </div>
          <div>
            <h3 class="card-title">Supervisor</h3>
            <p class="text-muted small mt-2">Supervisor controls and oversight.</p>
          </div>
          <div class="card-arrow">
            Access Panel <i class="fas fa-arrow-right ms-2"></i>
          </div>
        </div>
      </a>

      <!-- Card 6: Admin -->
      <a href="/admin/index.php" class="card-custom animate-fade-up" style="animation-delay: 0.6s;">
        <div class="card-image-wrapper">
          <div class="card-bg-image" style="background-image: url('images/admin1.png');"></div>
        </div>
        <div class="card-content">
          <div class="card-icon-float">
            <i class="fas fa-shield-alt"></i>
          </div>
          <div>
            <h3 class="card-title">Admin Console</h3>
            <p class="text-muted small mt-2">System settings and user management.</p>
          </div>
          <div class="card-arrow">
            Enter Console <i class="fas fa-arrow-right ms-2"></i>
          </div>
        </div>
      </a>
    </div>
  </div>

  <!-- Footer -->
  <?php include "footer.php"; ?>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
