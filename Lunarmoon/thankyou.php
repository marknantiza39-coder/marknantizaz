<?php

session_start();

// Get payment method for a personalized message

$method = isset($_GET['method']) ? strtoupper($_GET['method']) : 'ORDER';

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Thank You | Lunar Moon Jewelry</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@300;400&family=Playfair+Display:ital,wght@0,400;1,400&display=swap" rel="stylesheet">

    <style>

        body {

            font-family: 'Assistant', sans-serif;

            margin: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            min-height: 100vh;

            background: #fff;

            color: #121212;

            text-align: center;

        }

        .container { max-width: 600px; padding: 40px; }

        .logo { width: 120px; margin-bottom: 30px; }

        .icon-check {

            font-size: 50px;

            color: #d4af37;

            margin-bottom: 20px;

        }

        h1 {

            font-family: 'Playfair Display', serif;

            font-size: 36px;

            font-weight: 400;

            font-style: italic;

            margin-bottom: 10px;

        }

        p { color: #888; line-height: 1.6; margin-bottom: 30px; }

        .order-box {

            background: #f9f9f9;

            padding: 20px;

            border: 1px solid #eee;

            margin-bottom: 30px;

            font-size: 14px;

        }

        .btn-home {

            display: inline-block;

            background: #121212;

            color: #fff;

            text-decoration: none;

            padding: 15px 40px;

            text-transform: uppercase;

            letter-spacing: 2px;

            font-size: 12px;

            transition: 0.3s;

        }

        .btn-home:hover { background: #d4af37; }

    </style>

</head>

<body>



<div class="container">

    <img src="logo2.jpg" alt="Lunar Moon Logo" class="logo">

    <div class="icon-check"><i class="fa-regular fa-circle-check"></i></div>

    <h1>Your piece is being prepared.</h1>

    <p>Thank you for your purchase. We have received your <strong><?= $method ?></strong> order and are currently verifying the details. You will receive a confirmation email shortly.</p>

   

    <div class="order-box">

        <strong>What happens next?</strong><br>

        Our artisans will carefully package your jewelry. <br>

        Standard delivery takes 3-5 business days.

    </div>



    <a href="index.php" class="btn-home">Continue Browsing</a>

</div>



</body>

</html>