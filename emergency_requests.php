<?php
session_start();
require 'db_connect.php';
$role = $_SESSION['role'] ?? 'guest';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HemoFlow | Emergency Feed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="pt-24 bg-gray-50">

    <nav class="fixed top-0 left-0 w-full z-50 py-5 px-6 md:px-12 flex justify-between items-center bg-white/95 backdrop-blur-sm border-b border-gray-100 shadow-sm">
        <a href="index.php" class="flex items-center gap-3">
            <img src="mainlogo.png" alt="HemoFlow" class="h-10 w-auto">
            <span class="text-2xl font-bold text-red-700">HemoFlow</span>
        </a>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium">
            <a href="index.php" class="hover:text-red-700 transition">Home</a>
            <a href="blood_inventory.php" class="hover:text-red-700 transition">Blood Inventory</a>
            <a href="donors.php" class="hover:text-red-700 transition">Donors</a>
            <a href="emergency_requests.php" class="text-red-700 border-b-2 border-red-700 pb-1 font-bold">Emergency</a>
            <?php if ($role === 'admin'): ?>
                <a href="admin_dashboard.php" class="hover:text-red-700 transition">Admin Panel</a>
            <?php endif; ?>
        </div>
        <a href="logout.php" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-full font-semibold text-sm">Sign Out</a>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-4xl font-black text-gray-900">Live Emergencies</h1>
            <?php if ($role === 'hospital'): ?>
                <a href="create_emergency_request.php" class="bg-red-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-red-200">Post New Emergency</a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            $query = mysqli_query($conn, "SELECT * FROM emergency_requests WHERE status = 'Active' ORDER BY created_at DESC");
            if(mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_assoc($query)) {
                    echo "<div class='bg-white p-8 rounded-3xl border border-red-100 shadow-sm relative overflow-hidden'>
                        <div class='absolute top-0 left-0 w-full h-1.5 bg-red-600'></div>
                        <h3 class='text-xl font-black text-gray-900 mb-1'>{$row['blood_type']} Needed</h3>
                        <p class='text-sm text-gray-500 font-bold mb-4'>{$row['hospital_name']}</p>
                        <div class='bg-gray-50 p-4 rounded-xl text-xs text-gray-500 italic'>\"{$row['details']}\"</div>
                    </div>";
                }
            } else {
                echo "<p class='col-span-3 text-center text-gray-400 italic py-20'>No active emergencies in the network.</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>