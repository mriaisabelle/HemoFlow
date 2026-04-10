<?php
session_start();
require 'db_connect.php'; 

$isLoggedIn = isset($_SESSION['user_id']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$userName = $isLoggedIn ? $_SESSION['full_name'] : '';
$isRegisteredDonor = isset($_SESSION['is_registered_donor']) && $_SESSION['is_registered_donor'] == 1;

// Fetch Live DB Donors
$live_donors = [];
$query = "SELECT id, full_name, blood_type, location FROM users WHERE is_registered_donor = 1";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $live_donors[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HemoFlow | Registered Donors</title>
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
        .donor-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .donor-card:hover { transform: translateY(-5px); }
        .filter-pill.active { background-color: var(--hemo-red); color: white; border-color: var(--hemo-red); }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    </style>
</head>
<body class="pt-20">

    <nav id="navbar" class="fixed top-0 left-0 w-full z-50 py-5 px-6 md:px-12 flex justify-between items-center bg-white/95 backdrop-blur-sm">
        <a href="index.php" class="flex items-center gap-3">
            <img src="mainlogo.png" alt="HemoFlow" class="h-10 w-auto" onerror="this.src='https://via.placeholder.com/40x40.png?text=HF'">
            <span class="text-2xl font-bold text-hemo-red tracking-tight hidden sm:block">HemoFlow</span>
        </a>
        <div class="hidden md:flex items-center gap-8 text-gray-700 font-medium text-sm">
            <a href="index.php" class="hover:text-hemo-red transition">Home</a>
            <a href="blood_inventory.php" class="hover:text-hemo-red transition">Blood Inventory</a>
            <a href="donors.php" class="border-b-2 border-hemo-red text-hemo-red pb-1">Donors</a>
            <?php if ($role === 'hospital' || $role === 'admin'): ?>
                <a href="emergency_requests.php" class="hover:text-hemo-red transition text-red-500 font-bold">Emergency</a>
            <?php endif; ?>
        </div>
        
        <div class="flex gap-4">
            <?php if (!$isLoggedIn): ?>
                <button onclick="redirectToLogin()" class="bg-hemo-red text-white px-6 py-2.5 rounded-full font-semibold hover:bg-red-800 transition shadow-md text-sm">Register as Donor</button>
            <?php elseif ($isLoggedIn && $role === 'donor'): ?>
                <?php if (!$isRegisteredDonor): ?>
                    <button onclick="openRegistrationModal()" class="bg-hemo-red text-white px-6 py-2.5 rounded-full font-semibold hover:bg-red-800 transition shadow-md text-sm">Register as Donor</button>
                <?php else: ?>
                    <button class="bg-green-600 text-white px-6 py-2.5 rounded-full font-semibold cursor-default shadow-md text-sm"><i class="fa-solid fa-check-circle mr-1"></i> Verified Donor</button>
                <?php endif; ?>
            <?php elseif ($isLoggedIn && $role === 'hospital'): ?>
                <a href="create_emergency_request.php" class="bg-red-600 text-white px-6 py-2.5 rounded-full font-semibold hover:bg-red-700 transition shadow-md text-sm"><i class="fa-solid fa-truck-medical mr-1"></i> Post Emergency</a>
            <?php endif; ?>

            <?php if ($isLoggedIn): ?>
                <a href="logout.php" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-full font-semibold hover:bg-gray-200 transition text-sm">Logout</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="bg-gradient-to-r from-red-900 to-red-700 text-white py-16 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10">
            <div>
                <p class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest opacity-80 mb-4"><i class="fa-solid fa-users"></i> Our Community</p>
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tighter mb-4">Registered Donors</h1>
                <p class="text-red-100 max-w-xl italic">Logged in as: <?php echo $isLoggedIn ? htmlspecialchars($userName) : 'Guest (Viewing Network)'; ?></p>
            </div>
        </div>
    </header>

    <section class="max-w-7xl mx-auto px-6 mt-10">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col lg:flex-row gap-6 items-center">
            <div class="relative w-full lg:w-1/3">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchInput" placeholder="Search..." class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-500 outline-none transition">
            </div>
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-2/3">
                <button class="filter-pill active px-4 py-1.5 rounded-full border text-xs font-bold" onclick="filterType('All')">All</button>
                <button class="filter-pill px-4 py-1.5 rounded-full border text-xs font-bold" onclick="filterType('O-')">O-</button>
                <button class="filter-pill px-4 py-1.5 rounded-full border text-xs font-bold" onclick="filterType('O+')">O+</button>
                <button class="filter-pill px-4 py-1.5 rounded-full border text-xs font-bold" onclick="filterType('A-')">A-</button>
                <button class="filter-pill px-4 py-1.5 rounded-full border text-xs font-bold" onclick="filterType('A+')">A+</button>
                <button class="filter-pill px-4 py-1.5 rounded-full border text-xs font-bold" onclick="filterType('B-')">B-</button>
                <button class="filter-pill px-4 py-1.5 rounded-full border text-xs font-bold" onclick="filterType('B+')">B+</button>
                <button class="filter-pill px-4 py-1.5 rounded-full border text-xs font-bold" onclick="filterType('AB-')">AB-</button>
                <button class="filter-pill px-4 py-1.5 rounded-full border text-xs font-bold" onclick="filterType('AB+')">AB+</button>
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <p class="text-gray-500 text-sm mb-8 font-medium" id="resultsCount">Showing donors...</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8" id="donorGrid"></div>
    </main>

    <?php if ($role === 'donor'): ?>
    <div id="registrationModal" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeRegistrationModal()"></div>
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] relative z-10 p-8 border border-gray-100 custom-scroll">
            <h2 class="text-2xl font-black text-gray-900 mb-2">Pre-Donation Screening</h2>
            <form action="process_donor_reg.php" method="POST" onsubmit="return validateScreening()">
                <div class="space-y-3 mb-8">
                    <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-gray-100 border border-transparent hover:border-red-100"><input type="checkbox" class="w-5 h-5 accent-red-600" id="anemic"><span class="text-sm font-semibold">Anemia / Low Iron</span></label>
                    <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-gray-100 border border-transparent hover:border-red-100"><input type="checkbox" class="w-5 h-5 accent-red-600" id="disorder"><span class="text-sm font-semibold">Blood Disorder</span></label>
                    <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-gray-100 border border-transparent hover:border-red-100"><input type="checkbox" class="w-5 h-5 accent-red-600" id="weight"><span class="text-sm font-semibold">Under 50kg</span></label>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <select name="blood_type" class="w-full mt-1 p-3 bg-gray-50 border border-gray-200 rounded-xl font-bold"><option>O+</option><option>O-</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option></select>
                    <input type="text" name="location" required placeholder="City" class="w-full mt-1 p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                </div>
                <button type="submit" class="w-full bg-red-700 text-white py-4 rounded-2xl font-black uppercase text-xs">Confirm Registration</button>
                <button type="button" onclick="closeRegistrationModal()" class="w-full text-xs font-bold text-gray-400 mt-4">Cancel</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div id="profileModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] relative z-10 overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-300">
            <button onclick="closeModal()" class="absolute right-6 top-6 w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        // ALL 8 ORIGINAL STATIC FAKE DONORS KEPT INTACT
        const donors = [
            { id: 1001, name: "Isabelle Rodrigo", type: "O+", location: "Manila City", status: "Available", donations: 47, helped: 141, img: "isabelle.jpg" },
            { id: 1002, name: "Kurt Flores", type: "A+", location: "Meycauayan, Bulacan", status: "Cooldown", donations: 23, helped: 69, img: "kurt.jpg" },
            { id: 1003, name: "Joharry Faisal", type: "B+", location: "Manila City", status: "Available", donations: 31, helped: 93, img: "joharry.jpg" },
            { id: 1004, name: "Raven Unera", type: "AB+", location: "Taguig City", status: "Cooldown", donations: 62, helped: 186, img: "raven.jpg" },
            { id: 1005, name: "Len Milan", type: "O+", location: "Meycauayan, Bulacan", status: "Available", donations: 12, helped: 36, img: "len.jpg" },
            { id: 1006, name: "Daniel Briones", type: "A-", location: "South Caloocan City", status: "Cooldown", donations: 5, helped: 15, img: "daniel.jpg" },
            { id: 1007, name: "Bless Boctoy", type: "B-", location: "Makati City", status: "Available", donations: 19, helped: 57, img: "bless.jpg" },
            { id: 1008, name: "Bryan Torres", type: "AB-", location: "North Caloocan City", status: "Available", donations: 28, helped: 84, img: "bryan.jpg" }
        ];

        <?php foreach ($live_donors as $db_donor): 
            $is_me = ($isLoggedIn && $_SESSION['user_id'] == $db_donor['id']) ? 'true' : 'false';
            $display_name = addslashes($db_donor['full_name']) . ($is_me === 'true' ? " (You)" : "");
        ?>
        donors.unshift({
            id: <?php echo $db_donor['id']; ?>, 
            name: "<?php echo $display_name; ?>",
            type: "<?php echo addslashes($db_donor['blood_type']); ?>",
            location: "<?php echo addslashes($db_donor['location']); ?>",
            status: "Available", donations: 0, helped: 0, isYou: <?php echo $is_me; ?>,
            img: "https://ui-avatars.com/api/?name=<?php echo urlencode($db_donor['full_name']); ?>&background=b91c1c&color=fff&size=200"
        });
        <?php endforeach; ?>

        let currentType = 'All';

        function renderDonors(data) {
            const grid = document.getElementById('donorGrid');
            const count = document.getElementById('resultsCount');
            grid.innerHTML = '';
            data.forEach(donor => {
                const isYou = donor.isYou ? 'border-red-500 border-2 shadow-md' : 'border-gray-100 border';
                grid.innerHTML += `
                    <div class="donor-card bg-white p-8 rounded-[2rem] shadow-sm ${isYou} flex flex-col items-center text-center relative overflow-hidden">
                        <div class="absolute top-4 right-4 w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-600 font-bold text-xs">${donor.type}</div>
                        <div class="w-20 h-20 rounded-full border-4 border-gray-50 overflow-hidden mb-3"><img src="${donor.img}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=User'"></div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">${donor.name}</h3>
                        <p class="text-xs text-gray-400 font-medium italic mb-6">${donor.location}</p>
                        <button onclick="viewProfile(${donor.id})" class="w-full py-3 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:border-red-600 hover:text-red-600 transition">View Profile</button>
                    </div>`;
            });
            count.innerText = `Showing ${data.length} donors in network`;
        }

        function viewProfile(id) {
            const donor = donors.find(d => d.id === id);
            const modal = document.getElementById('profileModal');
            const content = document.getElementById('modalContent');
            content.innerHTML = `
                <div class="h-32 bg-gradient-to-r from-red-800 to-red-600"></div>
                <div class="px-8 pb-10 -mt-12">
                    <div class="flex justify-between items-end mb-6">
                        <img src="${donor.img}" class="w-24 h-24 rounded-3xl border-4 border-white shadow-lg object-cover bg-white" onerror="this.src='https://ui-avatars.com/api/?name=User'">
                        <span class="px-4 py-2 bg-red-600 text-white rounded-xl font-bold text-lg">${donor.type}</span>
                    </div>
                    <h2 class="text-3xl font-black text-gray-900 mb-1">${donor.name}</h2>
                    <p class="text-gray-500 font-medium mb-6"><i class="fa-solid fa-location-dot text-red-500"></i> ${donor.location}</p>
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="bg-gray-50 p-4 rounded-2xl text-center"><span class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status</span><span class="text-sm font-bold text-gray-800">${donor.status}</span></div>
                        <div class="bg-gray-50 p-4 rounded-2xl text-center"><span class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Rank</span><span class="text-sm font-bold text-red-600">Hero</span></div>
                        <div class="bg-gray-50 p-4 rounded-2xl text-center"><span class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Joined</span><span class="text-sm font-bold text-gray-800">2026</span></div>
                    </div>
                    <button onclick="closeModal()" class="w-full bg-gray-100 text-gray-600 py-4 rounded-2xl font-bold hover:bg-gray-200 transition">Close</button>
                </div>`;
            modal.classList.remove('hidden');
        }

        function redirectToLogin() { alert("Login required."); window.location.href = "login.php"; }
        function openRegistrationModal() { document.getElementById('registrationModal').classList.remove('hidden'); }
        function closeRegistrationModal() { document.getElementById('registrationModal').classList.add('hidden'); }
        function validateScreening() {
            if (document.getElementById('anemic').checked || document.getElementById('disorder').checked || document.getElementById('weight').checked) {
                alert("You do not meet safety requirements."); return false;
            }
            return true; 
        }

        function updateDisplay() {
            const term = document.getElementById('searchInput').value.toLowerCase();
            const filtered = donors.filter(d => (d.name.toLowerCase().includes(term) || d.location.toLowerCase().includes(term)) && (currentType === 'All' || d.type === currentType));
            renderDonors(filtered);
        }
        function filterType(type) { currentType = type; document.querySelectorAll('.filter-pill').forEach(btn => btn.classList.toggle('active', btn.innerText === type)); updateDisplay(); }
        document.getElementById('searchInput').addEventListener('input', updateDisplay);

        function closeModal() { document.getElementById('profileModal').classList.add('hidden'); }

        renderDonors(donors);
    </script>
</body>
</html>