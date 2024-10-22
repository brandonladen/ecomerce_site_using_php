<?php
error_reporting(E_ALL); // Report all types of errors
ini_set('display_errors', 1); // Display errors on the page
session_start();

// Check if the user is authenticated as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error_message'] = 'You are not allowed to access admin page.';
    header('Location: ../authentication/login.php');
    exit();
}

// Database connection
require '../configs/db.php';

// Handle Create, Update, Delete operations
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        // Add a new product
        $productName = trim($_POST['product_name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
         // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = '../assets/images/';
            $uploadFile = $uploadDir . basename($_FILES['image']['name']);
            $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

            // Check if the file is an image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check === false) {
                $message = 'File is not an image.';
            } elseif ($_FILES['image']['size'] > 500000) { // Limit file size to 500 KB
                $message = 'Sorry, your file is too large.';
            } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $message = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
            } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                // File uploaded successfully, save product info to the database
                $sql = "INSERT INTO Products (ProductName, Description, Price, ImagePath) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssds', $productName, $description, $price, $uploadFile);
                if ($stmt->execute()) {
                    $message = 'Product added successfully.';
                } else {
                    $message = 'Error adding product: ' . $stmt->error; // Include error detail
                }
            } else {
                $message = 'Sorry, there was an error uploading your file.';
            }
        } else {
            $message = 'Error: ' . $_FILES['image']['error'];
        }
    } elseif (isset($_POST['update_product'])) {
        // Update existing product
        $productId = intval($_POST['product_id']);
        $productName = trim($_POST['product_name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        
        // Check if an image was uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Image was uploaded, process the file
            $imagePath = '../assets/images/' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        } else {
            // No image uploaded, use default image path
            $imagePath = '../assets/images/default-product.jpg';
        }
    
        // Prepare SQL query for updating the product
        $stmt = $conn->prepare("UPDATE Products SET ProductName = ?, Description = ?, Price = ?, ImagePath = ? WHERE ProductID = ?");
        $stmt->bind_param("ssdsi", $productName, $description, $price, $imagePath, $productId);
    
        // Execute the query and check for success
        if ($stmt->execute()) {
            $message = "Product updated successfully!";
        } else {
            $message = "Error updating product: " . $stmt->error;
        }
    } elseif (isset($_POST['delete_product'])) {
        // Delete a product
        $productId = intval($_POST['product_id']);
        $stmt = $conn->prepare("DELETE FROM Products WHERE ProductID = ?");
        $stmt->bind_param("i", $productId);
        if ($stmt->execute()) {
            $message = "Product deleted successfully!";
        } else {
            $message = "Error deleting product: " . $stmt->error;
        }
    }
}

// Fetch all products
$products = [];
$stmt = $conn->prepare("SELECT * FROM Products");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Product Management</title>
    <link rel="stylesheet" href="../assets/css/admin.css" type="text/css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <a href="../authentication/logout.php">Logout</a>
    </header>

    <main>
        <h2>Product Management</h2>
        <div><?php echo $message; ?></div>

        <form action="admin.php" method="POST" enctype="multipart/form-data">
            <h3>Add Product</h3>
            <input type="text" name="product_name" placeholder="Product Name" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" name="price" placeholder="Price" step="0.01" required>
            <input type="file" name="image" accept="image/*" required><br><br>
            <button type="submit" name="add_product">Add Product</button>
        </form>

        <h3>Existing Products</h3>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th> <!-- Updated header to reflect that this is an image -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                        <td><?php echo htmlspecialchars($product['Description']); ?></td>
                        <td><?php echo htmlspecialchars($product['Price']); ?></td>
                        <td>
                            <img src="<?php echo htmlspecialchars($product['ImagePath']); ?>" alt="Product Image" style="width: 100px; height: auto;">
                        </td>
                        <td>
                            <form action="admin.php" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">
                                <button type="submit" name="delete_product">Delete</button>
                            </form>
                            <button onclick="populateUpdateForm(<?php echo htmlspecialchars(json_encode($product)); ?>)">Update</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>



        <!-- Update Product Modal -->
        <div id="updateModal">
            <h3>Update Product</h3>
            <form id="updateForm" action="admin.php" method="POST">
                <input type="hidden" name="product_id" id="update_product_id">
                <input type="text" name="product_name" id="update_product_name" required>
                <textarea name="description" id="update_description" required></textarea>
                <input type="number" name="price" id="update_price" step="0.01" required>
                <input type="text" name="image_url" id="update_image_url" required>
                <button type="submit" name="update_product">Update Product</button>
            </form>
            <button onclick="document.getElementById('updateModal').style.display='none'">Close</button>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 ShopWithUs. All Rights Reserved.</p>
    </footer>

    <script>
        function populateUpdateForm(product) {
            document.getElementById('update_product_id').value = product.ProductID;
            document.getElementById('update_product_name').value = product.ProductName;
            document.getElementById('update_description').value = product.Description;
            document.getElementById('update_price').value = product.Price;
            document.getElementById('update_image_url').value = product.ImageURL;
            document.getElementById('updateModal').style.display = 'block';
        }
    </script>
</body>
</html>