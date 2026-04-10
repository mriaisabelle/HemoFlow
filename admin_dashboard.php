<?php
session_start();
include 'db_connect.php';

// --- BACKEND LOGIC: HANDLE APPROVALS ---

// 1. Approve Emergency Broadcast (From Hospital)
if (isset($_GET['approve_emerg'])) {
    $id = intval($_GET['approve_emerg']);
    mysqli_query($conn, "UPDATE emergency_requests SET status = 'Active' WHERE id = $id");
    // This triggers the floating alert via JS on redirect
    header("Location: admin_dashboard.php?success=1");
    exit();
}

// 2. Approve Blood Request (From Donor/Hospital)
if (isset($_GET['approve_blood'])) {
    $id = intval($_GET['approve_blood']);
    mysqli_query($conn, "UPDATE blood_requests SET status = 'approved' WHERE id = $id");
    header("Location: admin_dashboard.php?success=1");
    exit();
}

// --- DATA FETCHING ---

// Count total pending items for the badge
$blood_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM blood_requests WHERE status = 'pending'");
$emerg_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM emergency_requests WHERE status = 'Pending'");
$blood_data = mysqli_fetch_assoc($blood_q);
$emerg_data = mysqli_fetch_assoc($emerg_q);
$pending_count = ($blood_data['total'] ?? 0) + ($emerg_data['total'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HemoFlow | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        :root { --hemo-red: #b91c1c; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .text-hemo-red { color: var(--hemo-red); }
        .bg-hemo-red { background-color: var(--hemo-red); }
        .tab-btn.active { background-color: white; color: var(--hemo-red); box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        #navbar { transition: all 0.3s ease-in-out; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* Floating Alert Animation */
        #floating-alert { transform: translateY(-100px); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        #floating-alert.show { transform: translateY(20px); }
    </style>
</head>
<body class="pt-24" onload="initDashboard()">

    <div id="floating-alert" class="fixed top-0 left-0 w-full flex justify-center z-[100] pointer-events-none <?php echo isset($_GET['success']) ? 'show' : ''; ?>">
        <div class="bg-slate-900 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-4 border border-slate-700">
            <div class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></div>
            <span class="text-sm font-bold tracking-wide">Action Confirmed & Database Updated</span>
        </div>
    </div>

    <div id="trackingModal" class="fixed inset-0 z-[60] flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-white w-full max-w-md m-4 p-8 rounded-[3rem] shadow-2xl border border-gray-100">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-2xl font-black text-gray-900">Emergency Dispatch</h3>
                    <p class="text-sm text-gray-400 font-medium italic" id="targetHospital">Recipient: ---</p>
                </div>
                <button onclick="closeTracking()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-circle-xmark text-2xl"></i></button>
            </div>
            <div class="relative w-full h-3 bg-gray-100 rounded-full mb-10">
                <div id="tracking-bar" class="absolute h-full bg-red-600 rounded-full transition-all duration-1000" style="width: 0%"></div>
                <div id="courier-icon" class="absolute -top-5 transition-all duration-1000" style="left: 0%">
                    <div class="bg-white shadow-xl p-3 rounded-2xl border border-gray-50">
                        <i class="fa-solid fa-ambulance text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 p-6 rounded-[2rem] border border-gray-100">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></div>
                    <p id="status-text" class="text-sm font-bold text-gray-700">Connecting to courier...</p>
                </div>
                <div class="flex justify-between items-end">
                    <div><p class="text-[10px] uppercase font-black text-gray-400 tracking-widest mb-1">ETA</p><p id="eta-text" class="text-lg font-black text-gray-900">Calculated</p></div>
                    <div class="text-right"><p class="text-[10px] uppercase font-black text-gray-400 tracking-widest mb-1">Status</p><p class="text-xs font-bold text-red-600 bg-red-50 px-3 py-1 rounded-lg">Live Dispatch</p></div>
                </div>
            </div>
        </div>
    </div>

    <nav id="navbar" class="fixed top-0 left-0 w-full z-50 py-5 px-6 md:px-12 flex justify-between items-center bg-white/95 backdrop-blur-sm border-b border-gray-100">
        <a href="index.php" class="flex items-center gap-3">
            <img src="mainlogo.png" alt="HemoFlow" class="h-10 w-auto">
            <span class="text-2xl font-bold text-hemo-red tracking-tight">HemoFlow Admin</span>
        </a>
        <div class="hidden md:flex items-center gap-8 text-gray-700 font-medium text-sm">
            <a href="admin_dashboard.php" class="text-hemo-red border-b-2 border-hemo-red pb-1">Dashboard</a>
            <a href="inventory_admin.html" class="hover:text-gray-500 transition">Admin Inventory</a>
        </div>
        <a href="logout.php" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-full font-semibold hover:bg-gray-200 transition text-sm">Sign Out</a>
    </nav>

    <header class="max-w-7xl mx-auto px-6 mb-10">
        <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
            <div class="absolute right-0 top-0 w-64 h-64 bg-red-600/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-end gap-6">
                <div>
                    <h1 class="text-4xl font-black tracking-tighter mb-2">Admin Control Center</h1>
                    <p class="text-slate-400 font-medium italic">Welcome back. System status: <span class="text-green-400 font-bold">Operational</span></p>
                </div>
                <a href="inventory_admin.html">
    <button class="bg-hemo-red hover:bg-red-700 text-white px-8 py-4 rounded-2xl font-bold transition shadow-lg shadow-red-900/40">
        <i class="fa-solid fa-boxes-stacked mr-2"></i> Manage Stock
    </button>
</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-20">
        <div class="bg-gray-200/50 p-1.5 rounded-2xl inline-flex mb-12">
            <button onclick="switchTab('donors')" id="tab-donors" class="tab-btn active px-8 py-3 rounded-xl text-sm font-bold transition-all">Registered Donors</button>
            <button onclick="switchTab('requests')" id="tab-requests" class="tab-btn px-8 py-3 rounded-xl text-sm font-bold text-gray-500 transition-all">Emergency Feed</button>
            <button onclick="switchTab('pending')" id="tab-pending" class="tab-btn px-8 py-3 rounded-xl text-sm font-bold text-gray-500 transition-all flex items-center gap-2">
                Approvals <span id="pending-badge" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-md text-[10px]"><?php echo $pending_count; ?></span>
            </button>
        </div>

        <section id="section-donors" class="animate-in fade-in duration-500">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="donorList"></div>
        </section>

        <section id="section-requests" class="hidden animate-in fade-in duration-500">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                // Show only Active (approved) emergencies here
                $active_q = mysqli_query($conn, "SELECT * FROM emergency_requests WHERE status = 'Active' ORDER BY created_at DESC");
                while($e = mysqli_fetch_assoc($active_q)) {
                    echo "<div class='bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl relative overflow-hidden'>
                        <div class='absolute top-0 right-0 p-4'><span class='bg-red-600 text-white text-[8px] font-black px-3 py-1 rounded-full uppercase'>{$e['urgency']}</span></div>
                        <div class='text-4xl font-black text-red-600 mb-4'>{$e['blood_type']}</div>
                        <h3 class='text-xl font-bold text-gray-900 mb-1'>{$e['hospital_name']}</h3>
                        <p class='text-sm text-gray-500 font-bold mb-6 italic'>Units: {$e['units']}</p>
                        <div class='flex gap-3'>
                            <button onclick=\"startTrackingSimulation('{$e['hospital_name']}')\" class='flex-1 bg-hemo-red text-white py-3 rounded-xl font-bold text-xs hover:bg-red-700 transition'>Respond Now</button>
                        </div>
                    </div>";
                }
                ?>
            </div>
        </section>

        <section id="section-pending" class="hidden animate-in fade-in duration-500 space-y-8">
            
            <div class="bg-white rounded-[2rem] border border-gray-100 overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-50 font-black text-xs uppercase tracking-widest text-gray-400">Hospital Emergency Broadcasts</div>
                <table class="w-full text-left">
                    <tbody class="divide-y divide-gray-50">
                        <?php
                        $pending_e = mysqli_query($conn, "SELECT * FROM emergency_requests WHERE status = 'Pending'");
                        while($row = mysqli_fetch_assoc($pending_e)) {
                            echo "<tr>
                                <td class='px-8 py-5 font-bold text-gray-900'>{$row['hospital_name']}</td>
                                <td class='px-8 py-5 text-sm font-bold text-red-600'>{$row['blood_type']} ({$row['urgency']})</td>
                                <td class='px-8 py-5 text-right'>
                                    <a href='admin_dashboard.php?approve_emerg={$row['id']}' class='bg-green-500 text-white px-6 py-2 rounded-lg text-[10px] font-bold hover:bg-green-600 transition inline-block'>Approve Broadcast</a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-[2rem] border border-gray-100 overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-50 font-black text-xs uppercase tracking-widest text-gray-400">Donor/Patient Blood Requests</div>
                <table class="w-full text-left">
                    <tbody class="divide-y divide-gray-50">
                        <?php
                        $pending_b = mysqli_query($conn, "SELECT * FROM blood_requests WHERE status = 'pending'");
                        while($row = mysqli_fetch_assoc($pending_b)) {
                            echo "<tr>
                                <td class='px-8 py-5 font-bold text-gray-900'>{$row['requester_name']}</td>
                                <td class='px-8 py-5 text-sm font-bold text-red-600'>{$row['blood_type']} ({$row['units']} Units)</td>
                                <td class='px-8 py-5 text-right'>
                                    <a href='admin_dashboard.php?approve_blood={$row['id']}' class='bg-green-500 text-white px-6 py-2 rounded-lg text-[10px] font-bold hover:bg-green-600 transition inline-block'>Confirm Request</a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        // RESTORED: Fake Donor List
        const donors = [
            { id: 1, name: "Isabelle Rodrigo", type: "O+", status: "Available", img: "isabelle.jpg" },
            { id: 2, name: "Kurt Flores", type: "A+", status: "Cooldown", img: "kurt.jpg" },
            { id: 3, name: "Joharry Faisal", type: "B+", status: "Available", img: "joharry.jpg" },
            { id: 4, name: "Raven Unera", type: "AB+", status: "Cooldown", img: "raven.jpg" },
            { id: 5, name: "Len Milan", type: "O-", status: "Cooldown", img: "len.jpg" },
            { id: 6, name: "Daniel Briones", type: "O+", status: "Available", img: "daniel.jpg" }
        ];

        function switchTab(tab) {
            document.querySelectorAll('section').forEach(s => s.classList.add('hidden'));
            document.getElementById(`section-${tab}`).classList.remove('hidden');
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'text-hemo-red'); b.classList.add('text-gray-500');
            });
            document.getElementById(`tab-${tab}`).classList.add('active', 'text-hemo-red');
        }

        function initDashboard() {
            const dList = document.getElementById('donorList');
            donors.forEach(d => {
                dList.innerHTML += `<div class="bg-white p-6 rounded-[2rem] border border-gray-100 flex flex-col items-center shadow-sm">
                    <img src="${d.img}" class="w-16 h-16 rounded-2xl mb-4 object-cover bg-gray-100" onerror="this.src='https://ui-avatars.com/api/?name=${d.name}'">
                    <h4 class="font-bold text-gray-900">${d.name}</h4>
                    <p class="text-[10px] uppercase font-black tracking-widest text-red-600 mb-4">${d.type} Donor</p>
                    <div class="flex gap-2 w-full">
                        <button class="flex-1 bg-gray-50 text-[10px] font-bold py-2 rounded-lg">Profile</button>
                        <button class="flex-1 bg-red-50 text-red-600 text-[10px] font-bold py-2 rounded-lg">Remove</button>
                    </div>
                </div>`;
            });

            // Auto-hide alert after 3 seconds if it was shown on reload
            const alert = document.getElementById('floating-alert');
            if(alert.classList.contains('show')) {
                setTimeout(() => alert.classList.remove('show'), 3000);
            }
        }

        // RESTORED: Tracking Animation Logic
        function startTrackingSimulation(hospitalName) {
            document.getElementById('trackingModal').classList.remove('hidden');
            document.getElementById('targetHospital').innerText = "Recipient: " + hospitalName;
            
            let progress = 0;
            const bar = document.getElementById('tracking-bar');
            const icon = document.getElementById('courier-icon');
            const status = document.getElementById('status-text');
            const eta = document.getElementById('eta-text');

            bar.style.width = "0%";
            icon.style.left = "0%";
            
            const statusMessages = ["Units secured. Departing...", "Navigating traffic...", "Near destination...", "Arrived at ER Entrance", "Handover Complete."];

            const interval = setInterval(() => {
                if (progress >= 100) {
                    clearInterval(interval);
                    status.innerText = "Blood Delivered Successfully!";
                    eta.innerText = "ARRIVED";
                } else {
                    progress += 20;
                    bar.style.width = progress + "%";
                    icon.style.left = `calc(${progress}% - 20px)`;
                    let msgIndex = Math.floor((progress / 100) * (statusMessages.length - 1));
                    status.innerText = statusMessages[msgIndex];
                    eta.innerText = Math.max(0, 10 - Math.floor(progress / 10)) + " Mins";
                }
            }, 1000);
        }

        function closeTracking() {
            document.getElementById('trackingModal').classList.add('hidden');
        }
    </script>
</body>
</html>