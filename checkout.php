<?php
session_start();
include('db.php'); // Include the database connection file

// Check if the cart exists in the session
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // Redirect to shopping page if cart is empty
    header('Location: index.php');
    exit;
}

// Handle the checkout process (submit cart data for confirmation)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proceed_to_confirmation'])) {
    // Store the cart in session or handle any other necessary details
    $_SESSION['checkout_cart'] = $_SESSION['cart'];

    // Redirect to confirmation page
    header('Location: confirmation.php');
    exit;
}

// Fetch products in the cart (just an example for showing cart details)
$productIds = array_keys($_SESSION['cart']);
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <link rel="stylesheet" href="assets/styles.css">
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
        <h1>Checkout</h1>

        <!-- Display Cart Items -->
        <?php if (empty($products)): ?>
            <div class="notice">Your cart is empty!</div>
            <div style="margin-top:12px"><a class="link" href="index.php">Go Back to Shopping</a></div>
        <?php else: ?>
            <h2>Your Cart</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalAmount = 0;
                    foreach ($products as $product) {
                        $quantity = $_SESSION['cart'][$product['id']];
                        $total = $product['price'] * $quantity;
                        $totalAmount += $total;
                    ?>
                        <tr>
                            <td>
                                <img src="images/<?php echo htmlspecialchars($product['picture']); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($product['price'], 2)); ?></td>
                            <td><?php echo $quantity; ?></td>
                            <td><?php echo number_format($total, 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h3>Total: $<?php echo number_format($totalAmount, 2); ?></h3>

            <!-- Checkout Form -->
            <form method="POST">
                <button class="btn" type="submit" name="proceed_to_confirmation">Proceed to Confirmation</button>
            </form>

            <div style="margin-top:12px"><a class="link" href="index.php">Go Back to Shopping</a></div>
        <?php endif; ?>

        <footer class="footer">
            <div class="small">&copy; <?php echo date('Y'); ?> My Shop</div>
        </footer>
    </main>

</body>

</html>

<?php $conn->close(); // Close the connection 
?>