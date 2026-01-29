<?php
/**
 * CynTour - Unified Registration System
 * 
 * Handles new user registration with custom design (no Bootstrap)
 */

// Start output buffering to ensure headers can be sent
ob_start();

// Start session at the top to avoid "headers already sent" issues
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'helpers.php';

$errors = array();
$formData = [];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getMysqliConnection();

    // Validate and sanitize input fields
    $formData = [
        'company_name' => trim(mysqli_real_escape_string($conn, $_POST['company_name'])),
        'first_name' => trim(mysqli_real_escape_string($conn, $_POST['first_name'])),
        'last_name' => trim(mysqli_real_escape_string($conn, $_POST['last_name'])),
        'email' => trim(mysqli_real_escape_string($conn, $_POST['email'])),
        'phone_number' => trim(mysqli_real_escape_string($conn, $_POST['phone_number']))
    ];
    
    $userPassword = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($formData['company_name'])) { 
        array_push($errors, "Company name is required"); 
    }
    if (empty($formData['first_name'])) { 
        array_push($errors, "First name is required"); 
    }
    if (empty($formData['last_name'])) { 
        array_push($errors, "Last name is required"); 
    }
    if (empty($formData['email'])) { 
        array_push($errors, "Email is required"); 
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) { 
        array_push($errors, "Invalid email format"); 
    }
    if (empty($formData['phone_number'])) { 
        array_push($errors, "Phone number is required"); 
    }
    if (empty($userPassword)) { 
        array_push($errors, "Password is required"); 
    } elseif (strlen($userPassword) < 8) { 
        array_push($errors, "Password must be at least 8 characters"); 
    }
    if ($userPassword !== $confirm_password) { 
        array_push($errors, "Passwords do not match"); 
    }

    // Check if email or company name already exist
    $check_stmt = $conn->prepare("SELECT email, company_name FROM users WHERE email = ? OR company_name = ?");
    $check_stmt->bind_param("ss", $formData['email'], $formData['company_name']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $user = $check_result->fetch_assoc();
        if ($user['email'] === $formData['email']) { 
            array_push($errors, "Email already exists"); 
        }
        if ($user['company_name'] === $formData['company_name']) { 
            array_push($errors, "Company name already exists"); 
        }
    }
    
    $check_stmt->close();

    // If no errors, insert into database
    if (count($errors) == 0) {
        $password_hash = password_hash($userPassword, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (company_name, first_name, last_name, email, phone_number, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $formData['company_name'], $formData['first_name'], $formData['last_name'], $formData['email'], $formData['phone_number'], $password_hash);

        if ($stmt->execute()) {
            $_SESSION['registration_success'] = true;
            safe_redirect('login.php');
        } else {
            array_push($errors, "Registration failed: " . $stmt->error);
        }

        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CynTour - Create your account">
    <title>CynTour - Register</title>
    
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
            padding: var(--spacing-xl);
        }
        
        .register-container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            animation: fadeInUp 0.6s ease forwards;
        }
        
        .register-image {
            flex: 0 0 380px;
            background: url('istanbul.jpeg') center/cover no-repeat;
            position: relative;
            display: none;
        }
        
        .register-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(42, 77, 105, 0.85) 0%, rgba(26, 51, 72, 0.9) 100%);
        }
        
        .register-image-content {
            position: relative;
            z-index: 2;
            padding: var(--spacing-2xl);
            color: var(--white);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .register-image-content h2 {
            font-size: 1.75rem;
            color: var(--white);
            margin-bottom: var(--spacing-md);
        }
        
        .register-image-content h2 span {
            color: var(--primary-light);
        }
        
        .register-image-content p {
            color: rgba(255,255,255,0.8);
            font-size: 0.95rem;
        }
        
        .benefit-list {
            list-style: none;
            margin-top: var(--spacing-xl);
        }
        
        .benefit-list li {
            display: flex;
            align-items: flex-start;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-md);
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
        }
        
        .benefit-list i {
            color: var(--primary-light);
            margin-top: 3px;
        }
        
        .register-form-container {
            flex: 1;
            padding: var(--spacing-2xl);
            overflow-y: auto;
            max-height: 95vh;
        }
        
        .register-logo {
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }
        
        .register-logo img {
            height: 60px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .register-header h1 {
            font-size: 1.5rem;
            color: var(--secondary);
            margin-bottom: var(--spacing-xs);
        }
        
        .register-header p {
            color: var(--gray-600);
            margin-bottom: 0;
            font-size: 0.95rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-md);
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
        
        .strength-meter {
            height: 4px;
            background: var(--gray-200);
            border-radius: var(--radius-full);
            margin-top: var(--spacing-sm);
            overflow: hidden;
        }
        
        .strength-meter-bar {
            height: 100%;
            border-radius: var(--radius-full);
            transition: width var(--transition-normal), background-color var(--transition-normal);
            width: 0;
        }
        
        .strength-text {
            font-size: 0.8rem;
            margin-top: var(--spacing-xs);
        }
        
        .register-divider {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            margin: var(--spacing-lg) 0;
            color: var(--gray-500);
            font-size: 0.9rem;
        }
        
        .register-divider::before,
        .register-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-300);
        }
        
        .register-links {
            text-align: center;
        }
        
        .back-to-home {
            text-align: center;
            margin-top: var(--spacing-lg);
        }
        
        .back-to-home a {
            color: var(--gray-500);
            font-size: 0.9rem;
        }
        
        @media (min-width: 992px) {
            .register-image {
                display: block;
            }
        }
        
        @media (max-width: 767px) {
            .register-form-container {
                padding: var(--spacing-xl);
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Image Side -->
        <div class="register-image">
            <div class="register-image-content">
                <h2>Join <span>CynTour</span></h2>
                <p>Create your account to access our premium travel services and exclusive deals.</p>
                
                <ul class="benefit-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Exclusive access to special hotel rates</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Priority booking for popular tours</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Personalized travel recommendations</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Dedicated 24/7 support team</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Manage all bookings in one place</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Form Side -->
        <div class="register-form-container">
            <div class="register-logo">
                <a href="home.php">
                    <img src="img/logo.png" alt="CynTour Logo">
                </a>
            </div>
            
            <div class="register-header">
                <h1>Create Your Account</h1>
                <p>Join our platform and start your Turkish adventure</p>
            </div>
            
            <?php if (count($errors) > 0): ?>
            <div class="cyn-alert cyn-alert-danger animate-fadeIn">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php foreach ($errors as $error): ?>
                    <p style="margin: 0;"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <form class="register-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="cyn-form-group">
                    <label class="cyn-form-label" for="company_name">
                        <i class="fas fa-building"></i> Company Name
                    </label>
                    <input type="text" 
                           id="company_name" 
                           name="company_name" 
                           class="cyn-form-control" 
                           placeholder="Your company name" 
                           value="<?php echo isset($formData['company_name']) ? htmlspecialchars($formData['company_name']) : ''; ?>"
                           required>
                </div>
                
                <div class="form-row">
                    <div class="cyn-form-group">
                        <label class="cyn-form-label" for="first_name">
                            <i class="fas fa-user"></i> First Name
                        </label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               class="cyn-form-control" 
                               placeholder="First name" 
                               value="<?php echo isset($formData['first_name']) ? htmlspecialchars($formData['first_name']) : ''; ?>"
                               required>
                    </div>
                    
                    <div class="cyn-form-group">
                        <label class="cyn-form-label" for="last_name">
                            <i class="fas fa-user"></i> Last Name
                        </label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               class="cyn-form-control" 
                               placeholder="Last name" 
                               value="<?php echo isset($formData['last_name']) ? htmlspecialchars($formData['last_name']) : ''; ?>"
                               required>
                    </div>
                </div>
                
                <div class="cyn-form-group">
                    <label class="cyn-form-label" for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="cyn-form-control" 
                           placeholder="your@email.com" 
                           value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>"
                           required>
                </div>
                
                <div class="cyn-form-group">
                    <label class="cyn-form-label" for="phone_number">
                        <i class="fas fa-phone"></i> Phone Number
                    </label>
                    <input type="tel" 
                           id="phone_number" 
                           name="phone_number" 
                           class="cyn-form-control" 
                           placeholder="+90 XXX XXX XX XX" 
                           value="<?php echo isset($formData['phone_number']) ? htmlspecialchars($formData['phone_number']) : ''; ?>"
                           required>
                </div>
                
                <div class="form-row">
                    <div class="cyn-form-group">
                        <label class="cyn-form-label" for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="password-toggle">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="cyn-form-control" 
                                   placeholder="Min 8 characters" 
                                   required
                                   minlength="8">
                            <button type="button" class="toggle-btn" onclick="togglePassword('password', 'toggleIcon1')" aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-meter-bar" id="strengthBar"></div>
                        </div>
                        <p class="strength-text" id="strengthText"></p>
                    </div>
                    
                    <div class="cyn-form-group">
                        <label class="cyn-form-label" for="confirm_password">
                            <i class="fas fa-lock"></i> Confirm Password
                        </label>
                        <div class="password-toggle">
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   class="cyn-form-control" 
                                   placeholder="Repeat password" 
                                   required>
                            <button type="button" class="toggle-btn" onclick="togglePassword('confirm_password', 'toggleIcon2')" aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="register" class="cyn-btn cyn-btn-primary cyn-btn-lg cyn-btn-block" style="margin-top: var(--spacing-md);">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="register-divider">or</div>
            
            <div class="register-links">
                <span style="color: var(--gray-600);">Already have an account?</span>
                <a href="login.php" class="cyn-btn cyn-btn-outline" style="margin-left: var(--spacing-sm);">
                    <i class="fas fa-sign-in-alt"></i> Sign In
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
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Password strength meter
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        let strength = 0;
        let text = '';
        let color = '';
        
        if (password.length === 0) {
            strengthBar.style.width = '0%';
            strengthText.textContent = '';
            return;
        }
        
        if (password.length >= 8) strength += 25;
        if (password.match(/[A-Z]/)) strength += 25;
        if (password.match(/[0-9]/)) strength += 25;
        if (password.match(/[^A-Za-z0-9]/)) strength += 25;
        
        if (strength <= 25) {
            text = 'Weak - Add more characters';
            color = 'var(--danger)';
        } else if (strength <= 50) {
            text = 'Fair - Add uppercase, numbers, symbols';
            color = 'var(--warning)';
        } else if (strength <= 75) {
            text = 'Good - Almost there!';
            color = 'var(--info)';
        } else {
            text = 'Strong password!';
            color = 'var(--success)';
        }
        
        strengthBar.style.width = strength + '%';
        strengthBar.style.backgroundColor = color;
        strengthText.textContent = text;
        strengthText.style.color = color;
    });
    
    // Form submission
    document.querySelector('.register-form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        
        // Clear previous errors
        document.querySelectorAll('.cyn-form-error').forEach(el => el.remove());
        
        let hasError = false;
        
        if (password !== confirm) {
            e.preventDefault();
            const confirmInput = document.getElementById('confirm_password');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'cyn-form-error';
            errorDiv.textContent = 'Passwords do not match';
            confirmInput.parentElement.parentElement.appendChild(errorDiv);
            hasError = true;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            const passwordInput = document.getElementById('password');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'cyn-form-error';
            errorDiv.textContent = 'Password must be at least 8 characters';
            passwordInput.closest('.cyn-form-group').appendChild(errorDiv);
            hasError = true;
        }
        
        if (!hasError) {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
            btn.disabled = true;
        }
    });
    </script>
</body>
</html>