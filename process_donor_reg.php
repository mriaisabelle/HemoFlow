<?php
session_start();
require 'PHPMailer/PHPMailer-master/PHPMailer-master/src/Exception.php';
require 'PHPMailer/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer/PHPMailer-master/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $blood_type = $_POST['blood_type'];
    $location = $_POST['location'];
    $full_name = $_SESSION['full_name'] ?? 'Hero';
    $user_email = $_SESSION['email'] ?? ''; 
    $user_id = $_SESSION['user_id'] ?? 0;

    if(empty($user_email) || $user_id == 0) {
         echo "<script>alert('Error: Session expired. Please log in again.'); window.location.href='donors.php';</script>";
         exit();
    }

    $domain = "http://localhost/hemoflow"; 
    $token_data = array("type" => $blood_type, "loc" => $location, "uid" => $user_id);
    $token = base64_encode(json_encode($token_data));
    $confirm_link = $domain . "/confirm_donor.php?token=" . urlencode($token);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mmirodrigo@tip.edu.ph'; 
        $mail->Password = 'oxyb owwu jktl efxe'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('mmirodrigo@tip.edu.ph', 'HemoFlow Verification');
        $mail->addAddress($user_email);
        $mail->isHTML(true);
        $mail->Subject = 'ACTION REQUIRED: Confirm Your Donor Registration';
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 10px; overflow: hidden;'>
                <div style='background-color: #b91c1c; color: white; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>Final Step: Confirm Registration</h2>
                </div>
                <div style='padding: 20px; color: #333;'>
                    <p>Greetings, <b>$full_name</b>,</p>
                    <p>You have successfully passed the initial pre-screening. Before we add you to the live donor network for <b>$location</b>, you must read the requirements and confirm.</p>
                    <h3 style='color: #b91c1c;'>Crucial Pre-Donation Requirements:</h3>
                    <ul style='padding-left: 20px; margin-bottom: 30px;'>
                        <li style='margin-bottom: 10px;'><b>Identification:</b> Bring a valid Government or Student ID.</li>
                        <li style='margin-bottom: 10px;'><b>Rest:</b> Have at least 6-8 hours of sleep.</li>
                        <li style='margin-bottom: 10px;'><b>Diet:</b> Eat a healthy meal. Do NOT donate on an empty stomach.</li>
                        <li style='margin-bottom: 10px;'><b>Restrictions:</b> No alcohol for 24 hours prior.</li>
                    </ul>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='$confirm_link' style='background-color: #b91c1c; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; display: inline-block;'>I AGREE - CONFIRM MY REGISTRATION</a>
                    </div>
                </div>
            </div>";

        $mail->send();
        echo "<script>alert('Pre-screening passed! Please check your email and click CONFIRM to join the live grid.'); window.location.href='donors.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Mailer Error: {$mail->ErrorInfo}'); window.location.href='donors.php';</script>";
    }
}
?>