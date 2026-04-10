<?php
session_start();
require 'db_connect.php';

// Security: Only hospitals and admins can post
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'hospital' && $_SESSION['role'] !== 'admin')) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_name = $_POST['hospital_name'];
    $blood_type = $_POST['blood_type'];
    $units = $_POST['units'];
    $urgency = $_POST['urgency'];
    $details = $_POST['details'];

    // FIXED: We added 'Pending' directly to the SQL Insert command
    $stmt = $conn->prepare("INSERT INTO emergency_requests (hospital_name, blood_type, units, urgency, details, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("ssiss", $hospital_name, $blood_type, $units, $urgency, $details);

    if ($stmt->execute()) {
        // FIXED: Updated the alert message to inform the hospital
        echo "<script>
                alert('Emergency Request Submitted Successfully! Waiting for Admin Confirmation to go live.'); 
                window.location.href='emergency_requests.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Database Error. Could not post emergency.'); 
                window.location.href='create_emergency_request.php';
              </script>";
    }
}
?>