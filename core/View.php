<?php
/**
 * CynTour View Renderer
 * 
 * Handles rendering of views and components.
 * 
 * @package CynTour
 * @version 2.0
 */

namespace CynTour\Core;

class View
{
    /**
     * Render the HTML head section
     * 
     * @param string $title Page title
     * @param string $additionalCss Additional CSS styles
     */
    public static function renderHead(string $title = 'CynTour - Premium Travel Services', string $additionalCss = ''): void
    {
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CynTour - Premium travel services in Turkey. Hotels, tours, transfers and more.">
        <meta name="author" content="CYN Turizm">
        <title><?php echo htmlspecialchars($title); ?></title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        
        <!-- Custom CynTour Styles -->
        <link href="assets/css/cyntour-style.css" rel="stylesheet">
        
        <?php if ($additionalCss): ?>
        <style><?php echo $additionalCss; ?></style>
        <?php endif;
    }
    
    /**
     * Render the main navigation bar
     * 
     * @param bool $transparent Whether to use transparent navbar
     */
    public static function renderNavbar(bool $transparent = false): void
    {
        $app = Application::getInstance();
        $currentPage = Helper::getCurrentPage();
        $navClass = $transparent ? 'cyn-navbar cyn-navbar-transparent' : 'cyn-navbar';
        ?>
        <nav class="<?php echo $navClass; ?>" id="mainNav">
            <div class="cyn-navbar-container">
                <a class="cyn-navbar-brand" href="home.php">
                    <img src="assets/images/logo.png" alt="CynTour Logo">
                </a>
                
                <button class="cyn-navbar-toggle" id="navToggle" aria-label="Toggle navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <ul class="cyn-navbar-nav" id="navMenu">
                    <li><a class="cyn-nav-link <?php echo Helper::isActivePage('home.php'); ?>" href="home.php">
                        <i class="fas fa-home"></i> Home
                    </a></li>
                    <li><a class="cyn-nav-link <?php echo Helper::isActivePage('hotels.php'); ?>" href="hotels.php">
                        <i class="fas fa-hotel"></i> Hotels
                    </a></li>
                    <li><a class="cyn-nav-link <?php echo Helper::isActivePage('tours.php'); ?>" href="tours.php">
                        <i class="fas fa-map-marked-alt"></i> Tours
                    </a></li>
                    <li><a class="cyn-nav-link <?php echo Helper::isActivePage('transfers.php'); ?>" href="transfers.php">
                        <i class="fas fa-car"></i> Transfer
                    </a></li>
                    <li><a class="cyn-nav-link <?php echo Helper::isActivePage('contact.php'); ?>" href="contact.php">
                        <i class="fas fa-envelope"></i> Contact
                    </a></li>
                    
                    <?php if ($app->isAuthenticated()): ?>
                        <?php if ($app->isAdmin()): ?>
                        <li><a class="cyn-nav-link <?php echo Helper::isActivePage('dashboard.php'); ?>" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a></li>
                        <?php endif; ?>
                        <li><a class="cyn-nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    <?php else: ?>
                        <li><a class="cyn-btn cyn-btn-primary cyn-btn-sm" href="login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
        <?php
    }
    
