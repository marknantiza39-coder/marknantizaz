<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "lunar_registration"; // Must match your phpMyAdmin name exactly

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>