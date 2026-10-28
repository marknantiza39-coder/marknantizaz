<?php
session_start();

if (isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];


    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

   
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'quantity' => 1
        ];
    }
    
    
    echo array_sum(array_column($_SESSION['cart'], 'quantity'));
}
?>