    /**
     * Render the dashboard sidebar
     */
    public static function renderSidebar(): void
    {
        $app = Application::getInstance();
        ?>
        <aside class="cyn-sidebar" id="sidebar">
            <div class="cyn-sidebar-brand">
                <a href="home.php">
                    <img src="assets/images/logo.png" alt="CynTour">
                </a>
            </div>
            
            <ul class="cyn-sidebar-nav">
                <li class="cyn-sidebar-heading">Main</li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('dashboard.php'); ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('hotels.php'); ?>" href="hotels.php">
                        <i class="fas fa-hotel"></i>
                        <span>Hotels</span>
                    </a>
                </li>
                
                <li class="cyn-sidebar-divider"></li>
                <li class="cyn-sidebar-heading">Vouchers</li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('hotel-vouchers.php'); ?>" href="hotel-vouchers.php">
                        <i class="fas fa-file-alt"></i>
                        <span>Hotel Vouchers</span>
                    </a>
                </li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('transfer-vouchers.php'); ?>" href="transfer-vouchers.php">
                        <i class="fas fa-car"></i>
                        <span>Transfer Vouchers</span>
                    </a>
                </li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('tour-vouchers.php'); ?>" href="tour-vouchers.php">
                        <i class="fas fa-map"></i>
                        <span>Tour Vouchers</span>
                    </a>
                </li>
                
                <li class="cyn-sidebar-divider"></li>
                <li class="cyn-sidebar-heading">Services</li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('tours.php'); ?>" href="tours.php">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Tours</span>
                    </a>
                </li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('transfers.php'); ?>" href="transfers.php">
                        <i class="fas fa-shuttle-van"></i>
                        <span>Transfers</span>
                    </a>
                </li>
                
                <li class="cyn-sidebar-divider"></li>
                <li class="cyn-sidebar-heading">Finance</li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('invoices.php'); ?>" href="invoices.php">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Invoices</span>
                    </a>
                </li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('receipts.php'); ?>" href="receipts.php">
                        <i class="fas fa-receipt"></i>
                        <span>Receipts</span>
                    </a>
                </li>
                
                <?php if ($app->isAdmin()): ?>
                <li class="cyn-sidebar-divider"></li>
                <li class="cyn-sidebar-heading">Admin</li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('users.php'); ?>" href="users.php">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('import-data.php'); ?>" href="import-data.php">
                        <i class="fas fa-file-import"></i>
                        <span>Import Data</span>
                    </a>
                </li>
                
                <li class="cyn-sidebar-nav-item">
                    <a class="cyn-sidebar-nav-link <?php echo Helper::isActivePage('backup.php'); ?>" href="backup.php">
                        <i class="fas fa-database"></i>
                        <span>Backup</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </aside>
        <?php
    }
    
    /**
     * Render the dashboard header
     * 
     * @param string $pageTitle Page title
     */
    public static function renderDashboardHeader(string $pageTitle = 'Dashboard'): void
    {
        $app = Application::getInstance();
        ?>
        <header class="cyn-dashboard-header">
            <div class="d-flex align-items-center gap-3">
                <button class="cyn-btn cyn-btn-light cyn-btn-icon" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 style="font-size: 1.5rem; margin: 0; color: var(--secondary);"><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <a href="home.php" class="cyn-btn cyn-btn-outline cyn-btn-sm">
                    <i class="fas fa-globe"></i> View Site
                </a>
                <div class="d-flex align-items-center gap-2">
                    <div class="cyn-stat-icon" style="width: 40px; height: 40px; font-size: 1rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--gray-800);"><?php echo $app->getDisplayName(); ?></div>
                        <a href="logout.php" style="font-size: 0.8rem;">Logout</a>
                    </div>
                </div>
            </div>
        </header>
        <?php
    }
    
