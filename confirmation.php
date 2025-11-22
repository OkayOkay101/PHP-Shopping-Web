<?php
session_start();
include('db.php'); // Include the database connection file

// Check if cart exists in the session (via checkout)
if (!isset($_SESSION['checkout_cart']) || empty($_SESSION['checkout_cart'])) {
    // Redirect to checkout if no cart found
    header('Location: checkout.php');
    exit;
}

// Get cart items from session
$checkoutCart = $_SESSION['checkout_cart'];

// Fetch product details from the database
$productIds = array_keys($checkoutCart);
if (!empty($productIds)) {
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($productIds)), ...$productIds);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $products = [];
}

// Total amount
$totalAmount = 0;
foreach ($products as $product) {
    $quantity = $checkoutCart[$product['id']];
    $totalAmount += $product['price'] * $quantity;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <header class="site-header">
        <div class="container">
            <div class="site-title">My Shop</div>
            <nav class="site-nav">
                <a href="index.php">Shop</a>
                <a href="admin.php">Admin</a>
                <a href="checkout.php">Checkout</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1>Order Confirmation</h1>

        <h2>Your Cart</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($product['price'], 2)); ?></td>
                        <td><?php echo $checkoutCart[$product['id']]; ?></td>
                        <td><?php echo number_format($product['price'] * $checkoutCart[$product['id']], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total: $<?php echo number_format($totalAmount, 2); ?></h3>

        <!-- Confirm Order Form -->
        <form method="POST" action="thank_you.php">
            <button class="btn" type="submit" name="confirm_order">Confirm Order</button>
        </form>

        <div style="margin-top:12px"><a class="link" href="checkout.php">Go Back to Checkout</a></div>

        <footer class="footer">
            <div class="small">&copy; <?php echo date('Y'); ?> My Shop</div>
        </footer>
    </main>

</body>

</html>

<?php $conn->close(); // Close the connection 
?>