<?php
session_start();

// Use the customer name if available, otherwise use a generic greeting
$customer_name = isset($_SESSION['customer_info']['fname']) ? $_SESSION['customer_info']['fname'] : 'Valued Customer';

// Optional: Clear the cart since the purchase is done
// unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmed | Lunar Moon Jewelry</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@300;400;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Assistant', sans-serif; 
            margin: 0; 
            color: #121212; 
            background: #fff; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
        }
        .container { 
            text-align: center; 
            padding: 60px 40px; 
            max-width: 600px; 
            border: 1px solid #f0f0f0;
            box-shadow: 0 15px 50px rgba(0,0,0,0.05);
        }
        /* Match the bigger logo style from checkout */
        .logo-large { 
            height: 120px; 
            width: auto; 
            margin-bottom: 30px; 
        }
        .icon-wrap {
            color: #d4af37; /* Gold theme color */
            font-size: 60px;
            margin-bottom: 20px;
        }
        h1 { 
            font-family: 'Playfair Display', serif; 
            font-size: 32px; 
            font-weight: 400; 
            margin-bottom: 15px; 
            letter-spacing: 1px;
        }
        p { 
            color: #666; 
            font-size: 16px; 
            line-height: 1.8; 
            margin-bottom: 40px; 
        }
        .btn-home { 
            display: inline-block; 
            background: #121212; 
            color: #fff; 
            padding: 18px 45px; 
            text-decoration: none; 
            text-transform: uppercase; 
            letter-spacing: 3px; 
            font-size: 12px; 
            transition: 0.3s; 
            border: 1px solid #121212;
        }
        .btn-home:hover { 
            background: #fff; 
            color: #121212; 
        }
        .order-number {
            display: block;
            margin-top: 20px;
            font-size: 11px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php"><img src="logo2.jpg" alt="Lunar Moon Logo" class="logo-large"></a>
    
    <div class="icon-wrap">
        <i class="fa-solid fa-circle-check"></i>
    </div>

    <h1>Thank You, <?= htmlspecialchars($customer_name) ?></h1>
    <p>Your order has been successfully placed. We are currently preparing your handcrafted jewelry for shipment. A confirmation email will be sent to you shortly.</p>

    <a href="index.php" class="btn-home">Return to Home</a>

    <span class="order-number">Order Ref: LM-<?= rand(10000, 99999) ?></span>
</div>

</body>
</html>