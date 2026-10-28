<?php
session_start();

if (file_exists('config.php')) {
    require_once 'config.php';
} else {
    die("Error: config.php not found. Please create it and define PAYREX_SECRET_KEY.");
}

spl_autoload_register(function ($class) {
    $prefix = 'Payrex\\';
    $base_dir = __DIR__ . '/vendor/payrex-lib/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

$cart = $_SESSION['cart'];
$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    $_SESSION['customer_info'] = $_POST;

    if ($payment_method == 'gcash' || $payment_method == 'bank') {
        try {
        
            \Payrex\Payrex::setVerifySslCerts(false);

            $payrex = new \Payrex\PayrexClient(PAYREX_SECRET_KEY);

            $line_items = [];
            foreach ($cart as $item) {
                $line_items[] = [
                    'name' => $item['name'],
                    'amount' => (int)($item['price'] * 100), 
                    'quantity' => (int)$item['quantity'],
                    'currency' => 'PHP',
                ];
            }

            $session = $payrex->checkoutSessions->create([
                'currency' => 'PHP',
                'payment_methods' => ['gcash', 'card', 'maya'],
                'success_url' => 'http://localhost/E-Business/success.php',
                'cancel_url' => 'http://localhost/E-Business/checkout.php',
                'line_items' => $line_items,
            ]);

            header("Location: " . $session->url);
            exit;
        } catch (Exception $e) {
            $error = "Payment Error: " . $e->getMessage();
        }
    } else {
    
        header("Location: process_order.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Lunar Moon Jewelry</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@300;400;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Assistant', sans-serif; margin: 0; color: #121212; background: #fff; }
        .checkout-container { display: flex; min-height: 100vh; }
        .shipping-section { flex: 1.2; padding: 60px 80px; }
        
        /* UPDATED: Bigger Logo */
        .logo-small { height: 100px; width: auto; mix-blend-mode: normal; margin-bottom: 40px; display: block; }
        
        h2 { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 400; margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: 1 / span 2; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 8px; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 1px solid #e0e0e0; outline: none; font-family: inherit; font-size: 14px; transition: 0.3s; }
        .input-group input:focus { border-color: #121212; }
        .order-summary { flex: 0.8; background: #f9f9f9; padding: 60px 50px; border-left: 1px solid #eee; }
        .summary-item { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
        .summary-item img { width: 65px; height: 65px; object-fit: cover; border: 1px solid #eee; }
        .summary-info { flex: 1; }
        .summary-info span { display: block; font-size: 14px; }
        .item-name { font-family: 'Playfair Display', serif; font-weight: 600; }
        .price-row { display: flex; justify-content: space-between; margin-top: 15px; font-size: 14px; color: #555; }
        .total-row { display: flex; justify-content: space-between; margin-top: 25px; padding-top: 20px; border-top: 2px solid #ddd; font-size: 20px; font-weight: 600; }
        .complete-order-btn { width: 100%; background: #121212; color: #fff; border: none; padding: 20px; text-transform: uppercase; letter-spacing: 3px; cursor: pointer; margin-top: 30px; transition: 0.3s; }
        .complete-order-btn:hover { background: #d4af37; }
        
        .error-box { background: #fee; color: #b33; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fcc; }

        @media (max-width: 900px) { .checkout-container { flex-direction: column-reverse; } .shipping-section, .order-summary { padding: 40px 20px; } }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="shipping-section">
        <a href="index.php"><img src="logo2.jpg" alt="Lunar Moon Logo" class="logo-small"></a>
        
        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php if(isset($error)): ?>
                <div class="error-box">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <h2>Shipping Information</h2>
            <div class="form-grid">
                <div class="input-group full-width">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="For order confirmation">
                </div>
                <div class="input-group">
                    <label>First Name</label>
                    <input type="text" name="fname" required>
                </div>
                <div class="input-group">
                    <label>Last Name</label>
                    <input type="text" name="lname" required>
                </div>
                <div class="input-group full-width">
                    <label>Complete Address</label>
                    <input type="text" name="address" required placeholder="House No., Street, Brgy">
                </div>
                <div class="input-group">
                    <label>City</label>
                    <input type="text" name="city" required>
                </div>
                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" required>
                </div>
            </div>

            <h2 style="margin-top:40px;">Payment Method</h2>
            <div class="input-group">
                <select name="payment_method">
                    <option value="cod">Cash on Delivery (COD)</option>
                    <option value="gcash">GCash (Online Demo)</option>
                    <option value="bank">Credit Card / Maya (Online Demo)</option>
                </select>
            </div>

            <button type="submit" class="complete-order-btn">Complete Purchase</button>
        </form>
    </div>

    <div class="order-summary">
        <h2>Order Summary</h2>
        <?php foreach ($cart as $item): ?>
        <div class="summary-item">
            <div style="position:relative;">
                <img src="<?= $item['image'] ?>">
                <span style="position:absolute; top:-10px; right:-10px; background:#888; color:#fff; width:20px; height:20px; border-radius:50%; font-size:10px; display:flex; align-items:center; justify-content:center;"><?= $item['quantity'] ?></span>
            </div>
            <div class="summary-info">
                <span class="item-name"><?= $item['name'] ?></span>
                <span style="color:#888; font-size:12px;">Purity: 18K Gold / Sterling Silver</span>
            </div>
            <span>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
        </div>
        <?php endforeach; ?>

        <div style="margin-top: 30px;">
            <div class="price-row"><span>Subtotal</span><span>₱<?= number_format($total_price, 2) ?></span></div>
            <div class="price-row"><span>Shipping</span><span>FREE</span></div>
            <div class="total-row"><span>Total</span><span>₱<?= number_format($total_price, 2) ?></span></div>
        </div>
    </div>
</div>

</body>
</html>