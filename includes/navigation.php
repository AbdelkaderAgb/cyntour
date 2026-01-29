<?php
/**
 * Shared Navigation Component
 * Include this file in your pages to get consistent navigation
 * Usage: include 'includes/navigation.php';
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine current page for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
}

// Function to check if user is admin
function isAdmin() {
    if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
        return true;
    }
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        return true;
    }
    return false;
}

// Function to get active class
function getActiveClass($page, $current) {
    return ($page === $current) ? 'active' : '';
}
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="img/logo.png" alt="CYN Tourism Logo" style="height: 60px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo getActiveClass('index.php', $current_page); ?>" href="index.php">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo getActiveClass('tours.php', $current_page); ?>" href="tours.php">
                        <i class="fas fa-map-marked-alt me-1"></i>Tours
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo getActiveClass('transfer.php', $current_page); ?>" href="transfer.php">
                        <i class="fas fa-car me-1"></i>Transfer
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo getActiveClass('contact.php', $current_page); ?>" href="contact.php">
                        <i class="fas fa-envelope me-1"></i>Contact
                    </a>
                </li>
                
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo getActiveClass('admin.php', $current_page); ?>" href="admin.php">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo isset($_SESSION['user']['first_name']) ? htmlspecialchars($_SESSION['user']['first_name']) : 'Account'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <?php if (isAdmin()): ?>
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="users.php"><i class="fas fa-users me-2"></i>Users</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo getActiveClass('login.php', $current_page); ?>" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo getActiveClass('register.php', $current_page); ?>" href="register.php">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
/* Navigation Styles */
.navbar {
    padding: 0.75rem 0;
    transition: all 0.3s ease;
}

.navbar-brand img {
    transition: transform 0.3s ease;
}

.navbar-brand img:hover {
    transform: scale(1.05);
}

.navbar-nav .nav-link {
    font-weight: 500;
    padding: 0.5rem 1rem;
    color: #333;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
}

.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
    color: #CA8C05;
    border-bottom-color: #CA8C05;
}

.navbar-nav .nav-link i {
    font-size: 0.9rem;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.dropdown-item {
    padding: 0.6rem 1.25rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #CA8C05;
}

@media (max-width: 991px) {
    .navbar-collapse {
        padding: 1rem 0;
    }
    
    .navbar-nav .nav-link {
        padding: 0.75rem 0;
        border-bottom: none;
    }
    
    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding-left: 1rem;
    }
}
</style>
