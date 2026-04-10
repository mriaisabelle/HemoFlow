<?php
session_start();
// If the user is already logged in, send them to the home page automatically
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HemoFlow | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; overflow: hidden; }
        
        .tab-active {
            background-color: #b91c1c;
            color: white;
            box-shadow: 0 4px 12px rgba(185, 28, 28, 0.2);
        }

        .form-side::-webkit-scrollbar { width: 5px; }
        .form-side::-webkit-scrollbar-track { background: transparent; }
        .form-side::-webkit-scrollbar-thumb { background: #f1f1f1; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen w-screen p-4 md:p-8">

    <div class="w-full max-w-5xl h-full flex flex-col md:flex-row bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100">
        
        <div class="hidden md:flex w-5/12 bg-gradient-to-br from-red-700 to-red-900 p-12 flex-col justify-between text-white relative">
            <div class="absolute -top-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
             <div class="relative z-10">
                <div class="flex items-center gap-3 mb-10">
                    <img src="whitelogo.png" alt="Logo" class="h-10 w-auto" onerror="this.src='https://via.placeholder.com/40x40.png?text=HF'">
                    <span class="text-2xl font-bold tracking-tighter">HemoFlow</span>
                </div>

                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 shrink-0 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-heart-pulse text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Save Lives</h3>
                            <p class="text-xs opacity-70 leading-relaxed">Every donation can save up to three lives.</p>
                        </div>
                    </div>
                    </div>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] uppercase tracking-[0.2em] opacity-50 font-bold">© 2025 HemoFlow Systems</p>
            </div>
        </div>

        <div class="w-full md:w-7/12 flex flex-col p-6 lg:p-16 bg-white overflow-y-auto form-side">
            <div class="w-full max-w-sm mx-auto flex flex-col min-h-full">
                
                <div class="flex bg-gray-100 p-1.5 rounded-2xl mb-8 border border-gray-200">
                    <button type="button" onclick="switchRole('donor')" id="tab-donor" class="flex-1 py-2 rounded-xl text-sm font-bold transition-all duration-300 tab-active">Donor / Patient</button>
                    <button type="button" onclick="switchRole('hospital')" id="tab-hospital" class="flex-1 py-2 rounded-xl text-sm font-bold transition-all duration-300 text-gray-400">Hospital</button>
                    <button type="button" onclick="switchRole('admin')" id="tab-admin" class="flex-1 py-2 rounded-xl text-sm font-bold transition-all duration-300 text-gray-400">Admin</button>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Welcome Back</h2>
                    <p id="role-subtitle" class="text-gray-500 text-sm mt-1 transition-all duration-300">Login to your donor account</p>
                </div>

                <form action="login_process.php" method="POST" class="space-y-4">
                    <input type="hidden" name="role" id="role-input" value="donor">

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Email</label>
                        <input name="email" type="email" required placeholder="name@company.com" class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all text-sm bg-gray-50/50">
                    </div>

                    <div>
                        <div class="flex justify-between mb-1.5 ml-1">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Password</label>
                            <a href="#" class="text-[10px] font-bold text-red-600 hover:underline">Forgot?</a>
                        </div>
                        <input name="password" type="password" required placeholder="••••••••" class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all text-sm bg-gray-50/50">
                    </div>

                    <div class="flex items-center ml-1">
                        <input type="checkbox" id="remember" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="remember" class="ml-2 text-xs text-gray-500 font-medium cursor-pointer">Remember me</label>
                    </div>

                    <button type="submit" id="submit-btn" class="w-full bg-red-700 text-white py-4 rounded-2xl font-bold hover:bg-red-800 transition-all shadow-lg shadow-red-900/10 active:scale-[0.98]">
                        Sign In
                    </button>
                </form>

                <div id="extra-actions" class="transition-all duration-500 opacity-100">
                    <div class="relative my-6 text-center">
                        <span class="bg-white px-3 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] relative z-10">OR</span>
                        <div class="absolute top-1/2 w-full h-[1px] bg-gray-100"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button class="border border-gray-200 py-3 rounded-xl flex items-center justify-center gap-2 text-[11px] font-bold text-gray-700 hover:bg-gray-50 transition">
                            <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-4 h-4" alt="Google"> Google
                        </button>
                        <button class="border border-gray-200 py-3 rounded-xl flex items-center justify-center gap-2 text-[11px] font-bold text-gray-700 hover:bg-gray-50 transition">
                            <i class="fa-brands fa-apple text-sm"></i> Apple
                        </button>
                    </div>

                    <p class="text-center mt-8 text-xs text-gray-500 font-medium">
                        Don't have an account? <a href="register.html" class="text-red-600 font-bold hover:underline">Create Account</a>
                    </p>
                </div>

                <div class="mt-auto pt-8 text-center pb-4">
                    <a href="index.php" class="text-gray-400 hover:text-red-600 text-xs font-bold flex items-center justify-center gap-2 transition group">
                        <i class="fa-solid fa-arrow-left text-[10px] group-hover:-translate-x-1 transition-transform"></i> Back to website
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchRole(role) {
            const tabs = ['donor', 'hospital', 'admin'];
            const subtitle = document.getElementById('role-subtitle');
            const submitBtn = document.getElementById('submit-btn');
            const extraActions = document.getElementById('extra-actions');
            const roleInput = document.getElementById('role-input');

            // Update hidden input so PHP knows who is logging in
            roleInput.value = role;

            tabs.forEach(t => {
                const el = document.getElementById('tab-' + t);
                if (t === role) {
                    el.classList.add('tab-active');
                    el.classList.remove('text-gray-400');
                } else {
                    el.classList.remove('tab-active');
                    el.classList.add('text-gray-400');
                }
            });

            subtitle.style.opacity = '0';
            setTimeout(() => {
                if (role === 'admin') {
                    subtitle.innerText = "Admin restricted access area";
                    submitBtn.innerText = "Sign In as Admin";
                    extraActions.style.display = 'none';
                } else {
                    subtitle.innerText = `Login to your ${role} account`;
                    submitBtn.innerText = "Sign In";
                    extraActions.style.display = 'block';
                }
                subtitle.style.opacity = '1';
            }, 150);
        }
    </script>
</body>
</html>