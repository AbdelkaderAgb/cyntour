<?php
/**
 * CynTour - Login Page
 * 
 * Clean, secure user authentication system
 */

ob_start();
session_start();

require_once "config.php";
require_once "helpers.php";

// Redirect if already logged in
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    safe_redirect('index.php');
}

$errors = [];
$success = '';

// Check for registration success message
if (isset($_SESSION['registration_success'])) {
    $success = "Registration successful! Please log in with your credentials.";
    unset($_SESSION['registration_success']);
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);
    
    // Validate inputs
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    
    // Attempt login if no validation errors
    if (empty($errors)) {
        try {
            $conn = getMysqliConnection();
            
            $stmt = $conn->prepare("SELECT id, company_name, first_name, last_name, email, password, role, status FROM users WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            
            if ($user && password_verify($password, $user['password'])) {
                // Check if account is active
                if ($user['status'] !== 'active') {
                    $errors[] = "Your account is not active. Please contact support.";
                } else {
                    // Regenerate session ID to prevent fixation
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['auth'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user'] = $user;
                    $_SESSION['username'] = $user['first_name'] ?? $user['email'];
                    $_SESSION['user_role'] = $user['role'] ?? 'user';
                    
                    // Handle remember me
                    if ($remember_me) {
                        $token = bin2hex(random_bytes(32));
                        $token_hash = hash('sha256', $token);
                        
                        $update_stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                        $update_stmt->bind_param("si", $token_hash, $user['id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                        
                        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
                        setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), '/', '', $secure, true);
                    }
                    
                    // Redirect based on role
                    $redirect_url = ($user['role'] === 'admin') ? 'admin.php' : 'index.php';
                    session_write_close();
                    safe_redirect($redirect_url);
                }
            } else {
                $errors[] = "Invalid email or password.";
            }
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            $errors[] = "An error occurred. Please try again later.";
        }
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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/cyntour-style.css" rel="stylesheet">
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            padding: var(--spacing-lg);
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            pointer-events: none;
        }
        
        .login-container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: var(--white);
            border-radius: var(--radius-2xl);
            box-shadow: 0 30px 60px rgba(0,0,0,0.25);
            overflow: hidden;
            animation: fadeInUp 0.6s ease forwards;
            position: relative;
            z-index: 1;
        }
        
        .login-image {
            flex: 1;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            min-height: 550px;
            position: relative;
            display: none;
        }
        
        .login-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
        }
        
        .login-image-content {
            position: relative;
            z-index: 2;
            padding: var(--spacing-3xl);
            color: var(--white);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-image-content h2 {
            font-size: 2.25rem;
            color: var(--white);
            margin-bottom: var(--spacing-lg);
            line-height: 1.3;
        }
        
        .login-image-content h2 span {
            color: var(--primary-light);
            font-style: italic;
        }
        
        .login-image-content p {
            color: rgba(255,255,255,0.85);
            font-size: 1.05rem;
            line-height: 1.7;
        }
        
        .login-form-container {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }
        
        .login-logo-text {
            font-family: var(--font-heading);
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary);
        }
        
        .login-logo-text span {
            color: var(--primary);
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
            margin-bottom: 1.25rem;
        }
        
        .password-toggle {
            position: relative;
        }
        
        .password-toggle .toggle-btn {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            padding: var(--spacing-sm);
            transition: color var(--transition-fast);
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
        
        .feature-list {
            margin-top: var(--spacing-xl);
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-md);
        }
        
        .feature-item i {
            color: var(--primary-light);
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
        <div class="login-image">
            <div class="login-image-content">
                <h2>Welcome to <span>CynTour</span></h2>
                <p>Your gateway to unforgettable Turkish experiences. Sign in to access exclusive deals, manage your bookings, and explore our premium travel services.</p>
                <div class="feature-list">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Exclusive hotel rates</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Priority booking for tours</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>24/7 customer support</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-form-container">
            <div class="login-logo">
                <a href="dashboard.php" style="text-decoration: none;">
                    <div class="login-logo-text">Cyn<span>Tour</span></div>
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
            
            <form class="login-form" action="" method="post" novalidate>
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
                           autocomplete="email"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
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
                        <button type="button" class="toggle-btn" onclick="togglePassword()" aria-label="Toggle password visibility">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <label for="remember_me">Remember me</label>
                    </div>
                    <a href="forgot-password.php" class="forgot-link">
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
    
    document.querySelector('.login-form').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        let hasError = false;
        
        // Clear previous errors
        document.querySelectorAll('.field-error').forEach(el => el.remove());
        
        if (!email) {
            showFieldError('email', 'Email is required');
            hasError = true;
        } else if (!isValidEmail(email)) {
            showFieldError('email', 'Please enter a valid email');
            hasError = true;
        }
        
        if (!password) {
            showFieldError('password', 'Password is required');
            hasError = true;
        }
        
        if (hasError) {
            e.preventDefault();
            return;
        }
        
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
        btn.disabled = true;
    });
    
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error cyn-form-error';
        errorDiv.textContent = message;
        field.closest('.cyn-form-group').appendChild(errorDiv);
    }
    </script>
</body>
</html>
