<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wastedb";

// Set default timezone
date_default_timezone_set('Asia/Manila');

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
