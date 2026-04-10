<?php
session_start();
require 'db_connect.php';

$isLoggedIn = isset($_SESSION['user_id']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$userName = $isLoggedIn ? $_SESSION['full_name'] : '';
$isRegisteredDonor = isset($_SESSION['is_registered_donor']) && $_SESSION['is_registered_donor'] == 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HemoFlow | Blood Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        :root { --hemo-red: #b91c1c; }
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; scroll-behavior: smooth; }
        .bg-hemo-red { background-color: var(--hemo-red); }
        .text-hemo-red { color: var(--hemo-red); }
        .border-hemo-red { border-color: var(--hemo-red); }
        #navbar { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="pt-20">

    <nav id="navbar" class="fixed top-0 left-0 w-full z-50 py-5 px-6 md:px-12 flex justify-between items-center bg-white/95 backdrop-blur-sm shadow-sm">
        <a href="index.php" class="flex items-center gap-3">
            <img src="mainlogo.png" alt="HemoFlow" class="h-10 w-auto" onerror="this.src='https://via.placeholder.com/40x40.png?text=HF'">
            <span class="text-2xl font-bold text-hemo-red tracking-tight hidden sm:block">HemoFlow</span>
        </a>
        <div class="hidden md:flex items-center gap-8 text-gray-700 font-medium text-sm">
            <a href="index.php" class="hover:text-hemo-red transition">Home</a>
            <a href="blood_inventory.php" class="border-b-2 border-hemo-red text-hemo-red pb-1 font-bold">Blood Inventory</a>
            <a href="donors.php" class="hover:text-hemo-red transition">Donors</a>
            <?php if ($role === 'hospital' || $role === 'admin'): ?>
                <a href="emergency_requests.php" class="hover:text-hemo-red transition text-red-500 font-bold">Emergency</a>
            <?php endif; ?>
            <?php if ($role === 'admin'): ?>
                <a href="admin_dashboard.php" class="hover:text-hemo-red transition">Admin Panel</a>
            <?php endif; ?>
        </div>
        
        <div class="flex gap-4 items-center">
            <?php if (!$isLoggedIn): ?>
                <a href="login.php" class="bg-hemo-red text-white px-6 py-2.5 rounded-full font-semibold hover:bg-red-800 transition shadow-md text-sm">Login to Request</a>
            <?php else: ?>
                <span class="hidden lg:block text-sm font-medium text-gray-600">Logged in as: <b class="text-hemo-red"><?php echo htmlspecialchars($userName); ?></b></span>
                <a href="logout.php" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-full font-semibold hover:bg-gray-200 transition text-sm">Logout</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="bg-gradient-to-r from-red-900 to-red-700 text-white py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <p class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest opacity-80 mb-4"><i class="fa-solid fa-droplet"></i> Live Tracking</p>
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tighter mb-4">Blood Inventory</h1>
            <p class="text-red-100 max-w-xl">Real-time supply network for partner hospitals and emergency responders.</p>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Current Supply</h2>
                <span class="text-xs font-bold bg-green-100 text-green-700 px-3 py-1 rounded-full"><i class="fa-solid fa-circle-check"></i> Network Stable</span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-black text-xl">O+</div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">High Supply</span>
                    </div>
                    <div class="mb-6">
                        <span class="block text-4xl font-black text-gray-900">420</span>
                        <span class="text-sm font-medium text-gray-500">Available Units</span>
                    </div>
                    <?php if ($isLoggedIn && ($role === 'hospital' || $role === 'donor')): ?>
                        <a href="request_form.php?type=O%2B" class="w-full bg-gray-50 hover:bg-red-50 hover:text-red-600 text-gray-600 font-bold py-3 rounded-xl border border-gray-100 transition text-center text-sm">Request Supply</a>
                    <?php endif; ?>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-black text-xl">O-</div>
                        <span class="text-xs font-bold text-red-500 uppercase tracking-widest flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span> Critical</span>
                    </div>
                    <div class="mb-6">
                        <span class="block text-4xl font-black text-gray-900">12</span>
                        <span class="text-sm font-medium text-gray-500">Available Units</span>
                    </div>
                    <?php if ($isLoggedIn && ($role === 'hospital' || $role === 'donor')): ?>
                        <a href="request_form.php?type=O-" class="w-full bg-gray-50 hover:bg-red-50 hover:text-red-600 text-gray-600 font-bold py-3 rounded-xl border border-gray-100 transition text-center text-sm">Request Supply</a>
                    <?php endif; ?>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-black text-xl">A+</div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Moderate</span>
                    </div>
                    <div class="mb-6">
                        <span class="block text-4xl font-black text-gray-900">156</span>
                        <span class="text-sm font-medium text-gray-500">Available Units</span>
                    </div>
                    <?php if ($isLoggedIn && ($role === 'hospital' || $role === 'donor')): ?>
                        <a href="request_form.php?type=A%2B" class="w-full bg-gray-50 hover:bg-red-50 hover:text-red-600 text-gray-600 font-bold py-3 rounded-xl border border-gray-100 transition text-center text-sm">Request Supply</a>
                    <?php endif; ?>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-black text-xl">B+</div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Moderate</span>
                    </div>
                    <div class="mb-6">
                        <span class="block text-4xl font-black text-gray-900">89</span>
                        <span class="text-sm font-medium text-gray-500">Available Units</span>
                    </div>
                    <?php if ($isLoggedIn && ($role === 'hospital' || $role === 'donor')): ?>
                        <a href="request_form.php?type=B%2B" class="w-full bg-gray-50 hover:bg-red-50 hover:text-red-600 text-gray-600 font-bold py-3 rounded-xl border border-gray-100 transition text-center text-sm">Request Supply</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div class="lg:col-span-1">
            
            <?php if ($role === 'hospital' || $role === 'admin'): ?>
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 sticky top-28 mb-6">
                <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-red-600"></div> Critical Alerts
                </h3>
                
                <div class="space-y-4">
                    <div class="border-l-4 border-red-600 bg-red-50/30 p-5 rounded-r-2xl relative">
                        <div class="flex justify-between items-center mb-2">
                            <span class="bg-red-600 text-white text-[10px] font-black uppercase px-2 py-1 rounded">O- CRITICAL</span>
                            <span class="text-xs font-bold text-gray-400">2h left</span>
                        </div>
                        <h4 class="font-bold text-gray-900">City General Hospital</h4>
                    </div>

                    <div class="border-l-4 border-orange-500 bg-orange-50/30 p-5 rounded-r-2xl relative">
                        <div class="flex justify-between items-center mb-2">
                            <span class="bg-orange-500 text-white text-[10px] font-black uppercase px-2 py-1 rounded">AB- LOW</span>
                            <span class="text-xs font-bold text-gray-400">4h left</span>
                        </div>
                        <h4 class="font-bold text-gray-900">Makati Med Center</h4>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 sticky top-28">
                <h4 class="font-bold text-sm text-gray-900 mb-2"><i class="fa-solid fa-circle-info text-gray-400 mr-1"></i> How to request</h4>
                <p class="text-xs text-gray-500 leading-relaxed">
                    If you represent a hospital or are a donor requiring blood, use the <b>Request Supply</b> buttons on the inventory cards. Your request will be sent to the Admin for confirmation.
                </p>
            </div>
            
        </div>

    </main>

    <footer class="bg-[#0a0f1c] text-gray-400 py-12 px-6 border-t border-white/5 mt-auto w-full">
        <div class="max-w-7xl mx-auto text-center">
            <p class="text-[11px] uppercase tracking-[0.2em]">© 2026 HemoFlow. Inventory Tracking.</p>
        </div>
    </footer>
</body>
</html>