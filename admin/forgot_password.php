<?php
session_start();

if(isset($_SESSION["admin"])){
    header("Location: ./");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password - Quest Portal Admin</title>
    <?php include "head.php" ?>
    <style>
        :root {
            --quest-yellow: #fec511;
            --quest-green: #5aac7b;
            --quest-green-600: #3d8b5e;
            --slate-200: #e2e8f0;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-800: #1e293b;
        }
        * { font-family: 'Montserrat', 'Segoe UI', system-ui, sans-serif; }
        body {
            margin: 0; padding: 0; min-height: 100vh;
            background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-green-600) 50%, var(--quest-yellow) 100%);
            position: relative; overflow-x: hidden;
        }
        .bg-shapes {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            overflow: hidden; z-index: 0; pointer-events: none;
        }
        .shape {
            position: absolute; border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            animation: float-shape 20s infinite ease-in-out;
        }
        .shape:nth-child(1) { width: 400px; height: 400px; top: -100px; left: -100px; }
        .shape:nth-child(2) { width: 300px; height: 300px; top: 50%; right: -80px; animation-delay: -5s; animation-duration: 25s; }
        .shape:nth-child(3) { width: 200px; height: 200px; bottom: -50px; left: 30%; animation-delay: -10s; animation-duration: 18s; }
        .shape:nth-child(4) { width: 150px; height: 150px; top: 20%; left: 60%; animation-delay: -15s; animation-duration: 22s; }
        @keyframes float-shape {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(30px, -30px) rotate(5deg); }
            50% { transform: translate(-20px, 20px) rotate(-5deg); }
            75% { transform: translate(20px, 10px) rotate(3deg); }
        }
        .auth-wrapper {
            position: relative; z-index: 1; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 2rem 1rem;
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px; padding: 2.5rem; width: 100%; max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
            animation: scaleIn 0.6s ease forwards;
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-logo {
            width: 70px; height: 70px; border-radius: 16px;
            box-shadow: 0 8px 20px rgba(90, 172, 123, 0.25);
            margin-bottom: 1.25rem; animation: float 3s ease-in-out infinite;
        }
        .auth-title { font-size: 1.5rem; font-weight: 700; color: var(--slate-800); margin-bottom: 0.25rem; }
        .auth-subtitle { color: var(--slate-500); font-size: 0.875rem; }
        .form-section {
            background: rgba(255, 255, 255, 0.6); border-radius: 16px;
            padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .form-label {
            font-weight: 600; color: var(--slate-700); font-size: 0.875rem;
            margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .form-control-custom {
            background: #fff; border: 2px solid var(--slate-200); border-radius: 12px;
            padding: 0.75rem 1rem; font-size: 0.9375rem; transition: all 0.25s ease; width: 100%;
        }
        .form-control-custom:focus {
            outline: none; border-color: var(--quest-green);
            box-shadow: 0 0 0 4px rgba(90, 172, 123, 0.15);
        }
        .submit-btn {
            width: 100%; padding: 0.875rem;
            background: linear-gradient(135deg, var(--quest-green), var(--quest-yellow));
            color: #fff; font-weight: 700; font-size: 1rem; border: none;
            border-radius: 12px; cursor: pointer; transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(90, 172, 123, 0.4);
            position: relative; overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .submit-btn::before {
            content: ''; position: absolute; top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        .submit-btn:hover {
            transform: translateY(-2px); box-shadow: 0 8px 25px rgba(90, 172, 123, 0.5);
        }
        .submit-btn:hover::before { left: 100%; }
        .back-link {
            color: var(--quest-green-600); text-decoration: none;
            font-weight: 600; font-size: 0.875rem; transition: color 0.2s;
        }
        .back-link:hover { color: var(--quest-green); text-decoration: underline; }
        .badge-admin {
            background: rgba(254, 197, 17, 0.15);
            color: var(--quest-yellow-600, #d97706);
            font-size: 0.65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
            padding: 0.15rem 0.5rem; border-radius: 9999px;
            margin-left: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="bg-shapes">
        <div class="shape"></div><div class="shape"></div><div class="shape"></div><div class="shape"></div>
    <?php
    if (isset($_SESSION['error'])) {
    ?><script>toastr.error("<?php echo htmlspecialchars($_SESSION["error"], ENT_QUOTES, 'UTF-8') ?>", "Error!");</script><?php
        unset($_SESSION['error']);
    } elseif (isset($_SESSION["success"])) {
    ?><script>toastr.success("<?php echo htmlspecialchars($_SESSION["success"], ENT_QUOTES, 'UTF-8') ?>","Success!");</script><?php
    unset($_SESSION['success']);
    }
    ?>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <img src="assets/images/quest.jpg" alt="Quest Portal Logo" class="auth-logo">
                <h1 class="auth-title">Reset Password<span class="badge-admin">Admin</span></h1>
                <p class="auth-subtitle">Enter your email to receive a reset link</p>
            </div>
            <form action="forgot_password_handler.php" method="post">
                <div class="form-section">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope" style="color: var(--quest-green);"></i> Email Address
                        </label>
                        <input type="email" placeholder="Enter your email" id="email" name="email" class="form-control-custom" required>
                    </div>
                <input type="hidden" name="user_type" value="admin">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="login.php" class="back-link"><i class="fas fa-arrow-left me-1"></i> Back to Login</a>
                </div>
                <button type="submit" class="submit-btn"><i class="fas fa-paper-plane me-2"></i> Send Reset Link</button>
            </form>
        </div>
    <script>
        document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
    </script>
</body>
</html>
