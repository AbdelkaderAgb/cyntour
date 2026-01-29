<?php
/**
 * CynTour - Hotels Listing Page (Unified Design)
 * 
 * This page displays the hotel listings with pagination and search functionality.
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
        
        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $resultToken = $stmt->get_result();
        
        if ($resultToken->num_rows > 0) {
            $user = $resultToken->fetch_assoc();
            $_SESSION['auth'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
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

// Database connection
$conn = getMysqliConnection();

// Get current page number from query string (default is 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$hotelsPerPage = 12;
$offset = ($page - 1) * $hotelsPerPage;

// Query to fetch hotel names with pagination
$sql = "SELECT DISTINCT hotel_name FROM pricing_data LIMIT $offset, $hotelsPerPage";
$result = $conn->query($sql);

// Query to get total number of hotels for pagination
$totalHotelsResult = $conn->query("SELECT COUNT(DISTINCT hotel_name) as total FROM pricing_data");
$totalHotelsRow = $totalHotelsResult->fetch_assoc();
$totalHotels = $totalHotelsRow['total'];
$totalPages = ceil($totalHotels / $hotelsPerPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php cyn_render_head('CynTour - Hotel Listings'); ?>
    <style>
        /* Hotels Page Specific Styles */
        .hotels-hero {
            background: linear-gradient(135deg, rgba(42, 77, 105, 0.9) 0%, rgba(26, 51, 72, 0.95) 100%),
                        url('istanbul.jpeg') center/cover no-repeat;
            padding: var(--spacing-3xl) var(--spacing-lg);
            text-align: center;
            color: var(--white);
        }
        
        .hotels-hero h1 {
            font-size: 2.5rem;
            color: var(--white);
            margin-bottom: var(--spacing-md);
        }
        
        .hotels-hero h1 span {
            color: var(--primary-light);
        }
        
        .hotels-hero p {
            color: rgba(255,255,255,0.8);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-section {
            max-width: 700px;
            margin: var(--spacing-xl) auto;
            position: relative;
        }
        
        .search-input {
            padding-right: 60px;
        }
        
        .search-icon {
            position: absolute;
            right: var(--spacing-lg);
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 1.25rem;
        }
        
        .hotels-section {
            padding: var(--spacing-2xl) 0;
        }
        
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: var(--spacing-lg);
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
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(202, 140, 5, 0.1);
            border-radius: var(--radius-lg);
            margin-right: var(--spacing-lg);
            flex-shrink: 0;
        }
        
        .hotel-icon i {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .hotel-info {
            flex: 1;
            min-width: 0;
        }
        
        .hotel-name {
            font-family: var(--font-heading);
            font-size: 1.1rem;
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
            font-size: 0.85rem;
            color: var(--gray-500);
        }
        
        .hotel-action {
            flex-shrink: 0;
            margin-left: var(--spacing-md);
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            gap: var(--spacing-sm);
            margin-top: var(--spacing-2xl);
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
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: var(--spacing-lg);
            color: var(--gray-300);
        }
        
        .stats-bar {
            background: var(--white);
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: var(--spacing-xl);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }
        
        .stats-bar-info {
            color: var(--gray-600);
        }
        
        .stats-bar-info strong {
            color: var(--primary);
        }
        
        @media (max-width: 767px) {
            .hotels-grid {
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
        }
    </style>
</head>
<body>
    <?php cyn_render_navbar(); ?>
    
    <!-- Hero Section -->
    <section class="hotels-hero">
        <div class="cyn-container">
            <h1>Premium <span>Hotel</span> Collection</h1>
            <p>Discover Turkey's finest accommodations. From boutique hotels to luxury resorts, find the perfect stay for your journey.</p>
            
            <!-- Search Bar -->
            <div class="search-section">
                <input type="text" 
                       id="hotelSearch" 
                       class="cyn-form-control cyn-form-control-rounded search-input" 
                       placeholder="Search hotels by name...">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>
    </section>
    
    <!-- Hotels Listing -->
    <section class="hotels-section">
        <div class="cyn-container">
            <!-- Stats Bar -->
            <div class="stats-bar">
                <div class="stats-bar-info">
                    Showing <strong><?php echo min($offset + 1, $totalHotels); ?>-<?php echo min($offset + $hotelsPerPage, $totalHotels); ?></strong> of <strong><?php echo $totalHotels; ?></strong> hotels
                </div>
                <?php if (cyn_is_admin()): ?>
                <a href="upload.php" class="cyn-btn cyn-btn-primary cyn-btn-sm">
                    <i class="fas fa-plus"></i> Add Hotel
                </a>
                <?php endif; ?>
            </div>
            
            <!-- Hotels Grid -->
            <div class="hotels-grid" id="hotelsList">
                <?php if ($result->num_rows > 0): ?>
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
                                <i class="fas fa-star text-warning"></i> Premium Partner
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
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <i class="fas fa-hotel"></i>
                        <h3>No Hotels Found</h3>
                        <p>There are no hotels in the database yet.</p>
                        <?php if (cyn_is_admin()): ?>
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
                <a href="?page=<?php echo $page - 1; ?>" class="pagination-link">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                
                if ($start > 1): ?>
                <a href="?page=1" class="pagination-link">1</a>
                <?php if ($start > 2): ?>
                <span class="pagination-link" style="pointer-events: none;">...</span>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="pagination-link <?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                <span class="pagination-link" style="pointer-events: none;" aria-hidden="true">...</span>
                <?php endif; ?>
                <a href="?page=<?php echo $totalPages; ?>" class="pagination-link"><?php echo $totalPages; ?></a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="pagination-link">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <?php cyn_render_footer(); ?>
    <?php cyn_render_scripts(); ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hotel search functionality
        const searchInput = document.getElementById('hotelSearch');
        const hotelCards = document.querySelectorAll('.hotel-card');
        
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
    });
    </script>
</body>
</html>
