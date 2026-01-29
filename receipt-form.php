<?php
// Include authentication
include 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Form - CYN Tourism</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --background: #f8fafc;
            --card: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-image: 
                radial-gradient(at 100% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(129, 140, 248, 0.05) 0px, transparent 50%);
        }
        
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
        }
        
        .header-logo {
            height: 50px;
            filter: brightness(0) invert(1);
        }
        
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 1rem 3rem;
        }
        
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
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
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
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
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 0.5rem;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <a href="Vcdashboard.php" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Dashboard</span>
                    </a>
                    <h1 class="mt-2"><i class="fas fa-receipt me-2"></i>Receipt Form</h1>
                </div>
                <img src="logo.png" alt="CYN Tourism" class="header-logo">
            </div>
        </div>
    </div>
    
    <div class="form-container">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-invoice-dollar me-2"></i>Create New Receipt</h5>
            </div>
            <div class="card-body">
                <form action="receipt.php" method="post">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="company_name" class="form-label"><i class="fas fa-building me-1 text-muted"></i>Company Name:</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date"><i class="fas fa-calendar mr-1 text-muted"></i>Date:</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount"><i class="fas fa-money-bill mr-1 text-muted"></i>Amount:</label>
                                <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="currency"><i class="fas fa-coins mr-1 text-muted"></i>Currency:</label>
                                <select class="form-control" id="currency" name="currency" required>
                                    <option value="$">USD ($)</option>
                                    <option value="€">EUR (€)</option>
                                    <option value="₺">TRY (₺)</option>
                                    <option value="د.ج">DZD (د.ج)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reason"><i class="fas fa-info-circle mr-1 text-muted"></i>Reason:</label>
                        <input type="text" class="form-control" id="reason" name="reason" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_method"><i class="fas fa-credit-card mr-1 text-muted"></i>Payment Method:</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="received_by"><i class="fas fa-user mr-1 text-muted"></i>Received By:</label>
                                <input type="text" class="form-control" id="received_by" name="received_by" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remaining_amount"><i class="fas fa-balance-scale mr-1 text-muted"></i>Remaining Amount (optional):</label>
                        <input type="number" class="form-control" id="remaining_amount" name="remaining_amount" min="0" step="0.01">
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="admin.php" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i>Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-receipt mr-1"></i>Generate Receipt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>