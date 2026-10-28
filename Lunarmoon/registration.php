<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "lunar_registration"; 
$conn = new mysqli($host, $user, $pass, $dbname);

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $email = $_POST['email'];
    
    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if($result->num_rows > 0){
        // EMAIL EXISTS: Send back with an error flag
        header("Location: index.php?error=emailexists");
        exit;
    } else {
        // EMAIL IS NEW: Proceed with registration
        $name = $_POST['fullname']; 
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = 'customer';

        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $pass, $role);

        if($stmt->execute()){
            header("Location: index.php?registered=1");
            exit;
        }
    }
}
?>