<?php
/**
 * CynTour - Unified Login System
 * 
 * Handles user authentication with custom design (no Bootstrap)
 */

session_start();
require_once "config.php";

$errors = [];
$success = '';

// Check for registration success message
if (isset($_SESSION['registration_success'])) {
    $success = "Registration successful! Please log in with your credentials.";
    unset($_SESSION['registration_success']);
}

// Redirect if already logged in
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    header("Location: home.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    try {
        $email = trim($_POST['email']);
        $userPassword = $_POST['password'];
        $rememberMe = isset($_POST['remember_me']);
        
        // Get database connection
        $conn = getDbConnection();
        
        // Attempt to find the user in the 'users' table
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($userPassword, $user['password'])) {
            $_SESSION['user'] = $user;
            $_SESSION['auth'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['first_name'] ?? $user['email'];
            $_SESSION['user_role'] = $user['role'] ?? 'user';
            
            // Handle remember me functionality
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                
                // Store token in database
                $mysqli = getMysqliConnection();
                $updateStmt = $mysqli->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $updateStmt->bind_param("si", $token, $user['id']);
                $updateStmt->execute();
                $updateStmt->close();
                
                // Set cookie for 30 days
                setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
            }
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $errors[] = "Invalid email or password.";
        }
    } catch(PDOException $e) {
        error_log('Login error: ' . $e->getMessage());
        $errors[] = "An error occurred. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CynTour - Login to your account">
    <title>CynTour - Login</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="css/cyntour-style.css" rel="stylesheet">
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            padding: var(--spacing-lg);
        }
        
        .login-container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            animation: fadeInUp 0.6s ease forwards;
        }
        
        .login-image {
            flex: 1;
            background: url('istanbul.jpeg') center/cover no-repeat;
            min-height: 500px;
            position: relative;
            display: none;
        }
        
        .login-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(42, 77, 105, 0.8) 0%, rgba(26, 51, 72, 0.9) 100%);
        }
        
        .login-image-content {
            position: relative;
            z-index: 2;
            padding: var(--spacing-2xl);
            color: var(--white);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-image-content h2 {
            font-size: 2rem;
            color: var(--white);
            margin-bottom: var(--spacing-md);
        }
        
        .login-image-content h2 span {
            color: var(--primary-light);
        }
        
        .login-image-content p {
            color: rgba(255,255,255,0.8);
            font-size: 1rem;
        }
        
        .login-form-container {
            flex: 1;
            padding: var(--spacing-3xl);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .login-logo img {
            height: 70px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .login-header h1 {
            font-size: 1.75rem;
            color: var(--secondary);
            margin-bottom: var(--spacing-sm);
        }
        
        .login-header p {
            color: var(--gray-600);
            margin-bottom: 0;
        }
        
        .login-form .cyn-form-group {
            margin-bottom: var(--spacing-lg);
        }
        
        .password-toggle {
            position: relative;
        }
        
        .password-toggle .toggle-btn {
            position: absolute;
            right: var(--spacing-lg);
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            padding: var(--spacing-sm);
        }
        
        .password-toggle .toggle-btn:hover {
            color: var(--primary);
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-lg);
            flex-wrap: wrap;
            gap: var(--spacing-sm);
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }
        
        .remember-me input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }
        
        .remember-me label {
            font-size: 0.95rem;
            color: var(--gray-700);
            cursor: pointer;
        }
        
        .forgot-link {
            font-size: 0.95rem;
        }
        
        .login-divider {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            margin: var(--spacing-xl) 0;
            color: var(--gray-500);
            font-size: 0.9rem;
        }
        
        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-300);
        }
        
        .login-links {
            text-align: center;
        }
        
        .login-links a {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin: var(--spacing-sm) var(--spacing-md);
        }
        
        .back-to-home {
            text-align: center;
            margin-top: var(--spacing-xl);
        }
        
        .back-to-home a {
            color: var(--gray-500);
            font-size: 0.9rem;
        }
        
        .back-to-home a:hover {
            color: var(--primary);
        }
        
        @media (min-width: 768px) {
            .login-image {
                display: block;
            }
        }
        
        @media (max-width: 767px) {
            .login-form-container {
                padding: var(--spacing-xl);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Image Side -->
        <div class="login-image">
            <div class="login-image-content">
                <h2>Welcome to <span>CynTour</span></h2>
                <p>Your gateway to unforgettable Turkish experiences. Sign in to access exclusive deals, manage your bookings, and explore our premium travel services.</p>
                <div style="margin-top: var(--spacing-xl);">
                    <div style="display: flex; align-items: center; gap: var(--spacing-sm); margin-bottom: var(--spacing-md);">
                        <i class="fas fa-check-circle" style="color: var(--primary-light);"></i>
                        <span>Exclusive hotel rates</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--spacing-sm); margin-bottom: var(--spacing-md);">
                        <i class="fas fa-check-circle" style="color: var(--primary-light);"></i>
                        <span>Priority booking for tours</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--spacing-sm);">
                        <i class="fas fa-check-circle" style="color: var(--primary-light);"></i>
                        <span>24/7 customer support</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Side -->
        <div class="login-form-container">
            <div class="login-logo">
                <a href="home.php">
                    <img src="img/logo.png" alt="CynTour Logo">
                </a>
            </div>
            
            <div class="login-header">
                <h1>Welcome Back!</h1>
                <p>Sign in to your account to continue</p>
            </div>
            
            <?php if (!empty($success)): ?>
            <div class="cyn-alert cyn-alert-success animate-fadeIn">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
            <div class="cyn-alert cyn-alert-danger animate-fadeIn">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php foreach ($errors as $error): ?>
                    <p style="margin: 0;"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <form class="login-form" action="" method="post">
                <div class="cyn-form-group">
                    <label class="cyn-form-label" for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="cyn-form-control cyn-form-control-rounded" 
                           placeholder="Enter your email" 
                           required 
                           autocomplete="email">
                </div>
                
                <div class="cyn-form-group">
                    <label class="cyn-form-label" for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="password-toggle">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="cyn-form-control cyn-form-control-rounded" 
                               placeholder="Enter your password" 
                               required 
                               autocomplete="current-password">
                        <button type="button" class="toggle-btn" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <label for="remember_me">Remember me</label>
                    </div>
                    <a href="forgot-password.html" class="forgot-link">
                        <i class="fas fa-key"></i> Forgot Password?
                    </a>
                </div>
                
                <button type="submit" name="login" class="cyn-btn cyn-btn-primary cyn-btn-lg cyn-btn-block">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <div class="login-divider">or</div>
            
            <div class="login-links">
                <span style="color: var(--gray-600);">Don't have an account?</span>
                <a href="register.php" class="cyn-btn cyn-btn-outline">
                    <i class="fas fa-user-plus"></i> Create Account
                </a>
            </div>
            
            <div class="back-to-home">
                <a href="home.php">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
    
    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    
    // Form submission loading state
    document.querySelector('.login-form').addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
        btn.disabled = true;
    });
    </script>
</body>
</html>