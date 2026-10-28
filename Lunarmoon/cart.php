<?php
session_start();

// 1. Handle Quantity Updates or Removals
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_id'])) {
        $id = $_POST['update_id'];
        $action = $_POST['action'];
        
        if ($action == 'plus') {
            $_SESSION['cart'][$id]['quantity']++;
        } elseif ($action == 'minus') {
            $_SESSION['cart'][$id]['quantity']--;
            if ($_SESSION['cart'][$id]['quantity'] < 1) unset($_SESSION['cart'][$id]);
        } elseif ($action == 'remove') {
            unset($_SESSION['cart'][$id]);
        }
        header("Location: cart.php");
        exit();
    }
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Vault | Lunar Moon Jewelry</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@300;400;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        /* THEME VARIABLES */
        :root {
            --bg-color: #fff;
            --text-color: #121212;
            --header-bg: #fff;
            --border-color: #f2f2f2;
            --card-bg: #fff;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #fff;
            --header-bg: #1a1a1a;
            --border-color: #333;
            --card-bg: #1e1e1e;
        }

        body { font-family: 'Assistant', sans-serif; margin: 0; color: var(--text-color); background: var(--bg-color); transition: background 0.3s; }
        
        header { padding: 30px 50px; text-align: center; border-bottom: 1px solid var(--border-color); background: var(--header-bg); }
        .site-logo { height: 100px; mix-blend-mode: multiply; }
        [data-theme="dark"] .site-logo { filter: invert(1) hue-rotate(180deg); mix-blend-mode: screen; }
        
        .container { max-width: 900px; margin: 50px auto; padding: 0 20px; }
        h1 { font-family: 'Playfair Display', serif; font-style: italic; font-weight: 400; text-align: center; margin-bottom: 40px; }

        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .cart-table th { text-align: left; padding: 15px; border-bottom: 1px solid var(--text-color); font-size: 12px; text-transform: uppercase; letter-spacing: 2px; }
        .cart-table td { padding: 20px 15px; border-bottom: 1px solid var(--border-color); }
        
        .product-info { display: flex; align-items: center; gap: 20px; }
        .product-info img { width: 80px; height: 80px; object-fit: cover; }
        .product-name { font-family: 'Playfair Display', serif; font-size: 18px; }

        .qty-box { display: flex; align-items: center; gap: 10px; border: 1px solid var(--border-color); width: fit-content; padding: 5px 10px; }
        .qty-box button { background: none; border: none; cursor: pointer; font-size: 16px; color: #888; }
        .qty-box button:hover { color: var(--text-color); }

        .cart-summary { text-align: right; border-top: 2px solid var(--text-color); padding-top: 20px; }
        .total-label { font-size: 14px; text-transform: uppercase; letter-spacing: 2px; color: #888; }
        .total-amount { font-family: 'Playfair Display', serif; font-size: 28px; display: block; margin-bottom: 20px; }

        .btn-group { display: flex; justify-content: space-between; align-items: center; margin-top: 40px; }
        .back-link { text-decoration: none; color: var(--text-color); font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        
        .checkout-btn { 
            background: #121212; color: #fff; border: none; padding: 18px 45px; 
            text-transform: uppercase; letter-spacing: 3px; cursor: pointer; transition: 0.3s;
            display: inline-block; text-decoration: none;
        }
        [data-theme="dark"] .checkout-btn { background: #fff; color: #121212; }
        .checkout-btn:hover { background: #d4af37 !important; color: #fff !important; }

        .empty-cart { text-align: center; padding: 100px 0; }
        .empty-cart p { font-size: 18px; color: #888; margin-bottom: 20px; }
    </style>
</head>
<body>

<header>
    <a href="index.php"><img src="logo2.jpg" alt="Logo" class="site-logo"></a>
</header>

<div class="container">
    <h1>Your Selection</h1>

    <?php if (!empty($cart)): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $id => $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total_price += $subtotal;
                ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="<?= $item['image'] ?>">
                            <div>
                                <div class="product-name"><?= $item['name'] ?></div>
                                <form method="POST" style="margin-top: 5px;">
                                    <input type="hidden" name="update_id" value="<?= $id ?>">
                                    <button name="action" value="remove" style="background:none; border:none; color: #888; font-size:11px; cursor:pointer; text-decoration:underline; padding:0;">Remove</button>
                                </form>
                            </div>
                        </div>
                    </td>
                    <td>₱<?= number_format($item['price'], 2) ?></td>
                    <td>
                        <div class="qty-box">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="update_id" value="<?= $id ?>">
                                <button name="action" value="minus">−</button>
                            </form>
                            <span><?= $item['quantity'] ?></span>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="update_id" value="<?= $id ?>">
                                <button name="action" value="plus">+</button>
                            </form>
                        </div>
                    </td>
                    <td>₱<?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <span class="total-label">Estimated Total</span>
            <span class="total-amount">₱<?= number_format($total_price, 2) ?></span>
        </div>

        <div class="btn-group">
            <a href="index.php" class="back-link">← Continue Shopping</a>
            <a href="checkout.php" style="text-decoration: none;">
                <button type="button" class="checkout-btn">Secure Checkout</button>
            </a>
        </div>

    <?php else: ?>
        <div class="empty-cart">
            <p>Your vault is currently empty.</p>
            <a href="index.php" class="checkout-btn" style="text-decoration:none;">Discover Pieces</a>
        </div>
    <?php endif; ?>
</div>

<footer style="text-align: center; padding: 50px; font-size: 11px; color: #bbb; letter-spacing: 3px; text-transform: uppercase;">
    © 2026 Lunar Moon Jewelry
</footer>

<script>
    (function() {
        const saved = localStorage.getItem('lunar-theme');
        if (saved === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    })();
</script>
</body>
</html>