<?php
$apiKey = '';
$weather = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $location = urlencode($_POST['location']);
    $url = "http://api.weatherstack.com/current?access_key={$apiKey}&query={$location}";

    // Fetch the weather data from the API
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Check if the response was successful
    if ($data && isset($data['current'])) {
        $weather = "Location: " . $data['location']['name'] . "<br>" .
                   "Temperature: " . $data['current']['temperature'] . " °C<br>" .
                   "Weather: " . $data['current']['weather_descriptions'][0] . "<br>";
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