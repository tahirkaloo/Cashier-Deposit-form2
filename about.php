<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Deposits Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include_once "navbar.php"; ?>

    <div class="container mt-5 mb-5">
        <div class="glass-panel p-5 animate-fade-up">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <span class="badge bg-primary mb-3 px-3 py-2 rounded-pill">About Us</span>
                    <h1 class="display-4 fw-bold mb-4">Empowering Financial <span class="text-primary">Clarity</span></h1>
                    <p class="lead text-muted mb-4">
                        Welcome to Deposits Portal, your one-stop destination for managing all your deposit transactions efficiently and securely.
                    </p>
                    <p class="mb-4">
                        At Deposits Portal, we understand the importance of streamlined deposit management for businesses and organizations of all sizes. Whether you're handling cash, checks, credit/debit card transactions, or other forms of deposits, our platform is designed to simplify your processes and enhance your financial operations.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="contact.php" class="btn btn-gradient">Contact Us</a>
                        <a href="termsandconditions.php" class="btn btn-outline-primary rounded-pill px-4">Our Terms</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <!-- Abstract decoration -->
                        <div class="bg-primary position-absolute rounded-circle" style="width: 300px; height: 300px; opacity: 0.1; top: -50px; right: -50px; z-index: 0;"></div>
                        <img src="images/about-img.png" class="img-fluid rounded-3 shadow-lg position-relative" alt="About Us" style="z-index: 1;">
                        <!-- Check if image exists, otherwise fallback or handle nicely. The original code referenced image.php?image=about-us.png which seemed to be a wrapper.
                             I saw 'about-img.png' in the file list earlier. I'll use that. -->
                    </div>
                </div>
            </div>

            <div class="row mt-5 pt-5">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Why Choose Us?</h2>
                    <p class="text-muted">We provide features that matter.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 bg-white rounded-3 shadow-sm h-100 text-center card-hover-effect">
                        <div class="mb-3 text-primary">
                            <i class="fas fa-chart-line fa-3x"></i>
                        </div>
                        <h5>Effortless Tracking</h5>
                        <p class="text-muted small">Keep track of all your deposit transactions in one centralized platform with real-time monitoring.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 bg-white rounded-3 shadow-sm h-100 text-center card-hover-effect">
                        <div class="mb-3 text-success">
                            <i class="fas fa-shield-alt fa-3x"></i>
                        </div>
                        <h5>Secure & Encrypted</h5>
                        <p class="text-muted small">Rest assured that your financial data is safe with our advanced encryption and security standards.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 bg-white rounded-3 shadow-sm h-100 text-center card-hover-effect">
                        <div class="mb-3 text-info">
                            <i class="fas fa-sliders-h fa-3x"></i>
                        </div>
                        <h5>Customizable</h5>
                        <p class="text-muted small">Tailor deposit forms to suit your specific requirements with customizable fields and validation rules.</p>
                    </div>
                </div>
            </div>

            <div class="mt-5 p-4 bg-light rounded-3">
                 <h4 class="fw-bold mb-3">Our Mission</h4>
                 <p class="mb-0">
                     To provide innovative solutions that empower businesses to manage their deposit activities with ease and confidence. We strive to deliver a user-friendly platform that offers comprehensive features, robust security measures, and exceptional customer support.
                 </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once 'footer.php'; ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
