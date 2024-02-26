<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $submission_id = $_GET['id'];

        // Delete the submission from the database
        $sql = "DELETE FROM cashierdeposit WHERE id = '$submission_id'";
        if (mysqli_query($conn, $sql)) {
            header("Location: supervisor.php");
            exit;
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid submission ID.";
    }
}

mysqli_close($conn);