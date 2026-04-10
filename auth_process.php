<?php
require 'PHPMailer/PHPMailer-master/PHPMailer-master/src/Exception.php';
require 'PHPMailer/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer/PHPMailer-master/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name  = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $phone      = mysqli_real_escape_string($conn, $_POST['phone']);
    $location   = mysqli_real_escape_string($conn, $_POST['location']);
    $role       = mysqli_real_escape_string($conn, $_POST['role']);
    $blood_type = ($role === 'donor') ? mysqli_real_escape_string($conn, $_POST['blood_type']) : null;

    $otp = rand(100000, 999999);

    $checkEmail = "SELECT email FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='register.html';</script>";
    } else {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'mmirodrigo@tip.edu.ph'; // Your Gmail
            $mail->Password   = 'oxyb owwu jktl efxe'; // Your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('mmirodrigo@tip.edu.ph', 'HemoFlow System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Verify your HemoFlow Account';
            $mail->Body    = "<h2>Welcome, $full_name!</h2><p>Your verification code is: <b>$otp</b></p>";

            $mail->send();

            $sql = "INSERT INTO users (full_name, email, password, blood_type, phone, location, role, otp_code, is_verified) 
                    VALUES ('$full_name', '$email', '$password', '$blood_type', '$phone', '$location', '$role', '$otp', 0)";

            if ($conn->query($sql) === TRUE) {
                header("Location: verify_otp.php?email=" . urlencode($email));
                exit();
            }
        } catch (Exception $e) {
            echo "Email error: {$mail->ErrorInfo}";
        }
    }
}
?>