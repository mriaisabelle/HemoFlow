<?php
$conn = new mysqli("localhost", "root", "", "hemoflow");

// Check connection
if ($conn->connect_error) { die("DB Connection failed: " . $conn->connect_error); }

$email = "admin@hemoflow.com";
$password = "123";
// Create a fresh hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 1. Delete old record
$conn->query("DELETE FROM users WHERE email = '$email'");

// 2. Insert new record
$sql = "INSERT INTO users (full_name, email, password, role, is_verified) 
        VALUES ('System Admin', '$email', '$hashed_password', 'admin', 1)";

if ($conn->query($sql)) {
    echo "<h2>Admin Reset Successful!</h2>";
    echo "<strong>Try logging in with these exact details:</strong><br>";
    echo "Email: <code style='background:#eee; padding:2px;'>admin@hemoflow.com</code><br>";
    echo "Password: <code style='background:#eee; padding:2px;'>admin123</code><br><br>";
    
    // Testing the hash immediately to be 100% sure
    if (password_verify("admin123", $hashed_password)) {
        echo "<span style='color:green;'>✓ PHP confirms this password is valid for the hash created.</span>";
    } else {
        echo "<span style='color:red;'>✗ PHP says the password doesn't match the hash. Check your PHP version.</span>";
    }
} else {
    echo "Error: " . $conn->error;
}
?>