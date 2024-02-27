<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Deposit Portal</title>
  <!-- CSS only -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div id="navbar"></div>
  <script>
    $(function(){
      $("#navbar").load("navbar.php");
    });
  </script>

  <!-- Main Content -->
  <h1>Welcome to Deposit Portal</h1>
 
  <div class="banner-container">
    <div class="banner banner1">
      <a href="cashierdeposit.php">
        <div class="banner-overlay">
          <h2>Cashier Deposit</h2>
          <i class="fas fa-cash-register" style="font-size: 32px;"></i>
          <i class="fas fa-arrow-right" style="font-size: 32px;"></i>
        </div>
      </a>
    </div>

    <div class="banner banner2">
      <a href="history.php">
        <div class="banner-overlay">
          <h2>History</h2>
          <i class="fas fa-dollar-sign" style="font-size: 32px;"></i>
          <i class="fas fa-arrow-right" style="font-size: 32px;"></i>
        </div>
      </a>
    </div>

    <div class="banner banner3">
      <a href="supervisor.php">
        <div class="banner-overlay">
          <h2>Supervisor</h2>
          <i class="fas fa-user-secret" style="font-size: 32px;"></i>
          <i class="fas fa-arrow-right" style="font-size: 32px;"></i>
        </div>
      </a>
    </div>

    <div class="banner banner3">
      <a href="admin.php">
        <div class="banner-overlay">
          <h2>Admin</h2>
          <i class="fas fa-user-shield" style="font-size: 32px;"></i>
          <i class="fas fa-arrow-right" style="font-size: 32px;"></i>
        </div>
      </a>
    </div>

  </div>
  <!-- Footer -->
  <footer class="bg-dark text-white mt-5">
    <div class="container py-3">
      <p>&copy; Deposit Portal(tahir). All Rights Reserved.</p>
    </div>
  </footer>

  <!-- Bootstrap JavaScript -->
  <script src="bootstrap/js/jquery.min.js"></script>
  <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>

