<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Deposit Portal</title>
  <!-- Add any CSS or Bootstrap here -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="styles.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
      <a href="coinexchange.php">
        <div class="banner-overlay">
          <h2>Coin Exchange</h2>
          <i class="fas fa-user-shield" style="font-size: 32px;"></i>
          <i class="fas fa-arrow-right" style="font-size: 32px;"></i>
        </div>
      </a>
    </div>

    <div class="banner banner3">
      <a href="depositform.php">
        <div class="banner-overlay">
          <h2>Deposit Form</h2>
          <i class="fas fa-user" style="font-size: 32px;"></i>
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
      <a href="/admin/index.php">
        <div class="banner-overlay">
          <h2>Admin</h2>
          <i class="fas fa-user-shield" style="font-size: 32px;"></i>
          <i class="fas fa-arrow-right" style="font-size: 32px;"></i>
        </div>
      </a>
    </div>

  </div>
  <!-- Footer -->
<?php include "footer.php"; ?>

</body>
</html>

