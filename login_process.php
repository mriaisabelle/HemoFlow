<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); 

    $stmt = $conn->prepare("SELECT id, full_name, password, role, is_verified, is_registered_donor FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // OTP Gatekeeper
        if ($user['is_verified'] == 0) {
            header("Location: verify_otp.php?email=" . urlencode($email));
            exit();
        }

        if (password_verify($password, $user['password'])) {
            // Set Session Variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $email; 
            $_SESSION['is_registered_donor'] = ($user['is_registered_donor'] == 1);

            // Clean the role string to avoid redirection errors
            $userRole = trim(strtolower($user['role']));

            // Direction Logic
            if ($userRole === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
            
        } else {
            echo "<script>alert('Invalid Password'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Email not found'); window.location.href='login.php';</script>";
    }
}
?>