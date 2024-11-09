<?php
$apiKey = 'cb471ed70d10bc1bfb870874eb38a185';
$weather = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $location = urlencode($_POST['location']);
    $url = "http://api.weatherstack.com/current?access_key={$apiKey}&query={$location}";

    // Fetch the weather data from the API
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Check if the response was successful
    if ($data && isset($data['current'])) {
        $day = date("l"); // Day of the week
        $date = date("F j, Y"); // Full date
        $precipitation = $data['current']['precip'];
        $humidity = $data['current']['humidity'];
        $windSpeed = $data['current']['wind_speed'];
        $weatherIcon = $data['current']['weather_icons'][0];
        $weatherDesc = $data['current']['weather_descriptions'][0];
        $locationName = $data['location']['name'];

        $weather = "<div class='weather-details'>
                        <img src='{$weatherIcon}' alt='{$weatherDesc}' class='weather-icon'><br>
                        <strong>Location:</strong> {$locationName}<br>
                        <strong>Day:</strong> {$day}, {$date}<br>
                        <strong>Weather:</strong> {$weatherDesc}<br>
                        <strong>Temperature:</strong> {$data['current']['temperature']} °C<br>
                        <strong>Precipitation:</strong> {$precipitation} mm<br>
                        <strong>Humidity:</strong> {$humidity}%<br>
                        <strong>Wind Speed:</strong> {$windSpeed} km/h<br>
                    </div>";
    } else {
        $weather = "Weather data not available.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link href="https://fonts.googleapis.com/css2?family=Arima:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/weather.css" type="text/css">
</head>
<body>
    <div class="container">
        <h1>Weather App</h1>
        <form method="POST" action="">
            <label for="location">Enter Location:</label>
            <input type="text" id="location" name="location" required>
            <button type="submit">Get Weather</button>
        </form>
        <a href="index.php" class="back-to-shopping-link">Back to shopping</a>
        <div class="weather-info">
            <?php if ($weather): ?>
                <h2>Weather Information</h2>
                <p><?php echo $weather; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
