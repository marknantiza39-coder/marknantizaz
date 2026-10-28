<?php
session_start();

// 1. DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "lunar_registration");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. DATA CAPTURE
$source = !empty($_SESSION['customer_info']) ? $_SESSION['customer_info'] : $_POST;

if (!empty($source)) {
    $fname = $source['fname'] ?? 'Guest';
    $lname = $source['lname'] ?? '';
    $customer_name = trim($fname . " " . $lname);
    $customer_email = $source['email'] ?? '';
    $shipping_address = ($source['address'] ?? '') . ", " . ($source['city'] ?? '') . " | Phone: " . ($source['phone'] ?? '');

    $total_price = 0;
    $items_ordered = [];
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total_price += $item['price'] * $item['quantity'];
            $items_ordered[] = $item['name'] . " (x" . $item['quantity'] . ")";
        }
    }
    $product_list = implode(", ", $items_ordered);

    // 3. DATABASE SAVE
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, shipping_address, product_name, total_price, status, order_date) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
    $stmt->bind_param("ssssd", $customer_name, $customer_email, $shipping_address, $product_list, $total_price);
    
    if ($stmt->execute()) {
        // Keep order details in session temporarily for the Thank You page to display
        $_SESSION['last_order'] = [
            'name' => $fname,
            'items' => $product_list,
            'address' => $shipping_address,
            'method' => isset($source['payment_method']) ? strtoupper($source['payment_method']) : 'ORDER'
        ];

        // Clear the actual shopping cart
        unset($_SESSION['cart']);
        unset($_SESSION['customer_info']);

        // REDIRECT TO THANKYOU.PHP
        header("Location: thankyou.php");
        exit();
    }
    $stmt->close();
} else {
    header("Location: index.php");
    exit();
}
?>