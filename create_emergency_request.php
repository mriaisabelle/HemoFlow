<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'hospital' && $_SESSION['role'] !== 'admin')) {
    header("Location: index.php");
    exit();
}
$hospital_name = $_SESSION['full_name'] ?? 'Hospital';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HemoFlow | Post Emergency</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    
    <nav class="bg-white border-b border-gray-100 px-6 py-4 shadow-sm">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <a href="emergency_requests.php" class="text-gray-500 hover:text-red-700 font-bold text-sm">← Back to Feed</a>
            <span class="text-xl font-black text-red-700">HemoFlow Emergency</span>
            <div class="w-24"></div> 
        </div>
    </nav>

    <div class="flex-grow flex items-center justify-center p-6">
        <div class="bg-white max-w-lg w-full rounded-[2.5rem] shadow-2xl p-10 border border-red-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-red-600"></div>
            <h2 class="text-2xl font-black text-gray-900 mb-6">Post Emergency</h2>
            
            <form action="process_emergency.php" method="POST" class="space-y-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-gray-400">Hospital Facility</label>
                    <input type="text" name="hospital_name" required value="<?php echo htmlspecialchars($hospital_name); ?>" readonly class="w-full mt-1 p-4 bg-gray-100 border border-gray-300 rounded-2xl outline-none font-bold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <select name="blood_type" required class="w-full mt-1 p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none font-bold"><option>O+</option><option>O-</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option></select>
                    <input type="number" name="units" required min="1" placeholder="Bags" class="w-full mt-1 p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none">
                </div>
                <select name="urgency" required class="w-full mt-1 p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none font-bold text-red-600"><option value="Critical">Critical (Within 2 Hrs)</option><option value="High">High (Within 12 Hrs)</option></select>
                <textarea name="details" rows="3" required placeholder="Patient contact info..." class="w-full mt-1 p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none resize-none"></textarea>
                <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-2xl font-black uppercase text-sm">Broadcast to Network</button>
            </form>
        </div>
    </div>
</body>
</html>