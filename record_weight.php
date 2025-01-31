<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wastedb";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if the request is a POST request and 'weight' & 'timestamp' are set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['weight']) && isset($_POST['timestamp'])) {
  $weight = $_POST['weight']; // Retrieve weight data from POST request
  $timestamp = $_POST['timestamp']; // Retrieve timestamp data from POST request

  // Insert the weight and timestamp into the database
  $sql = "INSERT INTO weight_data (weight, timestamp) VALUES ('$weight', '$timestamp')";

  if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
} else {
  echo "Invalid request method or missing weight/timestamp data!";
}

$conn->close();
?>