<?php
/**
 * CynTour - Unified Dashboard & Hotels Page
 * 
 * This page combines the hotel listings with the management dashboard.
 * It displays hotel listings and provides quick access to all management tools.
 */

// Include authentication and configuration
include 'auth.php';
require_once 'config.php';
require_once 'includes/components.php';

// Redirect to login page if user is not authenticated
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    // Try auto-login with cookie first
    if (isset($_COOKIE['remember_me'])) {
        $token = $_COOKIE['remember_me'];
        $conn = getMysqliConnection();
        
        $stmt = $conn->prepare("SELECT id, username, role, first_name, email FROM users WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $resultToken = $stmt->get_result();
        
        if ($resultToken->num_rows > 0) {
            $user = $resultToken->fetch_assoc();
            $_SESSION['auth'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'] ?? $user['first_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user'] = $user;
        } else {
            header("Location: login.php");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: login.php");
        exit();
    }
}

// Get user info
$user = $_SESSION['user'] ?? [];
$isAdmin = cyn_is_admin();

// Database connection
$conn = getMysqliConnection();

// Get current page number from query string (default is 1)
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$hotelsPerPage = 12;
$offset = ($page - 1) * $hotelsPerPage;

// Query to fetch hotel names with pagination using prepared statement
$sql = "SELECT DISTINCT hotel_name FROM pricing_data LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $hotelsPerPage);
$stmt->execute();
$result = $stmt->get_result();

// Query to get total number of hotels for pagination
$totalHotelsResult = $conn->query("SELECT COUNT(DISTINCT hotel_name) as total FROM pricing_data");
$totalHotels = 0;
$totalPages = 1;
if ($totalHotelsResult) {
    $totalHotelsRow = $totalHotelsResult->fetch_assoc();
    $totalHotels = $totalHotelsRow['total'];
    $totalPages = ceil($totalHotels / $hotelsPerPage);
    // Clamp page to valid range
    $page = min($page, max(1, $totalPages));
}

// Define dashboard cards
$dashboardCards = [
    ['icon' => 'fa-calendar-alt', 'title' => 'Transfer Calendar', 'desc' => 'Plan and manage transfer operations', 'link' => 'Calendar.php', 'color' => '#6366f1'],
    ['icon' => 'fa-calendar-alt', 'title' => 'Tour Calendar', 'desc' => 'View and manage city tours', 'link' => 'tour_calendar.php', 'color' => '#8b5cf6'],
    ['icon' => 'fa-building', 'title' => 'Hotel Calendar', 'desc' => 'Track hotel reservations', 'link' => 'cal.php', 'color' => '#ec4899'],
    ['icon' => 'fa-file-invoice-dollar', 'title' => 'Hotel Invoice', 'desc' => 'Create and manage hotel invoices', 'link' => 'invoice_form.php', 'color' => '#f59e0b'],
    ['icon' => 'fa-ticket-alt', 'title' => 'Hotel Voucher', 'desc' => 'Prepare hotel vouchers', 'link' => 'form.php', 'color' => '#10b981'],
    ['icon' => 'fa-exchange-alt', 'title' => 'Transfer Voucher', 'desc' => 'Manage transfer vouchers', 'link' => 'transfer-voucher-form.php', 'color' => '#3b82f6'],
    ['icon' => 'fa-file-invoice', 'title' => 'Transfer Invoice', 'desc' => 'Create transfer invoices', 'link' => 'transfer-invoice-form.php', 'color' => '#ef4444'],
    ['icon' => 'fa-receipt', 'title' => 'Receipt', 'desc' => 'Create and manage receipts', 'link' => 'receipt-form.php', 'color' => '#14b8a6'],
    ['icon' => 'fa-route', 'title' => 'Tour Voucher', 'desc' => 'Manage tour vouchers', 'link' => 'tour_voucher_form.php', 'color' => '#f97316'],
];

// Admin-only cards
if ($isAdmin) {
    $dashboardCards[] = ['icon' => 'fa-file-alt', 'title' => 'Letterhead', 'desc' => 'Create corporate documents', 'link' => 'letterhead.php', 'color' => '#6366f1'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php cyn_render_head('CynTour - Dashboard & Hotels'); ?>
    <style>
        /* Dashboard Page Styles */
        :root {
            --dash-primary: #6366f1;
            --dash-secondary: #4f46e5;
            --dash-accent: #818cf8;
        }
        
        .page-hero {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            padding: var(--spacing-2xl) var(--spacing-lg);
            color: var(--white);
        }
        
        .page-hero-content {
            max-width: var(--container-xl);
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-lg);
        }
        
        .welcome-text h1 {
            color: var(--white);
            font-size: 2rem;
            margin-bottom: var(--spacing-xs);
        }
        
        .welcome-text h1 span {
            color: var(--primary-light);
        }
        
        .welcome-text p {
            color: rgba(255,255,255,0.8);
            margin-bottom: 0;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            background: rgba(255,255,255,0.1);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--radius-full);
            backdrop-filter: blur(10px);
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            background: var(--primary-gradient);
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
        }
        
        .user-details {
            color: var(--white);
        }
        
        .user-details .name {
            font-weight: 600;
        }
        
        .user-details .role {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        /* Tab Navigation */
        .tab-nav {
            background: var(--white);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 70px;
            z-index: 100;
        }
        
        .tab-nav-container {
            max-width: var(--container-xl);
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
            display: flex;
            gap: var(--spacing-xs);
        }
        
        .tab-btn {
            padding: var(--spacing-md) var(--spacing-xl);
            border: none;
            background: none;
            font-family: var(--font-primary);
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--gray-600);
            cursor: pointer;
            position: relative;
            transition: var(--transition-fast);
        }
        
        .tab-btn::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary);
            transform: scaleX(0);
            transition: transform var(--transition-fast);
        }
        
        .tab-btn:hover {
            color: var(--primary);
        }
        
        .tab-btn.active {
            color: var(--primary);
        }
        
        .tab-btn.active::after {
            transform: scaleX(1);
        }
        
        .tab-btn i {
            margin-right: var(--spacing-sm);
        }
        
        /* Tab Content */
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Dashboard Cards Section */
        .dashboard-section {
            padding: var(--spacing-2xl) 0;
            background: var(--light);
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: var(--spacing-lg);
            max-width: var(--container-xl);
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
        }
        
        .dash-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-md);
            transition: var(--transition-normal);
            border: 1px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .dash-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-color, var(--primary));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform var(--transition-normal);
        }
        
        .dash-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: rgba(99, 102, 241, 0.2);
        }
        
        .dash-card:hover::before {
            transform: scaleX(1);
        }
        
        .dash-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--spacing-lg);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(129, 140, 248, 0.1));
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        
        .dash-icon i {
            font-size: 1.5rem;
            color: var(--card-color, var(--dash-primary));
        }
        
        .dash-card h3 {
            font-size: 1.2rem;
            color: var(--gray-900);
            margin-bottom: var(--spacing-sm);
            font-family: var(--font-primary);
            font-weight: 600;
        }
        
        .dash-card p {
            color: var(--gray-600);
            font-size: 0.9rem;
            margin-bottom: var(--spacing-lg);
        }
        
        .dash-link {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: var(--spacing-sm) var(--spacing-lg);
            background: var(--card-color, var(--dash-primary));
            color: var(--white);
            border-radius: var(--radius-lg);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition-fast);
        }
        
        .dash-link:hover {
            filter: brightness(1.1);
            transform: translateX(4px);
            color: var(--white);
        }
        
        /* Hotels Section */
        .hotels-section {
            padding: var(--spacing-2xl) 0;
            background: var(--light);
        }
        
        .hotels-header {
            max-width: var(--container-xl);
            margin: 0 auto var(--spacing-xl);
            padding: 0 var(--spacing-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }
        
        .hotels-header h2 {
            font-size: 1.75rem;
            color: var(--secondary);
        }
        
        .search-box {
            position: relative;
            width: 100%;
            max-width: 400px;
        }
        
        .search-box input {
            padding-right: 50px;
        }
        
        .search-box i {
            position: absolute;
            right: var(--spacing-lg);
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
        }
        
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: var(--spacing-lg);
            max-width: var(--container-xl);
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
        }
        
        .hotel-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            transition: var(--transition-normal);
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: var(--spacing-lg);
            border-left: 4px solid var(--primary);
        }
        
        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .hotel-icon {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(202, 140, 5, 0.1);
            border-radius: var(--radius-lg);
            margin-right: var(--spacing-md);
            flex-shrink: 0;
        }
        
        .hotel-icon i {
            font-size: 1.4rem;
            color: var(--primary);
        }
        
        .hotel-info {
            flex: 1;
            min-width: 0;
        }
        
        .hotel-name {
            font-family: var(--font-heading);
            font-size: 1rem;
            color: var(--secondary);
            margin-bottom: var(--spacing-xs);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .hotel-name a {
            color: inherit;
            text-decoration: none;
            transition: color var(--transition-fast);
        }
        
        .hotel-name a:hover {
            color: var(--primary);
        }
        
        .hotel-meta {
            font-size: 0.8rem;
            color: var(--gray-500);
        }
        
        .hotel-action {
            flex-shrink: 0;
            margin-left: var(--spacing-sm);
        }
        
        /* Stats Bar */
        .stats-bar {
            max-width: var(--container-xl);
            margin: 0 auto var(--spacing-lg);
            padding: var(--spacing-md) var(--spacing-lg);
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }
        
        .stats-info {
            color: var(--gray-600);
        }
        
        .stats-info strong {
            color: var(--primary);
        }
        
        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            gap: var(--spacing-sm);
            margin-top: var(--spacing-2xl);
            padding: 0 var(--spacing-lg);
            flex-wrap: wrap;
        }
        
        .pagination-link {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-full);
            background: var(--white);
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-fast);
            box-shadow: var(--shadow-sm);
        }
        
        .pagination-link:hover {
            background: var(--primary-light);
            color: var(--white);
        }
        
        .pagination-link.active {
            background: var(--primary);
            color: var(--white);
        }
        
        .empty-state {
            text-align: center;
            padding: var(--spacing-3xl);
            color: var(--gray-500);
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: var(--spacing-lg);
            color: var(--gray-300);
        }
        
        /* Responsive */
        @media (max-width: 767px) {
            .page-hero-content {
                flex-direction: column;
                text-align: center;
            }
            
            .hotels-grid, .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .hotel-card {
                flex-direction: column;
                text-align: center;
            }
            
            .hotel-icon {
                margin-right: 0;
                margin-bottom: var(--spacing-md);
            }
            
            .hotel-action {
                margin-left: 0;
                margin-top: var(--spacing-md);
            }
            
            .tab-nav-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .tab-btn {
                white-space: nowrap;
                padding: var(--spacing-md);
            }
        }
    </style>
