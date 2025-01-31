<?php
require __DIR__ . '/vendor/autoload.php'; // Firebase PHP SDK

use Kreait\Firebase\Factory;

// Initialize Firebase
$firebase = (new Factory)
    ->withServiceAccount(__DIR__ . '/wasticpod-firebase-adminsdk-fbsvc-19f60f388c.json')
    ->withDatabaseUri('https://wasticpod-default-rtdb.asia-southeast1.firebasedatabase.app/'); // Replace with your database URL

$database = $firebase->createDatabase();

// Handle incoming POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['weight'])) {
    $weight = $_POST['weight'];
    $timestamp = date("Y-m-d H:i:s");

    // Push data to Firebase
    $newData = $database->getReference('weights')->push([
        'weight' => $weight,
        'timestamp' => $timestamp
    ]);

    echo "Weight record saved successfully!";
} else {
    echo "Invalid request.";
}
?>
