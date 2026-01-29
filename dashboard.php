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
    $company_id_redirect = isset($_GET['company']) ? intval($_GET['company']) : 0;

    if ($receipt_id_to_delete > 0) {
        $sql = "DELETE FROM receipts WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$receipt_id_to_delete]);
    }

    // Redirect to prevent form resubmission
    header("Location: dashboard.php?company=" . $company_id_redirect);
    exit;
}

// Get company ID
$companyId = isset($_GET['company']) ? intval($_GET['company']) : 0;

if ($companyId) {
    // Tek bir şirketin makbuzlarını görüntüleme mantığı
    $sql_receipts = "SELECT r.id, r.receipt_date, r.subject, r.received_by, p.company 
                     FROM receipts r 
                     JOIN partners p ON r.partner_id = p.id 
                     WHERE r.partner_id = ? 
                     ORDER BY r.receipt_date DESC, r.id DESC";
    $stmt_receipts = $pdo->prepare($sql_receipts);
    $stmt_receipts->execute([$companyId]);
    $receipts = $stmt_receipts->fetchAll(PDO::FETCH_ASSOC);

    // Tabloda göstermek için bu makbuzlara ait tüm ödemeleri çek
    $sql_payments = "SELECT rp.receipt_id, rp.amount, rp.currency, rp.money_provider
                     FROM receipt_payments rp
                     JOIN receipts r ON rp.receipt_id = r.id
                     WHERE r.partner_id = ?";
    $stmt_payments = $pdo->prepare($sql_payments);
    $stmt_payments->execute([$companyId]);
    $all_payments = $stmt_payments->fetchAll(PDO::FETCH_ASSOC);

    // Kolay erişim için ödemeleri makbuz kimliğine göre grupla
    $payments_by_receipt = [];
    foreach ($all_payments as $payment) {
        $payments_by_receipt[$payment['receipt_id']][] = $payment;
    }

    $companyName = $receipts ? $receipts[0]['company'] : 'Şirket Bulunamadı';

    // Bu şirket için receipt_payments tablosundan toplamları hesapla
    $sql_totals = "SELECT rp.currency, SUM(rp.amount) as total_sum
                   FROM receipt_payments rp
                   JOIN receipts r ON rp.receipt_id = r.id
                   WHERE r.partner_id = ?
                   GROUP BY rp.currency";
    $stmt_totals = $pdo->prepare($sql_totals);
    $stmt_totals->execute([$companyId]);
    $totals_data = $stmt_totals->fetchAll(PDO::FETCH_ASSOC);

    $currency_totals = [];
    foreach($totals_data as $row) {
        $currency_totals[$row['currency']] = $row['total_sum'];
    }
    $receiptCount = count($receipts);

} else {
    // Ana panel görünümü mantığı (tüm şirketler)
    // Adım 1: Tüm şirketleri ve toplam makbuz sayılarını al.
    $sql_companies = "SELECT p.id, p.company, 
                          (SELECT COUNT(*) FROM receipts r WHERE r.partner_id = p.id) AS receipt_count
                      FROM partners p
                      ORDER BY p.company";
    $all_companies = $pdo->query($sql_companies)->fetchAll(PDO::FETCH_ASSOC);

    // Adım 2: Şirket ve para birimine göre gruplandırılmış tüm ödeme toplamlarını al.
    $sql_totals = "SELECT r.partner_id, rp.currency, SUM(rp.amount) as total_sum
                   FROM receipt_payments rp
                   JOIN receipts r ON rp.receipt_id = r.id
                   GROUP BY r.partner_id, rp.currency";
    $all_totals = $pdo->query($sql_totals)->fetchAll(PDO::FETCH_ASSOC);

    // Adım 3: Kolay erişim için toplamları şirkete göre düzenle.
    $totals_by_company = [];
    foreach ($all_totals as $total_row) {
        $totals_by_company[$total_row['partner_id']][$total_row['currency']] = $total_row['total_sum'];
    }

    // Adım 4: Verileri görüntüleme için son bir yapıda birleştir.
    $companies = [];
    foreach ($all_companies as $company_row) {
        $id = $company_row['id'];
        $companies[$id] = [
            'id' => $id,
            'company' => $company_row['company'],
            'receipt_count' => $company_row['receipt_count'],
            'totals' => $totals_by_company[$id] ?? []
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $companyId ? htmlspecialchars($companyName) : 'Receipt Dashboard' ?> - CynTour</title>
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
    <?php if ($companyId): ?>
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
                            <th>#</th>
                            <th>Date</th>
                            <th>Received By</th>
                            <th>Subject</th>
                            <th>Provider</th>
                            <th style="text-align: right;">Payments</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($receipts as $i => $row): ?>
                        <tr class="receipt-row" style="--delay: <?= 0.05 * $i ?>s;">
                            <td><strong>#<?= htmlspecialchars($row['id']) ?></strong></td>
                            <td><?= date('d M Y', strtotime($row['receipt_date'])) ?></td>
                            <td><?= htmlspecialchars($row['received_by'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td>
                                <?php 
                                $receipt_payments = $payments_by_receipt[$row['id']] ?? [];
                                if (!empty($receipt_payments)) {
                                    echo htmlspecialchars($receipt_payments[0]['money_provider']);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="text-right">
                                <?php if (!empty($receipt_payments)): ?>
                                    <?php foreach ($receipt_payments as $payment): ?>
                                        <div class="payment-amount"><?= get_currency_symbol($payment['currency']) . number_format($payment['amount'], 2) ?></div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a class="btn-action btn-view" href="receipt-view.php?id=<?= $row['id'] ?>" target="_blank" aria-label="View receipt #<?= $row['id'] ?>">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <form action="dashboard.php?company=<?= $companyId ?>" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete receipt #<?= $row['id'] ?>?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                        <input type="hidden" name="delete_receipt_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn-action btn-delete" aria-label="Delete receipt #<?= $row['id'] ?>">
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
        
        <div class="company-grid">
            <?php foreach ($companies as $i => $c): ?>
            <div class="company-card" style="animation-delay: <?= 0.1 * $i ?>s;">
                <a href="dashboard.php?company=<?= $c['id'] ?>">
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
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<footer class="dashboard-footer">
    © <?= date('Y') ?> CynTour. All rights reserved.
</footer>

</body>
</html>