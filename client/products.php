<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'You must log in to access this page.';
    header('Location: ../authentication/login.php');
    exit();
}

// Include the database connection file
include '../configs/db.php';

// Fetch products from the database
$query = "SELECT ProductName, Description, Price, ImagePath FROM Products";
$result = $conn->query($query);

// Initialize an array to hold the fetched products
$products = [];

if ($result->num_rows > 0) {
    // Fetch all products
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    $message = "No products found!";
}

// Check for session messages
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;

// Clear messages after displaying them
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - ShopWithUs</title>
    <link href="https://fonts.googleapis.com/css2?family=Arima:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/index.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/product.css" type="text/css">   
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>LadenMart</h1>
            </div>
            <ul class="nav-links">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="../administration/admin.php">Admin Page</a></li>
            <?php endif; ?>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li>
                    <!-- Logout Button -->
                    <form action="../authentication/logout.php" method="POST" style="display: inline;">
                        <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
        <hr>
    </header>

    <h2 style="text-align: center; color: white;">Our Products</h2>

    <div class="container">
    <div class="container">
    <!-- Display messages -->
    <div class="message-container">
        <?php if ($successMessage): ?>
            <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
    </div>
        <!-- Display products dynamically -->
        <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="card">
                <img src="<?php echo htmlspecialchars($product['ImagePath']); ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                <h3><?php echo htmlspecialchars($product['ProductName']); ?></h3>
                <p>Ksh.<?php echo htmlspecialchars($product['Price']); ?></p>
                <p><?php echo htmlspecialchars($product['Description']); ?></p>
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">
                    <button type="submit">Add to Cart</button>
                </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: white;">No products available at the moment.</p>
        <?php endif; ?>

    </div>

    <footer>
        <p>&copy; 2024 ShopWithUs. All Rights Reserved.</p>
    </footer>
</body>
</html>
