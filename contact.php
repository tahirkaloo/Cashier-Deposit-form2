<?php
session_start();
require_once 'db_connect.php';

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
    exit; // Exit if connection fails
} else {
    error_log("Connected to MySQL successfully");
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Insert form data into the contactresponses table
    $sql = "INSERT INTO contactresponses (username, name, email, message) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION['username'], $name, $email, $message);

    if (mysqli_stmt_execute($stmt)) {
        // Form submission successful
        echo json_encode(["success" => true]);
        exit;
    } else {
        // Form submission failed
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us</title>

  <!-- CSS only -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">

</head>

<body>
  <div id="navbar"></div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(function(){
      $("#navbar").load("navbar.php");
    });

    $(document).ready(function() {
      $('#contact-form').submit(function(e) {
        e.preventDefault(); // Prevent form submission

        // Get form values
        var name = $('#name').val();
        var email = $('#email').val();
        var message = $('#message').val();

        // Perform form validation
        if (name === '' || email === '' || message === '') {
          alert('Please fill in all the fields.');
        } else {
          // AJAX request to handle form submission
          $.ajax({
            type: 'POST',
            url: 'contact.php', // Update the URL with the correct PHP file
            data: {
              name: name,
              email: email,
              message: message
            },
            dataType: 'json', // Expect JSON response
            success: function(response) {
              if (response.success) {
                alert('Form submitted successfully! Thank you for contacting us.');
                $('#contact-form')[0].reset(); // Reset the form after successful submission
              } else {
                alert('An error occurred while submitting the form. Please try again later.');
              }
            },
            error: function(xhr, status, error) {
              alert('An error occurred while submitting the form. Please try again later.');
              console.error(error);
            }
          });
        }
      });
    });
  </script>

  <!-- Page Content -->
  <div class="container my-5">
    <h1>Contact Us</h1>
    <hr>
    <div class="row">
      <div class="col-md-6">
        <h3>Get in Touch</h3>
        <form id="contact-form">
          <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
          </div>
          <div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
          </div>
          <div class="form-group">
            <label for="message">Message:</label>
            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
      <div class="col-md-6">
        <h3>Visit Us</h3>
        <p>We are located in Chenault-Weems building. Make sure to enter through the left door and follow the signs. Our customer service agents are dedicated to providing excellent customer service.</p>
        <p><strong>Address:</strong> 7507 Library Dr, Hanover, VA 230069</p>
        <p><strong>Phone:</strong> <a href="tel:804-365-6050">804-365-6050</a></p>
        <p><strong>Email:</strong> <a href="mailto:Treasurer@hanovercounty.gov">Treasurer@hanovercounty.gov</a></p>
      </div>
    </div>
  </div>

  <!-- Include Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
