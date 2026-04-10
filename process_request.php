<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $blood_type = mysqli_real_escape_string($conn, $_POST['blood_type']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $units = intval($_POST['units']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    $stmt = $conn->prepare("INSERT INTO blood_requests (user_id, requester_name, blood_type, units, location, reason, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("ississ", $user_id, $name, $blood_type, $units, $location, $reason);

    if ($stmt->execute()) {
        echo "<script>alert('Request Submitted! Waiting for Admin confirmation.'); window.location.href='blood_inventory.php';</script>";
    } else {
        echo "<script>alert('Error processing request.'); window.location.href='blood_inventory.php';</script>";
    }
}
?>