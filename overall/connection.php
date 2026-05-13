<?php
// Database configuration
$host     = "localhost";
$username = "root";      // Default Workbench/XAMPP user
$password = "";          // Default is empty, or check your Workbench password
$dbname   = "prototype";

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4 for better compatibility
mysqli_set_charset($conn, "utf8mb4");
?>