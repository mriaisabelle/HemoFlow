<?php
require 'db_connect.php'; // Using your connection bridge
$email = $_GET['email'] ?? '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp = trim($_POST['otp']);
    $user_email = $_POST['email'];

    // Specifically checking 'otp_code' column as per your database update
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND otp_code = ?");
    $stmt->bind_param("ss", $user_email, $user_otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $conn->query("UPDATE users SET is_verified = 1 WHERE email = '$user_email'");
        echo "<script>alert('Account Verified Successfully!'); window.location.href='login.php';</script>";
        exit();
    } else {
        $error = "Invalid verification code. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HemoFlow | Verify Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-8 lg:p-12 border border-gray-100 text-center">
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-envelope-open-text text-2xl"></i>
        </div>

        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Verify Email</h2>
        <p class="text-gray-500 text-sm mt-2 mb-8">
            We've sent a 6-digit code to <br>
            <span class="font-bold text-gray-700"><?php echo htmlspecialchars($email); ?></span>
        </p>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 text-xs p-3 rounded-xl mb-6 font-bold">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="verify_otp.php" method="POST" class="space-y-6">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            
            <div>
                <input type="text" name="otp" maxlength="6" required placeholder="000000" 
                       class="w-full text-center text-3xl tracking-[0.5em] font-bold px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all bg-gray-50/50">
            </div>

            <button type="submit" class="w-full bg-red-700 text-white py-4 rounded-2xl font-bold hover:bg-red-800 transition-all shadow-lg shadow-red-900/10 active:scale-[0.98]">
                Verify Account
            </button>
        </form>

        <p class="mt-8 text-xs text-gray-500">
            Didn't receive a code? <a href="#" class="text-red-600 font-bold hover:underline">Resend Code</a>
        </p>
    </div>

</body>
</html>