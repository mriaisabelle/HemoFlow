<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$blood_type = $_GET['type'] ?? 'O+';
$requester_name = $_SESSION['full_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HemoFlow | Request Blood</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">
    <div class="bg-white max-w-md w-full rounded-[2.5rem] shadow-2xl p-10 border border-gray-100">
        <h2 class="text-3xl font-black text-gray-900 mb-2">Blood Request</h2>
        <p class="text-gray-500 mb-8 font-medium italic text-xs tracking-tight">System Request for <span class="text-red-600 font-bold"><?php echo htmlspecialchars($blood_type); ?></span></p>
        
        <form action="process_request.php" method="POST" class="space-y-4">
            <input type="hidden" name="blood_type" value="<?php echo htmlspecialchars($blood_type); ?>">
            
            <div>
                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest ml-1">Requester</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($requester_name); ?>" readonly class="w-full mt-1 p-4 bg-gray-100 border border-gray-200 rounded-2xl outline-none font-bold text-gray-600">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <input type="number" name="units" required min="1" placeholder="Units (Bags)" class="p-4 bg-gray-50 border border-gray-200 rounded-2xl">
                <input type="text" name="location" required placeholder="Hospital/City" class="p-4 bg-gray-50 border border-gray-200 rounded-2xl">
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest ml-1">Reason for Request</label>
                <textarea name="reason" rows="3" required placeholder="Explain why the blood is needed (e.g. Surgery, Emergency, etc.)" class="w-full mt-1 p-4 bg-gray-50 border border-gray-200 rounded-2xl outline-none resize-none"></textarea>
            </div>

            <button type="submit" class="w-full bg-[#b91c1c] text-white py-4 rounded-2xl font-black uppercase tracking-widest text-xs shadow-lg hover:bg-red-800 transition">Submit to Admin</button>
            <a href="blood_inventory.php" class="block text-center text-[10px] font-bold text-gray-400 mt-4 uppercase tracking-widest">Cancel</a>
        </form>
    </div>
</body>
</html>