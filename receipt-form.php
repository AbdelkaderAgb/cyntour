<?php
// Include authentication
include 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Form - CynTour</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="css/cyntour-style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .page-header {
            background: var(--secondary-gradient);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .page-header-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .page-header h1 {
            font-family: var(--font-heading);
            font-size: 1.75rem;
            color: white;
            margin: 0.5rem 0 0;
        }

        .back-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            transition: color var(--transition-fast);
        }

        .back-link:hover {
            color: white;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 1.5rem 3rem;
        }

        .form-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            border: 1px solid var(--gray-100);
        }

        .form-card-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-card-header h5 {
            font-family: var(--font-heading);
            font-size: 1.15rem;
            color: var(--secondary);
            margin: 0;
            font-weight: 600;
        }

        .form-card-header i {
            color: var(--primary);
            font-size: 1.25rem;
        }

        .form-card-body {
            padding: 2rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-field-full {
            grid-column: 1 / -1;
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-field-full {
                grid-column: auto;
            }
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-100);
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div class="page-header-content">
            <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i>Back to Dashboard</a>
            <h1><i class="fas fa-receipt"></i> Receipt Form</h1>
        </div>
    </div>
    
    <div class="form-container">
        <div class="form-card">
            <div class="form-card-header">
                <i class="fas fa-file-invoice-dollar"></i>
                <h5>Create New Receipt</h5>
            </div>
            <div class="form-card-body">
                <form action="receipt.php" method="post">
                    <div class="form-row">
                        <div class="cyn-form-group form-field-full">
                            <label class="cyn-form-label" for="company_name">
                                <i class="fas fa-building"></i> Company Name
                            </label>
                            <input type="text" class="cyn-form-control" id="company_name" name="company_name" placeholder="Enter company name" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="cyn-form-group">
                            <label class="cyn-form-label" for="amount">
                                <i class="fas fa-money-bill"></i> Amount
                            </label>
                            <input type="number" class="cyn-form-control" id="amount" name="amount" min="0" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="cyn-form-group">
                            <label class="cyn-form-label" for="currency">
                                <i class="fas fa-coins"></i> Currency
                            </label>
                            <select class="cyn-form-control cyn-form-select" id="currency" name="currency" required>
                                <option value="$">USD ($)</option>
                                <option value="€">EUR (€)</option>
                                <option value="₺">TRY (₺)</option>
                                <option value="د.ج">DZD (د.ج)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="cyn-form-group">
                            <label class="cyn-form-label" for="date">
                                <i class="fas fa-calendar"></i> Date
                            </label>
                            <input type="date" class="cyn-form-control" id="date" name="date" required>
                        </div>
                        <div class="cyn-form-group">
                            <label class="cyn-form-label" for="payment_method">
                                <i class="fas fa-credit-card"></i> Payment Method
                            </label>
                            <select class="cyn-form-control cyn-form-select" id="payment_method" name="payment_method" required>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="cyn-form-group">
                        <label class="cyn-form-label" for="reason">
                            <i class="fas fa-info-circle"></i> Reason / Description
                        </label>
                        <input type="text" class="cyn-form-control" id="reason" name="reason" placeholder="Enter payment reason" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="cyn-form-group">
                            <label class="cyn-form-label" for="received_by">
                                <i class="fas fa-user"></i> Received By
                            </label>
                            <input type="text" class="cyn-form-control" id="received_by" name="received_by" placeholder="Name of receiver" required>
                        </div>
                        <div class="cyn-form-group">
                            <label class="cyn-form-label" for="remaining_amount">
                                <i class="fas fa-balance-scale"></i> Remaining Amount (optional)
                            </label>
                            <input type="number" class="cyn-form-control" id="remaining_amount" name="remaining_amount" min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="dashboard.php" class="cyn-btn cyn-btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="cyn-btn cyn-btn-primary cyn-btn-lg">
                            <i class="fas fa-receipt"></i> Generate Receipt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>