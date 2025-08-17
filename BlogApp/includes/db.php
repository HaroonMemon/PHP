<?php
// Database credentials
$host = "localhost";      // Usually localhost
$user = "root";           // Your MySQL username
$password = "";           // Your MySQL password
$database = "blog_website"; // Database name we created

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>


