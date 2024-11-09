<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'You must log in to add items to your cart.';
    header('Location: ../authentication/login.php');
    exit();
}

// Include the database connection file
include '../configs/db.php';

// Check if the product ID is set
if (isset($_POST['product_id'])) {
    $productID = intval($_POST['product_id']);
    $userID = $_SESSION['user_id']; // Assuming you store user ID in session

    // Prepare an SQL statement to insert the order
    $stmt = $conn->prepare("INSERT INTO Orders (UserID, ProductID, TotalAmount) VALUES (?, ?, (SELECT Price FROM Products WHERE ProductID = ?))");
    $stmt->bind_param("iii", $userID, $productID, $productID);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Product added to cart successfully!';
    } else {
        $_SESSION['error_message'] = 'Error adding product to cart: ' . $stmt->error;
    }

    $stmt->close();
} else {
    $_SESSION['error_message'] = 'Product ID is missing.';
}

// Redirect back to the product page
header('Location: index.php');
exit();
?>
