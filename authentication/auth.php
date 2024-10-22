<?php
error_reporting(E_ALL); // Report all types of errors
ini_set('display_errors', 1); // Display errors on the page
session_start(); // Start a session for user management
require '../configs/db.php'; // Include the database connection

// Function to handle user registration
function registerUser($username, $email, $password) {
    global $conn; // Use the global connection variable

    // Check if the username or email already exists
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ? OR Email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return 'Username or email already exists.';
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO Users (Username, Password, Email, Role) VALUES (?, ?, ?, 'customer')");
    $stmt->bind_param("sss", $username, $hashedPassword, $email);
    $stmt->execute();

    return 'Registration successful!';
}

// Function to handle user login
function loginUser($username, $password) {
    global $conn; // Use the global connection variable

    // Fetch user from the database
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify the password
    if ($user && password_verify($password, $user['Password'])) {
        // Set session variables for logged-in user
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['role'] = $user['Role'];
        return 'Login successful!';
    } else {
        return 'Invalid username or password.';
    }
}

// Check if the request method is POST and handle the action accordingly
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        // Handle user registration
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $message = registerUser($username, $email, $password);

        // Redirect to login page with success message
        if ($message === 'Registration successful!') {
            // You can use a session variable to pass the message
            $_SESSION['success_message'] = $message;
            header('Location: login.php'); // Redirect to the login page
            exit(); // Stop executing the script
        }
    } elseif (isset($_POST['login'])) {
        // Handle user login
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $message = loginUser($username, $password);

        // Redirect to products page with success message
        if ($message === 'Login successful!') {
            $_SESSION['success_message'] = $message;
            header('Location: ../client/products.php'); //Redirect to products page
            exit();
        } else {
            $_SESSION['error_message'] = $message;
            header('Location: login.php'); // redirect to the same login page
        }
    }
}
?>
