<?php
ob_start();
session_status() === PHP_SESSION_ACTIVE ?: session_start();

// Define your database server details
$servername = "localhost"; // Or your database server's address
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password
$dbname = "HospitalDB";   //DB Name

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Set the PDO error mode to exception to handle errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //echo "Database connection successful!<br>";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "<br>";
    exit(); // Stop script if connection fails
}

// Only generate token if it doesn't exist
if (!isset($_SESSION['_token'])) {
    $_SESSION['_token'] = bin2hex(openssl_random_pseudo_bytes(16));
}

// Only validate POST requests that aren't file uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES)) {
    if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['_token']) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}
?>