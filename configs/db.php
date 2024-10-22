<?php
// Database configuration
$host = '127.0.0.1';
$username = 'root';
$password = 'root';
$database = 'ecommerce_db';

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set the charset to avoid issues with special characters
$conn->set_charset("utf8mb4");

// Use the connection for your queries here
// Example query
/*
$sql = "SELECT * FROM Products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Product Name: " . $row['ProductName'] . "<br>";
    }
} else {
    echo "0 results";
}
*/

// Close the connection when done
// $conn->close();
?>
