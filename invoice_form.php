<?php
// Include authentication
include 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Invoice Form - CYN Tourism</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        
        .btn-secondary {
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 0.5rem;
        }
        
        .btn-secondary:hover {
            background: var(--background);
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .room-group {
            border: 1px solid var(--border);
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.75rem;
            background-color: #fafbfc;
        }
        
        .room-group h4 {
            color: var(--primary);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .section-header {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border);
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
                    <h1 class="mt-2"><i class="fas fa-file-invoice-dollar me-2"></i>Hotel Invoice Form</h1>
                </div>
                <img src="logo.png" alt="CYN Tourism" class="header-logo">
            </div>
        </div>
    </div>
    
    <div class="form-container">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-alt me-2"></i>Create New Hotel Invoice</h5>
            </div>
            <div class="card-body">
                <form id="invoiceDataForm" action="invoice.php" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="invoice_no" class="form-label"><i class="fas fa-hashtag me-1 text-muted"></i>Invoice No:</label>
                            <input type="text" class="form-control" id="invoice_no" name="invoice_no" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label"><i class="fas fa-building me-1 text-muted"></i>Company Name:</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="hotel_name" class="form-label"><i class="fas fa-hotel me-1 text-muted"></i>Hotel Name:</label>
                        <input type="text" class="form-control" id="hotel_name" name="hotel_name" required>
                    </div>
                    
                    <h4 class="section-header"><i class="fas fa-bed me-2"></i>Room Information</h4>
                    
                    <div id="roomsContainer">
                        <div class="room-group">
                            <h4>Room 1</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="room_1" class="form-label">Room Type:</label>
                                    <input type="text" class="form-control" id="room_1" name="room_1" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="price_per_night_1" class="form-label">Price per Night:</label>
                                    <input type="number" class="form-control" id="price_per_night_1" name="price_per_night_1" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-secondary mb-4" id="addRoomBtn">
                        <i class="fas fa-plus me-1"></i>Add Room
                    </button>
                    
                    <h4 class="section-header"><i class="fas fa-calendar-alt me-2"></i>Accommodation Dates</h4>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="accommodation_start" class="form-label">Check-in Date:</label>
                            <input type="text" class="form-control" id="accommodation_start" name="accommodation_start" placeholder="Select date..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="accommodation_end" class="form-label">Check-out Date:</label>
                            <input type="text" class="form-control" id="accommodation_end" name="accommodation_end" placeholder="Select date..." required>
                        </div>
                    </div>
                    
                    <h4 class="section-header"><i class="fas fa-money-bill me-2"></i>Additional Charges</h4>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="transfer_price" class="form-label">Transfer Price:</label>
                            <input type="number" class="form-control" id="transfer_price" name="transfer_price" step="0.01">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="currency" class="form-label">Currency:</label>
                            <select class="form-select" id="currency" name="currency" required>
                                <option value="$">$ USD</option>
                                <option value="€">€ EUR</option>
                                <option value="₺">₺ TRY</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="notes" class="form-label">Notes:</label>
                            <input type="text" class="form-control" id="notes" name="notes" placeholder="Optional notes">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="customers" class="form-label"><i class="fas fa-users me-1 text-muted"></i>Customers:</label>
                        <textarea class="form-control" id="customers" name="customers" rows="3" placeholder="Enter customer names separated by commas" required></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="Vcdashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-invoice me-1"></i>Generate Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // Initialise date pickers
        flatpickr("#accommodation_start", {
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            altInputClass: "form-control"
        });

        flatpickr("#accommodation_end", {
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            altInputClass: "form-control"
        });

        // Dynamic room sections
        let roomCount = 1;
        document.getElementById('addRoomBtn').addEventListener('click', function () {
            roomCount++;
            const roomGroup = document.createElement('div');
            roomGroup.className = 'room-group';
            roomGroup.innerHTML = `
                <h4>Room ${roomCount}</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="room_${roomCount}" class="form-label">Room Type:</label>
                        <input type="text" class="form-control" id="room_${roomCount}" name="room_${roomCount}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="price_per_night_${roomCount}" class="form-label">Price per Night:</label>
                        <input type="number" class="form-control" id="price_per_night_${roomCount}" name="price_per_night_${roomCount}" step="0.01" required>
                    </div>
                </div>
            `;
            document.getElementById('roomsContainer').appendChild(roomGroup);
        });
    </script>
</body>

</html>
