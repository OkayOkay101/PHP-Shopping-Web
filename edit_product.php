<?php
session_start();
include('db.php'); // Include the database connection file

// Check if the product ID is set
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Fetch the product details from the database
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // If product is not found, redirect to admin page
    if (!$product) {
        header("Location: admin.php");
        exit;
    }

    // Handle the update action
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $picture = $_POST['picture'];

        // Update the product details in the database
        $updateSql = "UPDATE products SET name = ?, price = ?, picture = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssdi", $name, $price, $picture, $productId);
        $stmt->execute();

        // Redirect back to the admin page after editing the product
        header("Location: admin.php");
        exit;
    }
} else {
    header("Location: admin.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <header class="site-header">
        <div class="container">
            <div class="site-title">My Shop - Admin</div>
            <nav class="site-nav">
                <a href="index.php">Shop</a>
                <a href="admin.php">Admin</a>
                <a href="checkout.php">Checkout</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1>Edit Product</h1>

        <form action="edit_product.php?id=<?php echo $product['id']; ?>" method="POST">
            <input type="hidden" name="action" value="edit">
            <div class="form-row">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-row">
                <label for="price">Price:</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="form-row">
                <label for="picture">Image Filename:</label>
                <input type="text" id="picture" name="picture" value="<?php echo htmlspecialchars($product['picture']); ?>" required>
            </div>
            <button class="btn" type="submit">Update Product</button>
        </form>

        <div style="margin-top:12px"><a class="link" href="admin.php">Back to Admin Panel</a></div>

        <footer class="footer">
            <div class="small">&copy; <?php echo date('Y'); ?> My Shop</div>
        </footer>
    </main>

</body>

</html>

<?php $conn->close(); // Close the connection 
?>