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
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary: #e74a3b;
            --primary-dark: #be2617;
            --light: #f8f9fc;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--light);
            padding: 0;
            margin: 0;
        }
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        .page-header h1 {
            margin: 0;
            font-size: 1.75rem;
        }
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 20px 40px;
        }
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            border-radius: 0.5rem;
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
        }
        .card-header h5 {
            margin: 0;
            color: var(--primary);
            font-weight: 600;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        }
        .back-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        .back-link:hover {
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left mr-2"></i>Back to Dashboard</a>
                    <h1 class="mt-2"><i class="fas fa-receipt mr-2"></i>Receipt Form</h1>
                </div>
                <img src="logo.png" alt="CYN Tourism" style="height: 50px; filter: brightness(0) invert(1);">
            </div>
        </div>
    </div>
    
    <div class="form-container">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-invoice-dollar mr-2"></i>Create New Receipt</h5>
            </div>
            <div class="card-body">
                <form action="receipt.php" method="post">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="company_name"><i class="fas fa-building mr-1 text-muted"></i>Company Name:</label>
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
                        <a href="dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i>Cancel</a>
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