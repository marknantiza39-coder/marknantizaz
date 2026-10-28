<?php
session_start();

// 1. DATABASE CONNECTION
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "lunar_registration"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. HANDLE LOGIN FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Search for user in your table (Update 'users' to your actual table name if different)
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if password matches
        if (password_verify($password, $user['password'])) {
            // SET SESSION VARIABLES
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['role'] = $user['role']; // This detects 'admin' or 'customer'

            // 3. REDIRECT LOGIC
            if ($user['role'] === 'admin') {
                // If admin, go to dashboard
                header("Location: admin.php");
            } else {
                // If customer, go home with a success message
                header("Location: index.php?login=success");
            }
            exit();
        } else {
            // Wrong password
            echo "<script>alert('Invalid Password'); window.location.href='index.php';</script>";
        }
    } else {
        // No user found with that email
        echo "<script>alert('No account found with that email'); window.location.href='index.php';</script>";
    }
}

$conn->close();
?>