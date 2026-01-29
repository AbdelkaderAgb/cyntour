<?php
/**
 * CynTour - Forgot Password Page
 * 
 * Password reset request form
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reset'])) {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    
    if (empty($errors)) {
        try {
            $conn = getDbConnection();
            
            $stmt = $conn->prepare("SELECT id, email, first_name FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store token (for demo purposes, we show success message)
                // In production, you would:
                // 1. Store the token in database with expiry
                // 2. Send email with reset link
                
                $success = "If an account exists with this email, you will receive password reset instructions shortly.";
            } else {
                // Don't reveal if email exists or not (security)
                $success = "If an account exists with this email, you will receive password reset instructions shortly.";
            }
        } catch (PDOException $e) {
            error_log('Password reset error: ' . $e->getMessage());
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
    <meta name="description" content="CynTour - Reset your password">
    <title>CynTour - Forgot Password</title>
    
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
        }
        
        .forgot-container {
            display: flex;
            max-width: 900px;
            width: 100%;
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            animation: fadeInUp 0.6s ease forwards;
        }
        
        .forgot-image {
            flex: 1;
            background: url('istanbul.jpeg') center/cover no-repeat;
            min-height: 400px;
            position: relative;
            display: none;
        }
        
        .forgot-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(42, 77, 105, 0.8) 0%, rgba(26, 51, 72, 0.9) 100%);
        }
        
        .forgot-image-content {
            position: relative;
            z-index: 2;
            padding: var(--spacing-2xl);
            color: var(--white);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .forgot-image-content h2 {
            font-size: 1.75rem;
            color: var(--white);
            margin-bottom: var(--spacing-md);
        }
        
        .forgot-image-content h2 span {
            color: var(--primary-light);
        }
        
        .forgot-image-content p {
            color: rgba(255,255,255,0.8);
            font-size: 0.95rem;
        }
        
        .forgot-form-container {
            flex: 1;
            padding: var(--spacing-3xl);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .forgot-logo {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .forgot-logo img {
            height: 70px;
        }
        
        .forgot-header {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .forgot-header h1 {
            font-size: 1.5rem;
            color: var(--secondary);
            margin-bottom: var(--spacing-sm);
        }
        
        .forgot-header p {
            color: var(--gray-600);
            margin-bottom: 0;
            font-size: 0.95rem;
        }
        
        .forgot-icon {
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }
        
        .forgot-icon i {
            font-size: 4rem;
            color: var(--primary);
            opacity: 0.8;
        }
        
        .forgot-form .cyn-form-group {
            margin-bottom: var(--spacing-lg);
        }
        
        .forgot-links {
            text-align: center;
            margin-top: var(--spacing-xl);
        }
        
        .forgot-links a {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin: var(--spacing-sm) var(--spacing-md);
            font-size: 0.95rem;
        }
        
        @media (min-width: 768px) {
            .forgot-image {
                display: block;
            }
        }
        
        @media (max-width: 767px) {
            .forgot-form-container {
                padding: var(--spacing-xl);
            }
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-image">
            <div class="forgot-image-content">
                <h2>Reset Your <span>Password</span></h2>
                <p>Don't worry, it happens to everyone. Enter your email address and we'll send you instructions to reset your password.</p>
            </div>
        </div>
        
        <div class="forgot-form-container">
            <div class="forgot-logo">
                <a href="home.php">
                    <img src="img/logo.png" alt="CynTour Logo">
                </a>
            </div>
            
            <div class="forgot-icon">
                <i class="fas fa-key"></i>
            </div>
            
            <div class="forgot-header">
                <h1>Forgot Your Password?</h1>
                <p>Enter your email address and we'll send you a link to reset your password.</p>
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
            
            <form class="forgot-form" action="" method="post" novalidate>
                <div class="cyn-form-group">
                    <label class="cyn-form-label" for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="cyn-form-control cyn-form-control-rounded" 
                           placeholder="Enter your registered email" 
                           required 
                           autocomplete="email"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <button type="submit" name="reset" class="cyn-btn cyn-btn-primary cyn-btn-lg cyn-btn-block">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
            
            <div class="forgot-links">
                <a href="login.php">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
                <br>
                <a href="register.php">
                    <i class="fas fa-user-plus"></i> Create New Account
                </a>
            </div>
        </div>
    </div>
    
    <script>
    document.querySelector('.forgot-form').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        
        // Clear previous errors
        document.querySelectorAll('.field-error').forEach(el => el.remove());
        
        if (!email) {
            e.preventDefault();
            showFieldError('email', 'Email is required');
            return;
        }
        
        if (!isValidEmail(email)) {
            e.preventDefault();
            showFieldError('email', 'Please enter a valid email');
            return;
        }
        
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
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
