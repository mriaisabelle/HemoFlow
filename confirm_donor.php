<?php
session_start();
require 'db_connect.php';

if (isset($_GET['token'])) {
    $decoded_json = base64_decode($_GET['token']);
    $data = json_decode($decoded_json, true);
    
    if ($data && isset($data['uid'])) {
        $user_id = $data['uid'];
        $blood_type = $data['type'];
        $location = $data['loc'];
        
        $stmt = $conn->prepare("UPDATE users SET is_registered_donor = 1, blood_type = ?, location = ? WHERE id = ?");
        $stmt->bind_param("ssi", $blood_type, $location, $user_id);
        
        if ($stmt->execute()) {
            if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
                $_SESSION['is_registered_donor'] = true;
                $_SESSION['registered_blood_type'] = $blood_type;
                $_SESSION['registered_location'] = $location;
            }
            echo "<script>alert('Success! You are now LIVE on the database donor grid.'); window.location.href='donors.php';</script>";
            exit();
        }
    }
}
echo "<script>alert('Invalid or expired confirmation link.'); window.location.href='donors.php';</script>";
?>