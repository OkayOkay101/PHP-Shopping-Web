<?php
session_start();
include('db.php'); // Include the database connection file

// Check if the user is an admin (you can improve this with a more secure login system)
$isAdmin = true;  // You can add authentication checks here (e.g., checking session variables)

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Handle the delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Delete the product from the database
    $deleteSql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();

    // Redirect back to the admin page after deletion
    header("Location: admin.php");
    exit;
}

// Handle the add product action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $picture = $_POST['picture'];

    // Debugging: Check the captured data
    echo "<strong>Form Data:</strong><br>";
    echo "Name: " . htmlspecialchars($name) . "<br>";
    echo "Price: " . htmlspecialchars($price) . "<br>";
    echo "Picture Filename: " . htmlspecialchars($picture) . "<br>"; // Debugging output to see the picture filename

    // Check if picture is empty
    if (empty($picture)) {
        echo "Error: Picture filename is empty.<br>";
    }

    // Insert the new product into the database
    $insertSql = "INSERT INTO products (name, price, picture) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("ssd", $name, $price, $picture); // Bind string, decimal, string
    $stmt->execute();

    // Check if the insert was successful
    if ($stmt->affected_rows > 0) {
        echo "Product added successfully!<br>";
    } else {
        echo "Failed to add product.<br>";
    }

    // Redirect back to the admin page after adding the product
    header("Location: admin.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Products</title>
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
        <h1>Admin Panel - Manage Products</h1>

        <?php if ($isAdmin): ?>

            <section class="card" style="margin-bottom:18px;">
                <h2>Add New Product</h2>
                <form action="admin.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-row">
                        <label for="name">Product Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-row">
                        <label for="price">Price:</label>
                        <input type="number" step="0.01" id="price" name="price" required>
                    </div>
                    <div class="form-row">
                        <label for="picture">Image Filename:</label>
                        <input type="text" id="picture" name="picture" required>
                    </div>
                    <button class="btn" type="submit">Add Product</button>
                </form>
            </section>

            <section>
                <h2>Existing Products</h2>
                <ul class="admin-list">
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <li>
                            <div style="display:flex;gap:12px;align-items:center">
                                <img src="images/<?php echo $product['picture']; ?>" alt="Product Image" width="80" style="border-radius:8px">
                                <div>
                                    <div style="font-weight:700"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="small">$<?php echo number_format($product['price'], 2); ?></div>
                                </div>
                                <div style="margin-left:auto">
                                    <a class="link" href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a> |
                                    <a class="link" href="admin.php?action=delete&id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')"> Delete</a>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>

        <?php endif; ?>

        <div style="margin-top:18px"><a class="link" href="index.php">Go Back to Homepage</a></div>

        <footer class="footer">
            <div class="small">&copy; <?php echo date('Y'); ?> My Shop</div>
        </footer>
    </main>

</body>

</html>

<?php $conn->close(); // Close the connection 
?>