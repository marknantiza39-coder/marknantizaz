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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_SESSION['user_id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // 2. UPDATE BASIC INFO
    // We use 'fullname' to match your database structure
    $sql = "UPDATE users SET fullname = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $email, $uid);

    if ($stmt->execute()) {
        // Update session variables so the UI changes immediately
        $_SESSION['user_name'] = $fullname;
        $_SESSION['user_email'] = $email;

        // 3. UPDATE PASSWORD (Only if provided)
        if (!empty($new_password)) {
            $hashed_pass = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_pass->bind_param("si", $hashed_pass, $uid);
            $stmt_pass->execute();
        }

        // Redirect back to index with a success message (optional)
        header("Location: index.php?update=success");
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>