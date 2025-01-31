<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wastedb";

// Set the default timezone
date_default_timezone_set('Asia/Manila');

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get the selected date from the URL
$selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// Fetch up to 3 latest weight records for the selected date
$sql = "SELECT weight, timestamp FROM weight_data WHERE DATE(timestamp) = '$selected_date' ORDER BY timestamp DESC LIMIT 3";
$result = $conn->query($sql);

// Display the records in a list format
echo "<h4>Weight Records for " . date("F j, Y", strtotime($selected_date)) . "</h4>";
echo "<ul class='list-group'>";
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $time = date("g:i A", strtotime($row['timestamp']));
    echo "<li class='list-group-item'>Weight: " . $row['weight'] . " kg at " . $time . "</li>";
  }
} else {
  echo "<li class='list-group-item'>No records found for this date</li>";
}
echo "</ul>";

$conn->close();
?>