</head>
<body>
    <?php cyn_render_navbar(); ?>
    
    <!-- Page Hero -->
    <section class="page-hero">
        <div class="page-hero-content">
            <div class="welcome-text">
                <h1>Welcome back, <span><?php echo cyn_get_display_name(); ?></span></h1>
                <p>Manage your hotels, vouchers, and documents from one place.</p>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr(cyn_get_display_name(), 0, 1)); ?>
                </div>
                <div class="user-details">
                    <div class="name"><?php echo !empty($user['email']) ? htmlspecialchars($user['email']) : cyn_get_display_name(); ?></div>
                    <div class="role"><?php echo $isAdmin ? 'Administrator' : 'User'; ?></div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Tab Navigation -->
    <div class="tab-nav">
        <div class="tab-nav-container">
            <button class="tab-btn active" data-tab="dashboard">
                <i class="fas fa-th-large"></i> Dashboard
            </button>
            <button class="tab-btn" data-tab="hotels">
                <i class="fas fa-hotel"></i> Hotels
            </button>
        </div>
    </div>
    
    <!-- Dashboard Tab -->
    <div class="tab-content active" id="dashboard">
        <section class="dashboard-section">
            <div class="dashboard-grid">
                <?php foreach ($dashboardCards as $card): ?>
                <div class="dash-card" style="--card-color: <?php echo $card['color']; ?>;">
                    <div class="dash-icon">
                        <i class="fas <?php echo $card['icon']; ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($card['title']); ?></h3>
                    <p><?php echo htmlspecialchars($card['desc']); ?></p>
                    <a href="<?php echo htmlspecialchars($card['link']); ?>" class="dash-link">
                        Open <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
    
    <!-- Hotels Tab -->
    <div class="tab-content" id="hotels">
        <section class="hotels-section">
            <!-- Search & Actions -->
            <div class="hotels-header">
                <h2><i class="fas fa-hotel" style="color: var(--primary); margin-right: var(--spacing-sm);"></i> Hotel Collection</h2>
                <div class="search-box">
                    <input type="text" 
                           id="hotelSearch" 
                           class="cyn-form-control cyn-form-control-rounded" 
                           placeholder="Search hotels...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            
            <!-- Stats Bar -->
            <div class="stats-bar" style="margin-left: var(--spacing-lg); margin-right: var(--spacing-lg);">
                <div class="stats-info">
                    Showing <strong><?php echo min($offset + 1, $totalHotels); ?>-<?php echo min($offset + $hotelsPerPage, $totalHotels); ?></strong> of <strong><?php echo $totalHotels; ?></strong> hotels
                </div>
                <?php if ($isAdmin): ?>
                <a href="upload.php" class="cyn-btn cyn-btn-primary cyn-btn-sm">
                    <i class="fas fa-plus"></i> Add Hotel
                </a>
                <?php endif; ?>
            </div>
            
            <!-- Hotels Grid -->
            <div class="hotels-grid" id="hotelsList">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="hotel-card">
                        <div class="hotel-icon">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <div class="hotel-info">
                            <div class="hotel-name">
                                <a href="hotel.php?name=<?php echo urlencode($row['hotel_name']); ?>">
                                    <?php echo htmlspecialchars($row['hotel_name']); ?>
                                </a>
                            </div>
                            <div class="hotel-meta">
                                <i class="fas fa-star" style="color: var(--primary);"></i> Premium Partner
                            </div>
                        </div>
                        <div class="hotel-action">
                            <a href="hotel.php?name=<?php echo urlencode($row['hotel_name']); ?>" class="cyn-btn cyn-btn-primary cyn-btn-sm">
                                View <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-hotel"></i>
                        <h3>No Hotels Found</h3>
                        <p>There are no hotels in the database yet.</p>
                        <?php if ($isAdmin): ?>
                        <a href="upload.php" class="cyn-btn cyn-btn-primary" style="margin-top: var(--spacing-md);">
                            <i class="fas fa-plus"></i> Add First Hotel
                        </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>#hotels" class="pagination-link">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                
                if ($start > 1): ?>
                <a href="?page=1#hotels" class="pagination-link">1</a>
                <?php if ($start > 2): ?>
                <span class="pagination-link" style="pointer-events: none;" aria-hidden="true">...</span>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?page=<?php echo $i; ?>#hotels" class="pagination-link <?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                <span class="pagination-link" style="pointer-events: none;" aria-hidden="true">...</span>
                <?php endif; ?>
                <a href="?page=<?php echo $totalPages; ?>#hotels" class="pagination-link"><?php echo $totalPages; ?></a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>#hotels" class="pagination-link">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </section>
    </div>
    
    <?php cyn_render_footer(); ?>
    <?php cyn_render_scripts(); ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetTab = this.dataset.tab;
                
                // Update active states
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
                
                // Update URL hash
                history.pushState(null, '', '#' + targetTab);
            });
        });
        
        // Check URL hash on load
        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            // Sanitize hash to only allow valid tab identifiers (alphanumeric and hyphens)
            if (/^[a-zA-Z0-9-]+$/.test(hash)) {
                const targetBtn = document.querySelector(`.tab-btn[data-tab="${hash}"]`);
                if (targetBtn) {
                    targetBtn.click();
                }
            }
        }
        
        // Hotel search functionality
        const searchInput = document.getElementById('hotelSearch');
        const hotelCards = document.querySelectorAll('.hotel-card');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                hotelCards.forEach(card => {
                    const hotelName = card.querySelector('.hotel-name').textContent.toLowerCase();
                    if (hotelName.includes(searchTerm)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });
    </script>
</body>
</html>
