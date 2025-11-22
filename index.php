<?php
session_start();
include('db.php'); // Include the database connection file

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Add to cart logic
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $productId = $_GET['id'];
    $quantity = 1;

    // Add to cart
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

// Remove item from cart logic
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Check if product exists in the cart before trying to remove
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);  // Remove the item from the session
    }
}

// Update cart item quantity logic
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $id => $quantity) {
        if ($quantity == 0) {
            unset($_SESSION['cart'][$id]);  // Remove the item if quantity is zero
        } else {
            $_SESSION['cart'][$id] = $quantity;  // Update the quantity
        }
    }
}

// Check if there are any items in the cart before displaying
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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
        <section class="hero">
            <h1>Welcome to the Shopping Cart</h1>
            <p class="small">Browse products and add them to your cart.</p>
        </section>

        <div class="main-grid">
            <section>
                <h2>Products</h2>
                <ul class="product-grid">
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <li class="product">
                            <img src="images/<?php echo htmlspecialchars($product['picture']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="small">$<?php echo number_format($product['price'], 2); ?></div>
                            <div class="actions">
                                <a class="btn" href="index.php?action=add&id=<?php echo $product['id']; ?>">Add to Cart</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>

            <aside>
                <div class="cart-card">
                    <h3>Your Cart</h3>
                    <form method="POST">
                        <div>
                            <?php
                            if (!empty($cartItems)):
                                $total = 0;
                                foreach ($cartItems as $id => $quantity):
                                    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                                    $stmt->bind_param("i", $id);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $product = $res->fetch_assoc();
                                    if ($product):
                                        $total += $product['price'] * $quantity;
                            ?>
                                        <div class="cart-item">
                                            <img src="images/<?php echo htmlspecialchars($product['picture']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                            <div>
                                                <div><?php echo htmlspecialchars($product['name']); ?></div>
                                                <div class="small">$<?php echo number_format($product['price'], 2); ?> x <input style="width:60px;" type="number" name="quantity[<?php echo $product['id']; ?>]" value="<?php echo $quantity; ?>" min="1"></div>
                                            </div>
                                            <div style="margin-left:auto;">$<?php echo number_format($product['price'] * $quantity, 2); ?></div>
                                        </div>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                                <div class="small"><strong>Total: $<?php echo number_format($total, 2); ?></strong></div>
                                <div style="margin-top:10px;display:flex;gap:8px;">
                                    <button class="btn" type="submit" name="update_cart">Update Cart</button>
                                    <a class="btn secondary" href="checkout.php">Proceed to Checkout</a>
                                </div>
                            <?php else: ?>
                                <div class="small">Your cart is empty.</div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </aside>
        </div>

        <footer class="footer">
            <div class="small">&copy; <?php echo date('Y'); ?> My Shop</div>
        </footer>
    </main>

</body>

</html>

<?php $conn->close(); // Close the connection 
?>