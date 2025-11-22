<?php
session_start();

// Clear the cart after successful checkout
$_SESSION['cart'] = [];

// Show a thank-you message or confirmation
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You!</title>
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
        <div class="hero">
            <h1>Thank You for Your Order!</h1>
            <p class="small">Your order has been successfully placed. You will receive a confirmation email shortly.</p>
            <div style="margin-top:12px"><a class="btn" href="index.php">Go Back to Shopping</a></div>
        </div>

        <footer class="footer">
            <div class="small">&copy; <?php echo date('Y'); ?> My Shop</div>
        </footer>
    </main>
</body>

</html>