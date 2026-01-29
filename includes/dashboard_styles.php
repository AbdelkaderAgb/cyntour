<?php
/**
 * Shared Styles for CYN Tourism Dashboard Pages
 * Include this file in all form pages for consistent design
 * 
 * Usage: 
 * <?php include 'includes/dashboard_styles.php'; ?>
 */

// Function to output the CSS for dashboard-style forms
function outputDashboardStyles($primaryColor = '#6366f1') {
    $primaryDark = adjustBrightness($primaryColor, -20);
    $primaryLight = adjustBrightness($primaryColor, 20);
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: <?php echo $primaryColor; ?>;
        --primary-dark: <?php echo $primaryDark; ?>;
        --primary-light: <?php echo $primaryLight; ?>;
        --background: #f8fafc;
        --card: #ffffff;
        --text: #1e293b;
        --text-muted: #64748b;
        --border: #e2e8f0;
    }
    
    [data-theme="dark"] {
        --primary: <?php echo $primaryLight; ?>;
        --primary-dark: <?php echo $primaryColor; ?>;
        --background: #0f172a;
        --card: #1e293b;
        --text: #f8fafc;
        --text-muted: #94a3b8;
        --border: #334155;
    }
    
    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--background);
        color: var(--text);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        transition: all 0.3s ease;
        background-image: 
            radial-gradient(at 100% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
            radial-gradient(at 0% 100%, rgba(129, 140, 248, 0.05) 0px, transparent 50%);
    }
    
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.2);
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .page-header .subtitle {
        opacity: 0.9;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .back-link {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        background: rgba(255, 255, 255, 0.1);
    }
    
    .back-link:hover {
        color: white;
        background: rgba(255, 255, 255, 0.2);
        transform: translateX(-2px);
    }
    
    .header-logo {
        height: 50px;
        filter: brightness(0) invert(1);
        transition: transform 0.3s ease;
    }
    
    .header-logo:hover {
        transform: scale(1.05);
    }
    
    /* Form Container */
    .form-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 1rem 3rem;
    }
    
    /* Cards */
    .card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 30px rgba(99, 102, 241, 0.1);
    }
    
    .card-header {
        background: var(--card);
        border-bottom: 1px solid var(--border);
        padding: 1.25rem 1.5rem;
    }
    
    .card-header h5 {
        margin: 0;
        color: var(--primary);
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    /* Form Controls */
    .form-label {
        font-weight: 500;
        color: var(--text);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .form-control, .form-select {
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 0.9375rem;
        transition: all 0.2s ease;
        background-color: var(--card);
        color: var(--text);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }
    
    .form-control::placeholder {
        color: var(--text-muted);
    }
    
    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }
    
    .btn-secondary {
        background: var(--card);
        border: 1px solid var(--border);
        color: var(--text);
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background: var(--background);
        border-color: var(--primary);
        color: var(--primary);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
    }
    
    /* Alerts */
    .alert {
        border: none;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
    }
    
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        border-left: 4px solid #10b981;
    }
    
    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border-left: 4px solid #ef4444;
    }
    
    /* Section Headers */
    .section-header {
        font-size: 1rem;
        font-weight: 600;
        color: var(--primary);
        margin: 1.5rem 0 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--border);
    }
    
    /* Icon styling */
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(129, 140, 248, 0.1));
        color: var(--primary);
    }
    
    /* Footer */
    .page-footer {
        text-align: center;
        padding: 2rem;
        color: var(--text-muted);
        font-size: 0.875rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem 0;
        }
        
        .page-header h1 {
            font-size: 1.25rem;
        }
        
        .form-container {
            padding: 0 0.75rem 2rem;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
    
    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
</style>
<?php
}

// Function to output the page header
function outputPageHeader($title, $icon = 'fa-file-alt', $subtitle = '', $backLink = 'Vcdashboard.php') {
?>
<div class="page-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <a href="<?php echo htmlspecialchars($backLink); ?>" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </a>
                <h1 class="mt-2">
                    <i class="fas <?php echo htmlspecialchars($icon); ?> me-2"></i>
                    <?php echo htmlspecialchars($title); ?>
                </h1>
                <?php if ($subtitle): ?>
                <p class="subtitle"><?php echo htmlspecialchars($subtitle); ?></p>
                <?php endif; ?>
            </div>
            <img src="logo.png" alt="CYN Tourism" class="header-logo">
        </div>
    </div>
</div>
<?php
}

// Function to output JavaScript includes
function outputDashboardScripts() {
?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
}

// Helper function to adjust color brightness
function adjustBrightness($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + ($r * $percent / 100)));
    $g = max(0, min(255, $g + ($g * $percent / 100)));
    $b = max(0, min(255, $b + ($b * $percent / 100)));
    
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}
?>
