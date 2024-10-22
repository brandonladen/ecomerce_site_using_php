<?php
session_start();
if (isset($_SESSION['error_message'])) {
    echo '<p>' . $_SESSION['error_message'] . '</p>';
    unset($_SESSION['error_message']); // Clear the message after displaying it
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ShopWithUs</title>
    <link href="https://fonts.googleapis.com/css2?family=Arima:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    <style>
        body {
            font-family: 'Arima', Arial, sans-serif;
            color: #333;
            background-color: darkcyan; 
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
        }

        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .signup-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="form-container"> 
        <h2>Login</h2>
        <?php
        session_start(); // Start a session

        // Check for success message
        if (isset($_SESSION['success_message'])) {
            echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
            // Clear the message after displaying it
            unset($_SESSION['success_message']);
        } elseif($_SESSION['error_message']) {
            echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?> 
        <form action="auth.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" name="login" value="Login">
        </form>
        <div class="signup-link">
            <p>Don't have an account? <a href="signup.php">Signup</a></p>
        </div>
    </div>

</body>
</html>