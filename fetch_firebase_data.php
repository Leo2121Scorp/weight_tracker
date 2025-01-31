<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

// Initialize Firebase
$firebase = (new Factory)
    ->withServiceAccount(__DIR__ . '/wasticpod-firebase-adminsdk-fbsvc-19f60f388c.json')
    ->withDatabaseUri('https://wasticpod-default-rtdb.asia-southeast1.firebasedatabase.app/');

$database = $firebase->createDatabase();

// Fetch data from Firebase
$data = $database->getReference('weights')->getValue();
echo "<pre>";
print_r($data);
echo "</pre>";
?>
