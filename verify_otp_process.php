<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hemoflow");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $user_otp = $_POST['otp']; // The code they typed

    // Check if OTP matches (Assuming you stored the OTP in the 'users' table or a 'temp_otp' table)
    $sql = "SELECT otp FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($user_otp == $row['otp_code']){
        // SUCCESS: Mark as verified
        $conn->query("UPDATE users SET is_verified = 1 WHERE email = '$email'");
        echo "<script>alert('Account Verified! You can now login.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Invalid OTP code.'); window.location.href='verify_otp.php?email=$email';</script>";
    }
}
?>