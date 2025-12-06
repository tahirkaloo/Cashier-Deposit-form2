<?php
session_start();
require_once 'db_connect.php';

$success = false;
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate form data
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all the fields.';
        if(isset($_POST['ajax'])) { echo json_encode(["success" => false, "error" => $error]); exit; }
    } else {
        if (defined('DEMO_MODE') && DEMO_MODE) {
             // Mock Submission
             sleep(1);
             $success = true;
             if(isset($_POST['ajax'])) { echo json_encode(["success" => true]); exit; }
        } else {
            // Real Database Logic
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
            if (!$conn) {
                $error = "Failed to connect to MySQL: " . mysqli_connect_error();
                if(isset($_POST['ajax'])) { echo json_encode(["success" => false, "error" => $error]); exit; }
            } else {
                // Insert form data into the contactresponses table
                $sql = "INSERT INTO contactresponses (name, email, message) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);

                if (mysqli_stmt_execute($stmt)) {
                    $success = true;
                    if(isset($_POST['ajax'])) { echo json_encode(["success" => true]); exit; }
                } else {
                    $error = mysqli_error($conn);
                    if(isset($_POST['ajax'])) { echo json_encode(["success" => false, "error" => $error]); exit; }
                }
                mysqli_close($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us | Deposits Portal</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="styles.css">
</head>

<body>
  <?php include_once "navbar.php"; ?>

  <!-- Demo Mode Warning -->
  <?php if (defined('DEMO_MODE') && DEMO_MODE): ?>
    <div class="alert alert-warning text-center fw-bold" role="alert">
        <i class="fa fa-exclamation-triangle"></i> Demo Mode: Database Connection Failed. Mocking form submission.
    </div>
  <?php endif; ?>

  <div class="container mt-5 mb-5">
    <div class="glass-panel p-5 animate-fade-up">
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                 <h1 class="mb-4 text-gradient"><i class="fas fa-envelope-open-text me-2"></i>Get in Touch</h1>
                 <p class="lead text-muted mb-5">Have a question or need assistance? Fill out the form below and our team will get back to you shortly.</p>

                 <form id="contact-form" method="post" action="contact.php">
                      <div class="form-floating form-floating-custom mb-3">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
                        <label for="name">Name</label>
                      </div>
                      <div class="form-floating form-floating-custom mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                        <label for="email">Email address</label>
                      </div>
                      <div class="form-floating form-floating-custom mb-4">
                        <textarea class="form-control" id="message" name="message" placeholder="Leave a message here" style="height: 150px" required></textarea>
                        <label for="message">Message</label>
                      </div>
                      <button type="submit" class="btn btn-gradient btn-lg w-100">Send Message <i class="fas fa-paper-plane ms-2"></i></button>
                </form>
            </div>

            <div class="col-md-6 ps-md-5 d-flex flex-column justify-content-center">
                 <div class="p-4 rounded-3 bg-white shadow-sm mb-4">
                     <div class="d-flex align-items-center mb-3">
                         <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                             <i class="fas fa-map-marker-alt fa-lg"></i>
                         </div>
                         <div>
                             <h5 class="mb-1">Visit Us</h5>
                             <p class="mb-0 text-muted">123 Financial District,<br>New York, NY 10001</p>
                         </div>
                     </div>
                 </div>

                 <div class="p-4 rounded-3 bg-white shadow-sm mb-4">
                     <div class="d-flex align-items-center mb-3">
                         <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                             <i class="fas fa-phone-alt fa-lg"></i>
                         </div>
                         <div>
                             <h5 class="mb-1">Call Us</h5>
                             <p class="mb-0 text-muted"><a href="tel:+15551234567" class="text-decoration-none text-muted">+1 (555) 123-4567</a></p>
                         </div>
                     </div>
                 </div>

                 <div class="p-4 rounded-3 bg-white shadow-sm">
                     <div class="d-flex align-items-center mb-3">
                         <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                             <i class="fas fa-envelope fa-lg"></i>
                         </div>
                         <div>
                             <h5 class="mb-1">Email Us</h5>
                             <p class="mb-0 text-muted"><a href="mailto:support@depositsportal.com" class="text-decoration-none text-muted">support@depositsportal.com</a></p>
                         </div>
                     </div>
                 </div>
            </div>
        </div>
    </div>
  </div>

  <?php include_once 'footer.php'; ?>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#contact-form').submit(function(e) {
        e.preventDefault();

        var name = $('#name').val();
        var email = $('#email').val();
        var message = $('#message').val();

        if (name === '' || email === '' || message === '') {
          alert('Please fill in all the fields.');
          return;
        }

        // Simple visual feedback
        var btn = $(this).find('button[type="submit"]');
        var originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Sending...').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: 'contact.php',
            data: {
              name: name,
              email: email,
              message: message,
              ajax: 1
            },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                alert('Form submitted successfully! Thank you for contacting us.');
                $('#contact-form')[0].reset();
              } else {
                alert('Error: ' + (response.error || 'An error occurred.'));
              }
            },
            error: function(xhr, status, error) {
              alert('An error occurred while submitting the form.');
              console.error(error);
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
          });
      });
    });
  </script>
</body>
</html>
