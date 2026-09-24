<?php
// session_start();
include_once("includes/frontend.init.php");
include_once("includes/function.registration.php");

global $cfg, $mycms;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['latitude']) && !empty($_POST['longitude'])) {

    
    // if(isset($mycms->getSession('WEATHER_DATA'))){
    //     $mycms->removeSession('WEATHER_DATA');
    // }
    // Get latitude and longitude from user input
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);
    $place_name = htmlspecialchars($_POST['place_name']);

    // Weather API configuration
    $apiEndpoint = 'https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/' . urlencode("{$latitude},{$longitude}");
    $apiKey = 'TQZL9Q98A84EKW4YT8M73XKWH';
    $unitGroup = 'metric';

    // Set date range for the next 3 days
    // $startDate = date('Y-m-d');
    // $endDate = date('Y-m-d', strtotime('+3 days'));
    $startDate = date('Y-m-d',strtotime($cfg['CONF_START_DATE'])); // 5th feb 2025
    $endDate = date('Y-m-d', strtotime($cfg['CONF_END_DATE'])); // 8th feb 2025
    

    // Build the URL with parameters
    $url = "{$apiEndpoint}/{$startDate}/{$endDate}?unitGroup={$unitGroup}&key={$apiKey}&contentType=json";

    // Initialize cURL session
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session and get the response
    $response = curl_exec($ch);
    if ($response === FALSE) {
        die('Error occurred while fetching data from the API');
    }

    // Close cURL session and decode the JSON response
    curl_close($ch);
    $data = json_decode($response, true);
    $data['place_name'] = $place_name;
  
    $_SESSION['WEATHER_DATA']=$data;
    // echo '<pre>'; print_r($data);die;
    
    // $mycms->setSession('WEATHER_DATA',$data);
    // Display weather data
    // echo "<h2>Weather Forecast for Latitude: {$latitude}, Longitude: {$longitude}</h2>";
    // if (isset($data['days'])) {
    //     foreach ($data['days'] as $day) {
    //         echo "Date: " . $day['datetime'] . "<br>";
    //         echo "Temperature: " . $day['temp'] . "°C<br>";
    //         echo "Conditions: " . $day['conditions'] . "<br><br>";
    //     }
    // } else {
    //     echo "No weather data available for this location.";
    // }
    
}
$mycms->redirect('profile.php');
?>