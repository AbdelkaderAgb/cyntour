<?php
/**
 * Shared Assets Configuration
 * Include this file to get CDN-based CSS and JS references
 * Usage: include 'includes/assets.php';
 */

// Function to output CSS assets in <head>
function outputCssAssets() {
?>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
<?php
}

// Function to output JS assets before </body>
function outputJsAssets() {
?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery Easing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<?php
}

// Shared CSS styles for consistent design
function outputSharedStyles() {
?>
<style>
    :root {
        --primary: #CA8C05;
        --primary-light: #FFD700;
        --primary-dark: #A06000;
        --secondary: #2A4D69;
        --success: #1cc88a;
        --info: #36b9cc;
        --warning: #f6c23e;
        --danger: #e74a3b;
        --light: #f8f9fc;
        --dark: #5a5c69;
        --gray: #6c757d;
        --border-radius: 12px;
        --box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        --transition: all 0.3s ease;
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f9f9f9;
        color: #333;
        line-height: 1.7;
    }
    
    h1, h2, h3, h4, h5, h6 {
        font-family: 'Playfair Display', serif;
        font-weight: 600;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }
    
    .btn-primary {
        background: linear-gradient(to right, var(--primary), var(--primary-dark));
        border: none;
        border-radius: 30px;
        padding: 10px 25px;
        font-weight: 500;
        transition: var(--transition);
    }
    
    .btn-primary:hover {
        background: linear-gradient(to right, var(--primary-dark), var(--primary));
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(202, 140, 5, 0.3);
    }
    
    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: var(--transition);
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        transition: var(--transition);
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(202, 140, 5, 0.25);
    }
    
    .form-control-user {
        border-radius: 50px;
        padding: 15px 20px;
    }
    
    /* Sidebar styling for admin pages */
    .sidebar {
        background: linear-gradient(180deg, var(--primary) 10%, var(--primary-dark) 100%);
        min-height: 100vh;
    }
    
    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 1rem;
        border-radius: 8px;
        margin: 0.25rem 0.5rem;
        transition: var(--transition);
    }
    
    .sidebar .nav-link:hover {
        color: white;
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .sidebar .nav-link.active {
        color: white;
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    /* Table styling */
    .table {
        border-radius: var(--border-radius);
        overflow: hidden;
    }
    
    .table thead th {
        background-color: var(--primary);
        color: white;
        font-weight: 500;
        border: none;
    }
    
    .table tbody tr {
        transition: var(--transition);
    }
    
    .table tbody tr:hover {
        background-color: rgba(202, 140, 5, 0.05);
    }
    
    /* Alert styling */
    .alert {
        border-radius: var(--border-radius);
        border: none;
    }
    
    /* Navbar styling */
    .navbar {
        background-color: #fff !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        padding: 15px 20px;
    }
    
    .navbar-brand img {
        height: 60px;
        transition: var(--transition);
    }
    
    .navbar-brand img:hover {
        transform: scale(1.05);
    }
    
    .nav-link {
        font-weight: 500;
        padding: 10px 15px !important;
        color: var(--dark) !important;
        border-bottom: 2px solid transparent;
        transition: var(--transition);
    }
    
    .nav-link:hover, .nav-link.active {
        color: var(--primary) !important;
        border-bottom: 2px solid var(--primary);
    }
    
    /* Footer styling */
    .footer, .sticky-footer {
        background-color: var(--primary);
        color: white;
        padding: 20px 0;
    }
    
    /* Scroll to top button */
    .scroll-to-top {
        position: fixed;
        right: 1rem;
        bottom: 1rem;
        width: 2.75rem;
        height: 2.75rem;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        opacity: 0;
        visibility: hidden;
    }
    
    .scroll-to-top.show {
        opacity: 1;
        visibility: visible;
    }
    
    .scroll-to-top:hover {
        background: var(--primary-dark);
        transform: translateY(-5px);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            z-index: 1000;
            transform: translateX(-100%);
            transition: var(--transition);
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
    }
</style>
<?php
}

// Common scroll to top functionality
function outputScrollToTopScript() {
?>
<script>
    $(document).ready(function() {
        // Scroll to top functionality
        $(window).scroll(function() {
            if ($(this).scrollTop() > 200) {
                $('.scroll-to-top').addClass('show');
            } else {
                $('.scroll-to-top').removeClass('show');
            }
        });
        
        $('.scroll-to-top').click(function(e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, 500);
        });
    });
</script>
<?php
}
?>
