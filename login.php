<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login | Quest Schools - Student Portal</title>
    <?php include "head.php" ?>
    <style>
        :root {
            --quest-yellow: #fec511;
            --quest-green: #5aac7b;
            --quest-green-50: #f0faf4;
            --quest-green-100: #d4f0df;
            --quest-green-600: #3d8b5e;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
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
        .shape:nth-child(1) { width: 400px; height: 400px; top: -100px; left: -100px; animation-delay: 0s; }
        .shape:nth-child(2) { width: 300px; height: 300px; top: 50%; right: -80px; animation-delay: -5s; animation-duration: 25s; }
        .shape:nth-child(3) { width: 200px; height: 200px; bottom: -50px; left: 30%; animation-delay: -10s; animation-duration: 18s; }
        .shape:nth-child(4) { width: 150px; height: 150px; top: 20%; left: 60%; animation-delay: -15s; animation-duration: 22s; }
        @keyframes float-shape {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(30px, -30px) rotate(5deg); }
            50% { transform: translate(-20px, 20px) rotate(-5deg); }
            75% { transform: translate(20px, 10px) rotate(3deg); }
        }
        .login-wrapper {
            position: relative; z-index: 1; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 2rem 1rem;
        }
        .login-card {
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
        .login-header { text-align: center; margin-bottom: 2rem; }
        .login-logo {
            width: 80px; height: 80px; border-radius: 20px;
            box-shadow: 0 10px 25px rgba(90, 172, 123, 0.3);
            margin-bottom: 1.5rem; animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .login-title { font-size: 1.75rem; font-weight: 800; color: var(--slate-800); margin-bottom: 0.5rem; }
        .login-subtitle { color: var(--slate-500); font-size: 0.9375rem; }
        .form-section {
            background: rgba(255, 255, 255, 0.6); border-radius: 16px;
            padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .section-title {
            color: var(--quest-green-600); font-weight: 700; margin-bottom: 1.25rem;
            font-size: 0.9375rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .form-label {
            font-weight: 600; color: var(--slate-700); font-size: 0.875rem;
            margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .form-control-custom {
            background: #fff; border: 2px solid var(--slate-200); border-radius: 12px;
            padding: 0.875rem 1rem; font-size: 0.9375rem; transition: all 0.25s ease; width: 100%;
        }
        .form-control-custom:focus {
            outline: none; border-color: var(--quest-green);
            box-shadow: 0 0 0 4px rgba(90, 172, 123, 0.15);
        }
        .input-group-custom {
            display: flex; border: 2px solid var(--slate-200); border-radius: 12px;
            overflow: hidden; transition: all 0.25s ease;
        }
        .input-group-custom:focus-within {
            border-color: var(--quest-green); box-shadow: 0 0 0 4px rgba(90, 172, 123, 0.15);
        }
        .input-group-custom .form-control-custom { border: none; border-radius: 0; box-shadow: none; }
        .input-group-custom .form-control-custom:focus { box-shadow: none; }
        .password-toggle {
            background: transparent; border: none; padding: 0 1rem;
            color: var(--slate-400); cursor: pointer; transition: color 0.2s;
        }
        .password-toggle:hover { color: var(--quest-green-600); }
        .submit-btn {
            width: 100%; padding: 1rem;
            background: linear-gradient(135deg, var(--quest-green), var(--quest-yellow));
            color: #fff; font-weight: 700; font-size: 1rem; border: none;
            border-radius: 12px; cursor: pointer; transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(90, 172, 123, 0.4);
            position: relative; overflow: hidden;
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
        .submit-btn:active { transform: translateY(0); }
        .forgot-link {
            color: var(--quest-green-600); text-decoration: none;
            font-weight: 600; font-size: 0.875rem; transition: color 0.2s;
        }
        .forgot-link:hover { color: var(--quest-green); text-decoration: underline; }
        .divider {
            display: flex; align-items: center; gap: 1rem;
            margin: 1.5rem 0; color: var(--slate-400); font-size: 0.875rem;
        }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--slate-200); }
        .back-link {
            display: inline-flex; align-items: center; gap: 0.5rem;
            color: var(--slate-500); text-decoration: none;
            font-size: 0.875rem; font-weight: 500; transition: color 0.2s;
        }
        .back-link:hover { color: var(--slate-700); }
        .mb-3 { margin-bottom: 1.25rem !important; }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape"></div><div class="shape"></div><div class="shape"></div><div class="shape"></div>
    </div>
    <?php
    if (isset($_SESSION['error'])) {
    ?><script>toastr.error("<?php echo htmlspecialchars($_SESSION["error"], ENT_QUOTES, 'UTF-8') ?>", "Error!");</script><?php
        unset($_SESSION['error']);
    } else if (isset($_SESSION["success"])) {
    ?><script>toastr.success("<?php echo htmlspecialchars($_SESSION["success"], ENT_QUOTES, 'UTF-8') ?>", "Success!");</script><?php
        unset($_SESSION["success"]);
    }
    ?>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <img src="assets/images/quest.jpg" alt="Quest Portal Logo" class="login-logo">
                <h1 class="login-title">Welcome Back!</h1>
                <p class="login-subtitle">Sign in to access your student portal</p>
            </div>
            <form action="login_handler.inc.php" id="loginForm" method="post" enctype="multipart/form-data">
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-sign-in-alt"></i> Login Credentials
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope" style="color: var(--quest-green);"></i> Email Address
                        </label>
                        <input type="email" class="form-control-custom" placeholder="Enter your email address" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock" style="color: var(--quest-green);"></i> Password
                        </label>
                        <div class="input-group-custom">
                            <input type="password" class="form-control-custom" placeholder="Enter your password" id="password" name="password" style="border: none;">
                            <button class="password-toggle" type="button" id="passwordToggle"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="forgot_password.php" class="forgot-link"><i class="fas fa-key me-1"></i> Forgot password?</a>
                </div>
                <button type="submit" class="submit-btn"><i class="fas fa-sign-in-alt me-2"></i> Log In</button>
            </form>
            <div class="divider">or</div>
            <div class="text-center">
                <a href="./" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
    <script>
        const password = document.querySelector("#password");
        const passwordToggle = document.querySelector("#passwordToggle");
        passwordToggle.addEventListener("click", function() {
            if (password.type == "password") {
                password.type = "text"; passwordToggle.innerHTML = "<i class='fas fa-eye-slash'></i>";
            } else {
                password.type = "password"; passwordToggle.innerHTML = "<i class='fas fa-eye'></i>";
            }
        });
        document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
    </script>
</body>
</html>

