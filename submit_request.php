<?php
session_start();
// Isama ang iyong database connection file
include 'db_config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Kunin ang data mula sa form
    // mysqli_real_escape_string ay para iwas-hack (SQL Injection)
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $blood_type = mysqli_real_escape_string($conn, $_POST['blood_type']);
    $units = mysqli_real_escape_string($conn, $_POST['units']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    
    // 2. I-check kung may notes (optional)
    $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';

    // 3. I-save sa database (Siguraduhin na 'pending' ang status para lumabas sa Admin Dashboard)
    $sql = "INSERT INTO blood_requests (name, blood_type, units, location, notes, status, created_at) 
            VALUES ('$name', '$blood_type', '$units', '$location', '$notes', 'pending', NOW())";

    if (mysqli_query($conn, $sql)) {
        // 4. Kapag successful, magpapakita ng alert at babalik sa inventory
        echo "<script>
                alert('Request submitted! The Admin has been notified.');
                window.location.href = 'blood_inventory.php';
              </script>";
    } else {
        // Kapag may error sa SQL
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

} else {
    // Kapag sinubukang i-access ang file na ito nang hindi dumadaan sa form
    header("Location: blood_inventory.php");
    exit();
}
?>