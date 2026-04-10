<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['full_name'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HemoFlow | Give Blood, Give Life</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        :root { --hemo-red: #b91c1c; }
        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
        .bg-hemo-red { background-color: var(--hemo-red); }
        .text-hemo-red { color: var(--hemo-red); }
        .border-hemo-red { border-color: var(--hemo-red); }
        #navbar { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-gray-50">

    <nav id="navbar" class="fixed top-0 left-0 w-full z-50 py-5 px-6 md:px-12 flex justify-between items-center bg-white/95 backdrop-blur-sm">
        <a href="index.php" class="flex items-center gap-3">
            <img src="mainlogo.png" alt="HemoFlow Logo" class="h-10 w-auto" onerror="this.src='https://via.placeholder.com/40x40.png?text=HF'">
            <span class="text-2xl font-bold text-hemo-red tracking-tight hidden sm:block">HemoFlow</span>
        </a>
        
       <div class="hidden md:flex items-center gap-8 text-gray-700 font-medium text-sm">
            <a href="index.php" class="text-hemo-red border-b-2 border-hemo-red pb-1">Home</a>
            <a href="blood_inventory.php" class="hover:text-hemo-red transition">Blood Inventory</a>
            <a href="donors.php" class="hover:text-hemo-red transition">Donors</a>
            <?php if ($role === 'hospital' || $role === 'admin'): ?>
                <a href="emergency_requests.php" class="hover:text-hemo-red transition text-red-500 font-bold">Emergency</a>
            <?php endif; ?>
            <?php if ($role === 'admin'): ?>
                <a href="admin_dashboard.php" class="hover:text-hemo-red transition">Admin Panel</a>
            <?php endif; ?>
        </div>

        <?php if ($isLoggedIn): ?>
            <div class="flex items-center gap-4">
                <span class="hidden lg:block text-sm font-medium text-gray-600">Hi, <b class="text-hemo-red"><?php echo htmlspecialchars(explode(' ', trim($userName))[0]); ?></b></span>
                <a href="logout.php" class="bg-gray-800 text-white px-6 py-2.5 rounded-full font-semibold hover:bg-black transition shadow-md text-center">Logout</a>
            </div>
        <?php else: ?>
            <a href="login.php" class="bg-hemo-red text-white px-6 py-2.5 rounded-full font-semibold hover:bg-red-800 transition shadow-md text-center">Get Started</a>
        <?php endif; ?>
    </nav>

    <header class="flex flex-col md:flex-row min-h-[700px] pt-20 md:pt-0">
        <div class="w-full md:w-5/12 bg-hemo-red text-white p-12 md:p-20 flex flex-col justify-center relative">
            <span class="inline-block border border-white/40 rounded-full px-4 py-1 text-xs mb-8 w-fit font-medium">Saving Lives Since 2025</span>
            <h1 class="text-5xl md:text-7xl font-extrabold mb-4 leading-tight">GIVE BLOOD</h1>
            <p class="text-3xl italic font-light mb-6 opacity-90">Give Life</p>
            <p class="text-xl mb-12 font-medium">Join HemoFlow</p>
            <div class="bg-white text-gray-800 rounded-2xl p-8 flex justify-around shadow-2xl max-w-sm">
                <div class="text-center"><p class="text-3xl font-bold text-hemo-red">15,847</p><p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mt-1">Active Donors</p></div>
                <div class="border-r border-gray-100"></div>
                <div class="text-center"><p class="text-3xl font-bold text-hemo-red">42,391</p><p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mt-1">Lives Saved</p></div>
            </div>
        </div>
        <div class="w-full md:w-7/12 relative min-h-[400px]">
            <img src="https://images.unsplash.com/photo-1615461066159-fea0960485d5?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover">
            <div class="absolute bottom-8 right-8 bg-white p-8 rounded-2xl shadow-2xl max-w-xs border border-gray-100">
                <p class="text-sm text-gray-600 mb-6 leading-relaxed">HemoFlow connects donors, hospitals, and blood banks to save lives.</p>
                <div class="flex gap-3">
                    <?php if ($role !== 'hospital'): ?>
                        <a href="<?php echo $isLoggedIn ? 'donors.php' : 'login.php'; ?>" class="bg-hemo-red text-white text-[11px] px-5 py-3 rounded-lg font-bold flex-1 text-center hover:bg-red-800 transition">Donate Now →</a>
                    <?php endif; ?>
                    <a href="blood_inventory.php" class="border border-hemo-red text-hemo-red text-[11px] px-5 py-3 rounded-lg font-bold flex-1 text-center hover:bg-red-50 transition <?php echo ($role === 'hospital') ? 'bg-hemo-red text-white' : ''; ?>">Find Blood</a>
                </div>
            </div>
        </div>
    </header>

    <?php if ($role !== 'hospital'): ?>
        <section class="py-24 px-6 text-center bg-white">
            <div class="flex flex-col items-center mb-16">
                <span class="text-hemo-red text-xs font-bold uppercase tracking-[0.3em] flex items-center gap-2"><i class="fa-solid fa-diamond text-[8px]"></i> OUR MISSION</span>
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mt-6 leading-tight">Connecting Lives Through<br>Blood Donation</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-16 max-w-6xl mx-auto px-4">
                <div class="flex flex-col items-center"><div class="bg-hemo-red/10 text-hemo-red w-16 h-16 rounded-full flex items-center justify-center mb-6"><i class="fa-solid fa-heart-pulse text-2xl"></i></div><h3 class="font-bold text-xl mb-3">Save Lives</h3><p class="text-gray-500 leading-relaxed text-sm max-w-xs">Every donation can save up to three lives.</p></div>
                <div class="flex flex-col items-center"><div class="bg-hemo-red/10 text-hemo-red w-16 h-16 rounded-full flex items-center justify-center mb-6"><i class="fa-solid fa-hospital text-2xl"></i></div><h3 class="font-bold text-xl mb-3">Connect Hospitals</h3><p class="text-gray-500 leading-relaxed text-sm max-w-xs">Real-time inventory management.</p></div>
                <div class="flex flex-col items-center"><div class="bg-hemo-red/10 text-hemo-red w-16 h-16 rounded-full flex items-center justify-center mb-6"><i class="fa-solid fa-clock text-2xl"></i></div><h3 class="font-bold text-xl mb-3">Emergency Response</h3><p class="text-gray-500 leading-relaxed text-sm max-w-xs">Rapid response system for urgent requests.</p></div>
            </div>
        </section>
        
        <section class="py-24 px-6">
            <div class="flex flex-col items-center mb-16 text-center">
                <span class="text-hemo-red text-xs font-bold uppercase tracking-[0.3em] flex items-center gap-2"><i class="fa-solid fa-diamond text-[8px]"></i> DONOR STORIES</span>
                <h2 class="text-4xl font-bold text-slate-900 mt-6">Voices That Inspire</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="bg-white p-10 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between"><p class="text-gray-600 text-sm italic mb-8">"HemoFlow made the process seamless."</p><div class="flex items-center gap-4 border-t pt-6"><img src="len.jpg" class="w-12 h-12 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Len'"><div><p class="font-bold text-sm">Len Milan</p><p class="text-hemo-red text-[10px] font-extrabold uppercase">Type: O+</p></div></div></div>
                <div class="bg-white p-10 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between"><p class="text-gray-600 text-sm italic mb-8">"I track my donation history."</p><div class="flex items-center gap-4 border-t pt-6"><img src="kurt.jpg" class="w-12 h-12 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Kurt'"><div><p class="font-bold text-sm">Kurt Flores</p><p class="text-hemo-red text-[10px] font-extrabold uppercase">Type: A+</p></div></div></div>
                <div class="bg-white p-10 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between"><p class="text-gray-600 text-sm italic mb-8">"HemoFlow has transformed our inventory."</p><div class="flex items-center gap-4 border-t pt-6"><img src="daniel.jpg" class="w-12 h-12 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Daniel'"><div><p class="font-bold text-sm">Daniel Briones</p><p class="text-hemo-red text-[10px] font-extrabold uppercase">RN, City Hospital</p></div></div></div>
            </div>
        </section>
        <section class="bg-hemo-red py-16 px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-12 text-white text-center">
                <div><p class="text-5xl font-bold">15K+</p><p class="text-xs opacity-70 uppercase font-bold mt-3">Active Donors</p></div>
                <div><p class="text-5xl font-bold">250+</p><p class="text-xs opacity-70 uppercase font-bold mt-3">Hospitals</p></div>
                <div><p class="text-5xl font-bold">42K+</p><p class="text-xs opacity-70 uppercase font-bold mt-3">Lives Saved</p></div>
                <div><p class="text-5xl font-bold">98%</p><p class="text-xs opacity-70 uppercase font-bold mt-3">Success</p></div>
            </div>
        </section>
    <?php endif; ?>

    <script>
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 20) {
                nav.classList.add('shadow-xl', 'py-3', 'bg-white/100'); nav.classList.remove('py-5', 'bg-white/95'); nav.style.borderBottom = "1px solid #f3f4f6";
            } else {
                nav.classList.remove('shadow-xl', 'py-3', 'bg-white/100'); nav.classList.add('py-5', 'bg-white/95'); nav.style.borderBottom = "none";
            }
        });
    </script>
</body>
</html>