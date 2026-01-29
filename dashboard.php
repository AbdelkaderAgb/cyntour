<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session for CSRF token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Include required files
require_once 'database-config.php';
require_once 'helpers.php';

// Handle receipt deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_receipt_id'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
    
    $receipt_id_to_delete = intval($_POST['delete_receipt_id']);
    $company_redirect = isset($_GET['company']) ? urlencode($_GET['company']) : '';

    if ($receipt_id_to_delete > 0) {
        $sql = "DELETE FROM receipts WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$receipt_id_to_delete]);
    }

    // Redirect to prevent form resubmission
    header("Location: dashboard.php" . ($company_redirect ? "?company=" . $company_redirect : ""));
    exit;
}

// Get company name from URL (using customer_company field)
$companyFilter = isset($_GET['company']) ? trim($_GET['company']) : '';

if ($companyFilter) {
    // Single company view - show receipts for this company
    // Use DATE_FORMAT in SQL to avoid expensive strtotime() calls in PHP
    $sql_receipts = "SELECT r.id, r.receipt_no, 
                            DATE_FORMAT(r.created_at, '%d %b %Y') as formatted_date,
                            r.created_at, r.customer_name, 
                            r.customer_company, r.total_amount, r.currency, 
                            r.payment_status, r.notes
                     FROM receipts r 
                     WHERE r.customer_company = ? 
                     ORDER BY r.created_at DESC, r.id DESC";
    $stmt_receipts = $pdo->prepare($sql_receipts);
    $stmt_receipts->execute([$companyFilter]);
    $receipts = $stmt_receipts->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all payments for these receipts
    $sql_payments = "SELECT rp.receipt_id, rp.amount, rp.payment_method, rp.payment_date
                     FROM receipt_payments rp
                     JOIN receipts r ON rp.receipt_id = r.id
                     WHERE r.customer_company = ?";
    $stmt_payments = $pdo->prepare($sql_payments);
    $stmt_payments->execute([$companyFilter]);
    $all_payments = $stmt_payments->fetchAll(PDO::FETCH_ASSOC);

    // Group payments by receipt ID for easy access
    $payments_by_receipt = [];
    foreach ($all_payments as $payment) {
        $payments_by_receipt[$payment['receipt_id']][] = $payment;
    }

    $companyName = $companyFilter;

    // Calculate totals for this company by currency
    $sql_totals = "SELECT r.currency, SUM(r.total_amount) as total_sum
                   FROM receipts r
                   WHERE r.customer_company = ?
                   GROUP BY r.currency";
    $stmt_totals = $pdo->prepare($sql_totals);
    $stmt_totals->execute([$companyFilter]);
    $totals_data = $stmt_totals->fetchAll(PDO::FETCH_ASSOC);

    $currency_totals = [];
    foreach($totals_data as $row) {
        $currency_totals[$row['currency']] = $row['total_sum'];
    }
    $receiptCount = count($receipts);

} else {
    // Main dashboard view - show all companies
    // Optimized: Single query using subquery for accurate receipt counts
    // Uses subquery join instead of CTE for MySQL 5.7 compatibility
    $sql_companies = "SELECT r.customer_company, 
                             r.currency,
                             SUM(r.total_amount) as total_sum,
                             cc.receipt_count
                      FROM receipts r
                      JOIN (
                          SELECT customer_company, COUNT(*) as receipt_count
                          FROM receipts 
                          WHERE customer_company IS NOT NULL AND customer_company != ''
                          GROUP BY customer_company
                      ) cc ON r.customer_company = cc.customer_company
                      WHERE r.customer_company IS NOT NULL AND r.customer_company != ''
                      GROUP BY r.customer_company, r.currency, cc.receipt_count
                      ORDER BY r.customer_company";
    $all_company_data = $pdo->query($sql_companies)->fetchAll(PDO::FETCH_ASSOC);

    // Organize data by company - receipt_count is now included in each row
    $companies = [];
    foreach ($all_company_data as $row) {
        $company_name = $row['customer_company'];
        if (!isset($companies[$company_name])) {
            $companies[$company_name] = [
                'company' => $company_name,
                'receipt_count' => $row['receipt_count'],
                'totals' => []
            ];
        }
        $companies[$company_name]['totals'][$row['currency']] = $row['total_sum'];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $companyFilter ? htmlspecialchars($companyName) : 'Receipt Dashboard' ?> - CynTour</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="css/cyntour-style.css" rel="stylesheet">
    <style>
        /* Dashboard Specific Styles */
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        /* Enhanced Navbar */
        .dashboard-navbar {
            background: var(--white);
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--secondary);
            text-decoration: none;
        }

        .navbar-brand i {
            color: var(--primary);
            font-size: 1.75rem;
        }

        .navbar-brand span {
            color: var(--primary);
        }

        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            animation: fadeInUp 0.5s ease-out forwards;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
            animation: fadeInUp 0.5s ease-out 0.1s forwards;
            opacity: 0;
        }

        .page-header h1 {
            font-family: var(--font-heading);
            font-size: 2rem;
            color: var(--secondary);
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: var(--gray-600);
            margin: 0;
        }

        /* Company Cards */
        .company-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .company-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            overflow: hidden;
            border: 1px solid var(--gray-100);
            opacity: 0;
            animation: fadeInUp 0.5s ease-out forwards;
        }

        .company-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .company-card a {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .company-card-body {
            padding: 1.75rem;
            text-align: center;
        }

        .avatar-initials {
            width: 70px;
            height: 70px;
            border-radius: var(--radius-full);
            background: var(--primary-gradient);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            font-family: var(--font-heading);
            margin: 0 auto 1rem;
            box-shadow: var(--shadow-gold);
        }

        .company-card-title {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 1rem;
        }

        .company-card-stats {
            display: flex;
            justify-content: space-around;
            border-top: 1px solid var(--gray-100);
            padding-top: 1rem;
            margin-top: 0.5rem;
        }

        .company-stat {
            text-align: center;
        }

        .company-stat-label {
            font-size: 0.8rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .company-stat-value {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .company-stat-value.highlight {
            color: var(--primary);
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-100);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.receipts {
            background: linear-gradient(135deg, rgba(202,140,5,0.15) 0%, rgba(160,96,0,0.15) 100%);
            color: var(--primary);
        }

        .stat-icon.total {
            background: linear-gradient(135deg, rgba(16,185,129,0.15) 0%, rgba(5,150,105,0.15) 100%);
            color: var(--success);
        }

        .stat-info {
            flex: 1;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-family: var(--font-heading);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-800);
            line-height: 1;
        }

        /* Receipt Table */
        .table-container {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            border: 1px solid var(--gray-100);
        }

        .table-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
            border-bottom: 1px solid var(--gray-100);
            font-family: var(--font-heading);
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-header i {
            color: var(--primary);
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-table thead {
            background: var(--gray-50);
        }

        .receipt-table th {
            padding: 1rem 1.25rem;
            text-align: left;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--gray-200);
        }

        .receipt-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
            color: var(--gray-700);
        }

        .receipt-table .text-right {
            text-align: right;
        }

        .receipt-table .text-center {
            text-align: center;
        }

        .receipt-table tbody tr {
            transition: all 0.2s ease;
        }

        .receipt-table tbody tr:hover {
            background: var(--gray-50);
        }

        .receipt-table tbody tr:last-child td {
            border-bottom: none;
        }

        .receipt-table .receipt-row {
            animation: fadeInUp 0.5s ease-out var(--delay, 0s) forwards;
            opacity: 0;
        }

        .payment-amount {
            font-weight: 600;
            color: var(--success);
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-paid {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .status-partial {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        .status-refunded {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .delete-form {
            display: inline;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .btn-view {
            background: linear-gradient(135deg, rgba(202,140,5,0.1) 0%, rgba(160,96,0,0.1) 100%);
            color: var(--primary);
        }

        .btn-view:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-2px);
        }

        .btn-delete {
            background: linear-gradient(135deg, rgba(239,68,68,0.1) 0%, rgba(220,38,38,0.1) 100%);
            color: var(--danger);
        }

        .btn-delete:hover {
            background: var(--danger);
            color: var(--white);
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 1rem;
        }

        .empty-state h4 {
            color: var(--gray-600);
            margin-bottom: 0.5rem;
        }

        /* Footer */
        .dashboard-footer {
            text-align: center;
            padding: 2rem;
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-top: 2rem;
            border-top: 1px solid var(--gray-200);
            background: var(--white);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-container {
                padding: 0 1rem;
            }
            
            .main-container {
                padding: 1rem;
            }

            .company-grid {
                grid-template-columns: 1fr;
            }

            .stats-row {
                grid-template-columns: 1fr;
            }

            .receipt-table {
                display: block;
                overflow-x: auto;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
    </style>
</head>
<body>

<nav class="dashboard-navbar">
    <div class="navbar-container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-file-invoice-dollar"></i>
            Cyn<span>Tour</span>
        </a>
        <a href="receipt-create.php" class="cyn-btn cyn-btn-primary">
            <i class="fas fa-plus-circle"></i> New Receipt
        </a>
    </div>
</nav>

<div class="main-container">
    <?php if ($companyFilter): ?>
        <!-- ======================= -->
        <!-- Single Company View     -->
        <!-- ======================= -->
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1><?= htmlspecialchars($companyName) ?></h1>
                <p>Receipt history and payment summary</p>
            </div>
            <a href="dashboard.php" class="cyn-btn cyn-btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon receipts">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Receipts</div>
                    <div class="stat-value"><?= $receiptCount ?></div>
                </div>
            </div>

            <?php foreach ($currency_totals as $currency => $total): ?>
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total (<?= htmlspecialchars($currency) ?>)</div>
                    <div class="stat-value"><?= get_currency_symbol($currency) . number_format($total, 2) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Receipt Table -->
        <div class="table-container">
            <div class="table-header">
                <i class="fas fa-history"></i> Receipt History
            </div>
            <?php if (empty($receipts)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h4>No Receipts Found</h4>
                    <p>No receipts have been recorded for this company yet.</p>
                </div>
            <?php else: ?>
                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                            <th style="text-align: right;">Amount</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($receipts as $i => $row): ?>
                        <tr class="receipt-row" style="--delay: <?= 0.05 * $i ?>s;">
                            <td><strong><?= htmlspecialchars($row['receipt_no']) ?></strong></td>
                            <td><?= htmlspecialchars($row['formatted_date']) ?></td>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td>
                                <?php 
                                $status_class = 'status-' . $row['payment_status'];
                                ?>
                                <span class="status-badge <?= $status_class ?>"><?= ucfirst($row['payment_status']) ?></span>
                            </td>
                            <td>
                                <?php 
                                $receipt_payments = $payments_by_receipt[$row['id']] ?? [];
                                if (!empty($receipt_payments)) {
                                    $methods = array_unique(array_column($receipt_payments, 'payment_method'));
                                    echo htmlspecialchars(ucfirst(str_replace('_', ' ', implode(', ', $methods))));
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="text-right">
                                <div class="payment-amount"><?= get_currency_symbol($row['currency']) . number_format($row['total_amount'], 2) ?></div>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a class="btn-action btn-view" href="receipt-view.php?id=<?= $row['id'] ?>" target="_blank" aria-label="View receipt <?= htmlspecialchars($row['receipt_no']) ?>">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <form action="dashboard.php?company=<?= urlencode($companyFilter) ?>" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete receipt <?= htmlspecialchars($row['receipt_no']) ?>?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                        <input type="hidden" name="delete_receipt_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn-action btn-delete" aria-label="Delete receipt <?= htmlspecialchars($row['receipt_no']) ?>">
                                            <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- ======================= -->
        <!-- All Companies View      -->
        <!-- ======================= -->
        <div class="page-header">
            <h1>Company Dashboard</h1>
            <p>Select a company to view detailed receipt history.</p>
        </div>
        
        <?php if (empty($companies)): ?>
            <div class="table-container">
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <h4>No Companies Found</h4>
                    <p>No receipts with company information have been recorded yet.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="company-grid">
                <?php $i = 0; foreach ($companies as $company_name => $c): ?>
                <div class="company-card" style="animation-delay: <?= 0.1 * $i ?>s;">
                    <a href="dashboard.php?company=<?= urlencode($c['company']) ?>">
                        <div class="company-card-body">
                            <?= generate_initials_avatar($c['company']) ?>
                            <h5 class="company-card-title"><?= htmlspecialchars($c['company']) ?></h5>
                            <div class="company-card-stats">
                                <div class="company-stat">
                                    <div class="company-stat-label">Receipts</div>
                                    <div class="company-stat-value highlight"><?= $c['receipt_count'] ?></div>
                                </div>
                                <div class="company-stat">
                                    <div class="company-stat-label">Total Value</div>
                                    <?php if (empty($c['totals'])): ?>
                                        <div class="company-stat-value">-</div>
                                    <?php else: ?>
                                        <?php foreach ($c['totals'] as $currency => $total): ?>
                                            <div class="company-stat-value"><?= get_currency_symbol($currency) . number_format($total, 2) ?></div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php $i++; endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<footer class="dashboard-footer">
    © <?= date('Y') ?> CynTour. All rights reserved.
</footer>

</body>
</html>
