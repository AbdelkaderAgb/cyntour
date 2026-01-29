<?php
// Include authentication
include 'auth.php';

// Initialize error message variable
$error_message = "";
$success_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'config.php';
    
    try {
        // Database connection
        $conn = getMysqliConnection();
        
        // Get form data
        $voucher_no = $_POST['voucher_no'];
        $company_name = $_POST['company_name'];
        $hotel_name = isset($_POST['hotel_name']) ? $_POST['hotel_name'] : '';
        $flight_number = isset($_POST['flight_number']) ? $_POST['flight_number'] : '';
        $pickup_location = $_POST['pickup_location'];
        $dropoff_location = $_POST['dropoff_location'];
        $pickup_date = $_POST['pickup_date'];
        $pickup_time = $_POST['pickup_time'];
        $transfer_type = $_POST['transfer_type'];
        $return_date = isset($_POST['return_date']) && !empty($_POST['return_date']) ? $_POST['return_date'] : null;
        $return_time = isset($_POST['return_time']) && !empty($_POST['return_time']) ? $_POST['return_time'] : null;
        $total_pax = $_POST['total_pax'];
        $passengers = $_POST['passengers'];
        
        // Prepare and execute SQL statement
        $sql = "INSERT INTO transfer_vouchers (voucher_no, company_name, hotel_name, flight_number, pickup_location, dropoff_location, 
                pickup_date, pickup_time, transfer_type, return_date, return_time, total_pax, passengers, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssss", $voucher_no, $company_name, $hotel_name, $flight_number, $pickup_location, 
                        $dropoff_location, $pickup_date, $pickup_time, $transfer_type, $return_date, $return_time, 
                        $total_pax, $passengers);
        
        if ($stmt->execute()) {
            $success_message = "Voucher data saved to database successfully";
            $stmt->close();
            $conn->close();
            
            // Forward the form data to transfer-voucher.php
            // The form will be submitted to transfer-voucher.php as if it came directly from the form
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Forwarding to Transfer Voucher</title>
            </head>
            <body>
                <p>Saving to database and forwarding to transfer voucher...</p>
                
                <form id="forwardForm" action="transfer-voucher.php" method="post">
                    <?php foreach($_POST as $key => $value): ?>
                        <?php if(is_array($value)): ?>
                            <?php foreach($value as $item): ?>
                                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>[]" value="<?php echo htmlspecialchars($item); ?>">
                            <?php endforeach; ?>
                        <?php else: ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </form>
                
                <script>
                    // Automatically submit the form
                    document.getElementById('forwardForm').submit();
                </script>
            </body>
            </html>
            <?php
            exit;
        } else {
            throw new Exception("Error inserting data: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Voucher Form - CYN Tourism</title>
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
            max-width: 900px;
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
                    <h1 class="mt-2"><i class="fas fa-shuttle-van me-2"></i>Transfer Voucher Form</h1>
                </div>
                <img src="logo.png" alt="CYN Tourism" class="header-logo">
            </div>
        </div>
    </div>
    
    <div class="form-container">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-alt mr-2"></i>Create New Transfer Voucher</h5>
            </div>
            <div class="card-body">
        
        <?php if ($success_message): ?>
            <div class="alert alert-success mt-3 mb-3" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger mt-3 mb-3" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="voucher_no">Voucher No:</label>
                <input type="text" class="form-control" id="voucher_no" name="voucher_no" required>
            </div>
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>
            <div class="form-group">
                <label for="hotel_name">Hotel Name:</label>
                <input type="text" class="form-control" id="hotel_name" name="hotel_name">
            </div>
            <div class="form-group">
                <label for="flight_number">Flight Number:</label>
                <input type="text" class="form-control" id="flight_number" name="flight_number">
            </div>
            <div class="form-group">
                <label for="pickup_location">Starting Point:</label>
                <input type="text" class="form-control" id="pickup_location" name="pickup_location" required>
            </div>
            <div class="form-group">
                <label for="dropoff_location">Destination:</label>
                <input type="text" class="form-control" id="dropoff_location" name="dropoff_location" required>
            </div>
            <div class="form-group">
                <label for="pickup_date">Date:</label>
                <input type="date" class="form-control" id="pickup_date" name="pickup_date" required>
            </div>
            <div class="form-group">
                <label for="pickup_time">Time:</label>
                <input type="time" class="form-control" id="pickup_time" name="pickup_time" required>
            </div>
            <div class="form-group">
                <label for="transfer_type">Transfer Type:</label>
                <select class="form-control" id="transfer_type" name="transfer_type" required onchange="toggleReturnDateTime(this.value)">
                    <option value="One Way">One Way</option>
                    <option value="Arrival-Return">Arrival-Return</option>
                </select>
            </div>
            <div class="form-group" id="return_date_group" style="display: none;">
                <label for="return_date">Return Date:</label>
                <input type="date" class="form-control" id="return_date" name="return_date">
            </div>
            <div class="form-group" id="return_time_group" style="display: none;">
                <label for="return_time">Return Time:</label>
                <input type="time" class="form-control" id="return_time" name="return_time">
            </div>
            <div class="form-group">
                <label for="total_pax">Total Pax:</label>
                <input type="number" class="form-control" id="total_pax" name="total_pax" required>
            </div>
            <div class="form-group">
                <label for="passengers">Passengers:</label>
                <textarea class="form-control" id="passengers" name="passengers" rows="5" placeholder="Enter passenger names separated by new lines" required></textarea>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="admin.php" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i>Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-file-alt mr-1"></i>Generate Voucher</button>
            </div>
        </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function toggleReturnDateTime(value) {
            const returnDateGroup = document.getElementById('return_date_group');
            const returnTimeGroup = document.getElementById('return_time_group');
            if (value === 'Arrival-Return') {
                returnDateGroup.style.display = 'block';
                returnTimeGroup.style.display = 'block';
            } else {
                returnDateGroup.style.display = 'none';
                returnTimeGroup.style.display = 'none';
            }
        }
    </script>
</body>

</html>