<?php
$host = 'address';
$username = 'name';
$password = 'pass';
$database = 'your_db';

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
