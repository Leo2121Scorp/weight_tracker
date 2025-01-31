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

// Check if a specific date is selected, otherwise show all records
$selected_date = isset($_GET['date']) ? $_GET['date'] : '';

// Construct SQL query based on the selected date
if ($selected_date) {
  // Fetch records for the selected date
  $sql = "SELECT id, weight, timestamp FROM weight_data WHERE DATE(timestamp) = '$selected_date' ORDER BY timestamp DESC";
} else {
  // Fetch all records
  $sql = "SELECT id, weight, timestamp FROM weight_data ORDER BY timestamp DESC";
}

$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Weight Data Records</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      padding: 20px;
      background-color: #f8f9fa;
    }
    .table-container {
      margin-top: 20px;
    }
    h1, h3 {
      text-align: center;
    }
    .btn {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Weight Data Records</h1>

    <!-- Show date information if a specific date is selected -->
    <?php if ($selected_date): ?>
      <h3>Date: <?php echo date("F j, Y", strtotime($selected_date)); ?></h3>
    <?php endif; ?>

    <!-- Display Table Only if Records Exist -->
    <?php if ($result->num_rows > 0): ?>
      <div class="table-container">
        <table class="table table-striped table-bordered">
          <thead class="thead-dark">
            <tr>
              <th>ID</th>
              <th>Weight (kg)</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Display each record in a table row
            while ($row = $result->fetch_assoc()) {
              // Format the timestamp to the correct timezone (Asia/Manila)
              $dateTime = new DateTime($row["timestamp"], new DateTimeZone('Asia/Manila'));
              $formattedTimestamp = $dateTime->format('g:i A, d M Y');

              echo "<tr><td>" . $row["id"] . "</td><td>" . $row["weight"] . "</td><td>" . $formattedTimestamp . "</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info text-center">
        <?php if ($selected_date): ?>
          No weight records found for <?php echo date("F j, Y", strtotime($selected_date)); ?>.
        <?php else: ?>
          No weight records found in the database.
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- Navigation Button -->
    <div class="text-center">
      <a href="index.php" class="btn btn-primary">Back to Calendar</a>
    </div>
  </div>
</body>
</html>