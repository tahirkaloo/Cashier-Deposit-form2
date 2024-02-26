<?php
session_start();

// Unset all session variables
session_unset();

// Clear the session cookie
setcookie(session_name(), '', time()-3600);

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit;


