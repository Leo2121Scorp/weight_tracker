<?php
// Database configuration
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

// Handle month and year navigation
$year = isset($_GET['year']) ? $_GET['year'] : date("Y");
$month = isset($_GET['month']) ? $_GET['month'] : date("m");

// Determine the first and last days of the month
$first_day_of_month = date("Y-m-01", strtotime("$year-$month-01"));
$days_in_month = date("t", strtotime($first_day_of_month));
$first_day_of_week = date("N", strtotime($first_day_of_month)); // 1 (for Monday) through 7 (for Sunday)

// Check if a date is selected
$selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// Fetch the sum of weights for each day in the current month
$weight_sql = "SELECT DATE(timestamp) as day, SUM(weight) as total_weight FROM weight_data WHERE MONTH(timestamp) = '$month' AND YEAR(timestamp) = '$year' GROUP BY DATE(timestamp)";
$weight_result = $conn->query($weight_sql);

// Store weights and categories in an array
$daily_weights = [];
if ($weight_result->num_rows > 0) {
  while ($weight_row = $weight_result->fetch_assoc()) {
    $day = $weight_row['day'];
    $total_weight = $weight_row['total_weight'];
    // Categorize the weight based on thresholds
    if ($total_weight >= 5) {
      $category = "high"; // Red
    } elseif ($total_weight >= 2) {
      $category = "mid"; // Orange
    } else {
      $category = "low"; // Yellow
    }
    $daily_weights[$day] = ['total_weight' => $total_weight, 'category' => $category];
  }
}

// Calculate the weight category for the entire month
$category_counts = ["low" => 0, "mid" => 0, "high" => 0];
foreach ($daily_weights as $day => $data) {
  $category_counts[$data['category']]++;
}

// Determine the predominant category for the month
$month_category = array_search(max($category_counts), $category_counts);

// Calculate previous and next months
$prev_month = date("m", strtotime("$year-$month-01 -1 month"));
$prev_year = date("Y", strtotime("$year-$month-01 -1 month"));
$next_month = date("m", strtotime("$year-$month-01 +1 month"));
$next_year = date("Y", strtotime("$year-$month-01 +1 month"));

// Check if the current displayed month is the same as the current month
$is_current_month = ($year == date("Y") && $month == date("m"));

$conn->close();
?>

<!-- Calendar HTML -->
<div class="calendar-navigation text-center mb-4">
  <!-- Previous Month Button -->
  <a href="calendar.php?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="btn btn-info">&laquo; Previous Month</a>
  
  <!-- Current Month and Year Label with Highlight Color -->
  <span class="h4 mx-3" style="background-color: 
    <?php
    // Set the background color of the month based on the predominant category
    if ($month_category == "high") {
      echo "#ff4c4c"; // Red
    } elseif ($month_category == "mid") {
      echo "#ffa500"; // Orange
    } else {
      echo "#ffff00"; // Yellow
    }
    ?>;">
    <?php echo date("F Y", strtotime("$year-$month-01")); ?>
  </span>

  <!-- Next Month Button - Hide if it is the current month -->
  <?php if (!$is_current_month): ?>
    <a href="calendar.php?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="btn btn-info">Next Month &raquo;</a>
  <?php endif; ?>
</div>

<div class="calendar">
  <!-- Day of the Week Headers -->
  <div class="calendar-header">Mon</div>
  <div class="calendar-header">Tue</div>
  <div class="calendar-header">Wed</div>
  <div class="calendar-header">Thu</div>
  <div class="calendar-header">Fri</div>
  <div class="calendar-header">Sat</div>
  <div class="calendar-header">Sun</div>

  <?php
  // Print empty days before the first day of the current month
  for ($i = 1; $i < $first_day_of_week; $i++) {
    echo '<div class="calendar-day empty"></div>';
  }

  // Print all days of the current month
  for ($day = 1; $day <= $days_in_month; $day++) {
    $date = date("$year-$month-") . str_pad($day, 2, "0", STR_PAD_LEFT);
    $class = $date == $selected_date ? "selected" : "";

    // Determine the color category for each day based on the weight
    $bg_color = "#fff"; // Default white background
    if (isset($daily_weights[$date])) {
      if ($daily_weights[$date]['category'] == "high") {
        $bg_color = "#ff4c4c"; // Red
      } elseif ($daily_weights[$date]['category'] == "mid") {
        $bg_color = "#ffa500"; // Orange
      } else {
        $bg_color = "#ffff00"; // Yellow
      }
    }
    echo "<div class='calendar-day $class' id='day-$date' style='background-color: $bg_color;' onclick=\"showSmallList('$date')\" ondblclick=\"goToWeightRecords('$date')\">$day</div>";
  }
  ?>
</div>

<!-- JavaScript to handle single and double click -->
<script>
  // Show a small list of records on single click
  function showSmallList(date) {
    // Make an AJAX call to fetch a small list of records for the clicked date
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
      document.getElementById("smallList").innerHTML = this.responseText;
    }
    xhttp.open("GET", `small_list.php?date=${date}`, true);
    xhttp.send();
  }

  // Navigate to the weight records page on double click
  function goToWeightRecords(date) {
    window.location.href = `weight_records.php?date=${date}`;
  }
</script>