    /**
     * Render the footer
     */
    public static function renderFooter(): void
    {
        ?>
        <footer class="cyn-footer">
            <div class="cyn-container">
                <div class="cyn-footer-content">
                    <!-- Brand Section -->
                    <div class="cyn-footer-brand">
                        <img src="assets/images/logo.png" alt="CynTour">
                        <p class="cyn-footer-text">Your trusted partner for premium travel services in Turkey since 2006. We provide unforgettable experiences with the highest quality standards.</p>
                        <div style="margin-top: var(--spacing-md);">
                            <img src="assets/images/tursab-seeklogo-removebg.png" alt="TURSAB" style="height: 40px;">
                            <p style="color: rgba(255,255,255,0.5); font-size: 0.8rem; margin-top: var(--spacing-xs);">License No: 11738</p>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div>
                        <h4 class="cyn-footer-title">Quick Links</h4>
                        <ul class="cyn-footer-links">
                            <li><a href="home.php"><i class="fas fa-angle-right"></i> Home</a></li>
                            <li><a href="hotels.php"><i class="fas fa-angle-right"></i> Hotels</a></li>
                            <li><a href="tours.php"><i class="fas fa-angle-right"></i> Tours</a></li>
                            <li><a href="transfers.php"><i class="fas fa-angle-right"></i> Transfer</a></li>
                            <li><a href="contact.php"><i class="fas fa-angle-right"></i> Contact</a></li>
                        </ul>
                    </div>
                    
                    <!-- Services -->
                    <div>
                        <h4 class="cyn-footer-title">Services</h4>
                        <ul class="cyn-footer-links">
                            <li><a href="hotels.php"><i class="fas fa-angle-right"></i> Hotel Booking</a></li>
                            <li><a href="tours.php"><i class="fas fa-angle-right"></i> City Tours</a></li>
                            <li><a href="transfers.php"><i class="fas fa-angle-right"></i> Airport Transfer</a></li>
                            <li><a href="tours.php"><i class="fas fa-angle-right"></i> Day Trips</a></li>
                        </ul>
                    </div>
                    
                    <!-- Contact -->
                    <div>
                        <h4 class="cyn-footer-title">Contact Us</h4>
                        <ul class="cyn-footer-links">
                            <li>
                                <a href="tel:+905318176770">
                                    <i class="fas fa-phone-alt" style="color: var(--primary); margin-right: var(--spacing-sm);"></i>
                                    +90 531 817 67 70
                                </a>
                            </li>
                            <li>
                                <a href="mailto:info@cyntour.com">
                                    <i class="fas fa-envelope" style="color: var(--primary); margin-right: var(--spacing-sm);"></i>
                                    info@cyntour.com
                                </a>
                            </li>
                            <li>
                                <a href="mailto:sales@cyntourim.com">
                                    <i class="fas fa-envelope" style="color: var(--primary); margin-right: var(--spacing-sm);"></i>
                                    sales@cyntourim.com
                                </a>
                            </li>
                            <li>
                                <span style="display: flex; align-items: flex-start; gap: var(--spacing-sm);">
                                    <i class="fas fa-map-marker-alt" style="color: var(--primary); margin-top: 4px;"></i>
                                    <span>Molla Gürani, Karakoyunlu Sokağı No:2 D:4, 34093 Fatih/İstanbul</span>
                                </span>
                            </li>
                        </ul>
                        <div class="cyn-social-links" style="margin-top: var(--spacing-md);">
                            <a href="https://www.instagram.com/cyn__turizm/" target="_blank" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" title="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/905318176770" target="_blank" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="cyn-footer-bottom">
                    <p>&copy; <?php echo date('Y'); ?> CYN TURIZM. All Rights Reserved.</p>
                    <p>Established 2006 | Istanbul, Turkey</p>
                </div>
            </div>
        </footer>
        
        <!-- Back to Top Button -->
        <button class="cyn-back-to-top" id="backToTop" aria-label="Back to top">
            <i class="fas fa-arrow-up"></i>
        </button>
        <?php
    }
    
    /**
     * Render common scripts
     */
    public static function renderScripts(): void
    {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Navigation Toggle
            const navToggle = document.getElementById('navToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (navToggle && navMenu) {
                navToggle.addEventListener('click', function() {
                    navToggle.classList.toggle('active');
                    navMenu.classList.toggle('active');
                });
            }
            
            // Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
            
            // Back to Top Button
            const backToTop = document.getElementById('backToTop');
            
            if (backToTop) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        backToTop.classList.add('visible');
                    } else {
                        backToTop.classList.remove('visible');
                    }
                });
                
                backToTop.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
            
            // Navbar Scroll Effect
            const navbar = document.getElementById('mainNav');
            if (navbar && navbar.classList.contains('cyn-navbar-transparent')) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                });
            }
        });
        </script>
        <?php
    }
}
