<?php
include 'includes/db.php';

$username = "Admin";             // Admin username
$email = "admin@gmail.com";      // Admin email
$password = "admin123";          // Admin password

// Check if admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0){
    echo "Admin user already exists!";
} else {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'admin', NOW())");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    if($stmt->execute()){
        echo "Admin user created successfully!";
    } else {
        echo "Failed to create admin user.";
    }
}
$stmt->close();
$conn->close();
?>
