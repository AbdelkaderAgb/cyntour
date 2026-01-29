<?php
/**
 * CynTour - Registration Page
 * 
 * Clean, secure user registration system
 */

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'helpers.php';

// Redirect if already logged in
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    safe_redirect('index.php');
}

$errors = [];
$formData = [
    'company_name' => '',
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone_number' => ''
];

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = getMysqliConnection();
    
    // Sanitize and validate inputs (prepared statements handle SQL injection)
    $formData = [
        'company_name' => trim($_POST['company_name'] ?? ''),
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone_number' => trim($_POST['phone_number'] ?? '')
    ];
    
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($formData['company_name'])) {
        $errors[] = "Company name is required";
    } elseif (strlen($formData['company_name']) > 255) {
        $errors[] = "Company name is too long";
    }
    
    if (empty($formData['first_name'])) {
        $errors[] = "First name is required";
    } elseif (strlen($formData['first_name']) > 100) {
        $errors[] = "First name is too long";
    }
    
    if (empty($formData['last_name'])) {
        $errors[] = "Last name is required";
    } elseif (strlen($formData['last_name']) > 100) {
        $errors[] = "Last name is too long";
    }
    
    if (empty($formData['email'])) {
        $errors[] = "Email is required";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($formData['phone_number'])) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Insert new user if no validation errors
    // The database has UNIQUE constraints on email and company_name
    // so we handle duplicates via the error response from INSERT
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (company_name, first_name, last_name, email, phone_number, password, role, status) VALUES (?, ?, ?, ?, ?, ?, 'user', 'active')");
        $stmt->bind_param("ssssss", 
            $formData['company_name'], 
            $formData['first_name'], 
            $formData['last_name'], 
            $formData['email'], 
            $formData['phone_number'], 
            $password_hash
        );
        
        if ($stmt->execute()) {
            $_SESSION['registration_success'] = true;
            $stmt->close();
            $conn->close();
            safe_redirect('login.php');
        } else {
            // Check for duplicate entry errors
            if ($conn->errno === 1062) { // Duplicate entry error code
                $error_msg = $conn->error;
                if (stripos($error_msg, 'email') !== false) {
                    $errors[] = "Email already exists";
                } elseif (stripos($error_msg, 'company_name') !== false) {
                    $errors[] = "Company name already exists";
                } else {
                    $errors[] = "This email or company name is already registered";
                }
            } else {
                error_log('Registration error: ' . $conn->error);
                $errors[] = "Registration failed. Please try again.";
            }
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
            
            <form class="register-form" action="" method="post" novalidate>
                <div class="cyn-form-group">
                    <label class="cyn-form-label" for="company_name">
                        <i class="fas fa-building"></i> Company Name
                    </label>
                    <input type="text" 
                           id="company_name" 
                           name="company_name" 
                           class="cyn-form-control" 
                           placeholder="Your company name" 
                           value="<?php echo htmlspecialchars($formData['company_name']); ?>"
                           required
                           maxlength="255">
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
                               value="<?php echo htmlspecialchars($formData['first_name']); ?>"
                               required
                               maxlength="100">
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
                               value="<?php echo htmlspecialchars($formData['last_name']); ?>"
                               required
                               maxlength="100">
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
                           value="<?php echo htmlspecialchars($formData['email']); ?>"
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
                           value="<?php echo htmlspecialchars($formData['phone_number']); ?>"
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
    
    // Form validation
    document.querySelector('.register-form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        const email = document.getElementById('email').value.trim();
        const companyName = document.getElementById('company_name').value.trim();
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const phone = document.getElementById('phone_number').value.trim();
        
        // Clear previous errors
        document.querySelectorAll('.field-error').forEach(el => el.remove());
        
        let hasError = false;
        
        if (!companyName) {
            showFieldError('company_name', 'Company name is required');
            hasError = true;
        }
        
        if (!firstName) {
            showFieldError('first_name', 'First name is required');
            hasError = true;
        }
        
        if (!lastName) {
            showFieldError('last_name', 'Last name is required');
            hasError = true;
        }
        
        if (!email) {
            showFieldError('email', 'Email is required');
            hasError = true;
        } else if (!isValidEmail(email)) {
            showFieldError('email', 'Please enter a valid email');
            hasError = true;
        }
        
        if (!phone) {
            showFieldError('phone_number', 'Phone number is required');
            hasError = true;
        }
        
        if (password.length < 8) {
            showFieldError('password', 'Password must be at least 8 characters');
            hasError = true;
        }
        
        if (password !== confirm) {
            showFieldError('confirm_password', 'Passwords do not match');
            hasError = true;
        }
        
        if (hasError) {
            e.preventDefault();
            return;
        }
        
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
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
