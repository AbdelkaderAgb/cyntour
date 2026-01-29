<?php
// Include authentication for all requests
session_start();
require_once 'config.php';

// Check if user is logged in for non-POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    include 'auth.php';
}

// This block will only run when a POST request is made to this page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify user is authenticated for POST requests too
    if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
        header('Content-Type: application/json');
        die(json_encode(["success" => false, "message" => "Unauthorized. Please login first."]));
    }
    
    // Set the content type to application/json for the response
    header('Content-Type: application/json');

    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Database connection
    try {
        $conn = getMysqliConnection();
    } catch (Exception $e) {
        die(json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]));
    }

    // Get POST data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Log received data (for debugging purposes, can be removed in production)
    error_log('Received data: ' . print_r($data, true));

    // Check if the first customer exists before accessing it
    $customer_name = isset($data['customers'][0]['name']) ? $data['customers'][0]['name'] : '';

    // Check if voucher number already exists
    $check_sql = "SELECT voucher_no FROM h_vouchers WHERE voucher_no = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $data['voucher_no']);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Voucher number already exists. Please use a unique voucher number."]);
        $check_stmt->close();
        $conn->close();
        exit(); // Stop script execution
    }
    $check_stmt->close();

    // Handle room value
    $room = isset($data['room']) ? (string)$data['room'] : 'Unknown';
    error_log('Room value: ' . $room);

    // Prepare SQL query to insert new voucher
    $sql = "INSERT INTO h_vouchers (voucher_no, company_name, hotel, room_count, check_in_date, check_out_date, nights, room, transfer_type, customer_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    error_log('SQL query: ' . $sql);

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die(json_encode(["success" => false, "message" => "Query preparation error: " . $conn->error]));
    }

    // Bind form data to SQL query
    $stmt->bind_param("sssissssss", 
        $data['voucher_no'],
        $data['company_name'],
        $data['hotel'],
        $data['room_count'],
        $data['accommodation_start'],
        $data['accommodation_end'],
        $data['nights'],
        $room,
        $data['transfer_type'],
        $customer_name
    );

    // Execute query and handle response
    if ($stmt->execute()) {
        error_log('Affected rows: ' . $stmt->affected_rows);
        echo json_encode(["success" => true, "message" => "Voucher saved successfully"]);
    } else {
        error_log('SQL error: ' . $stmt->error);
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
    exit(); // IMPORTANT: Stop the script to prevent the HTML below from being sent with the JSON response.
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Voucher Form - CYN Tourism</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
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
        
        .customer-info {
            border: 1px solid var(--border);
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.75rem;
            background-color: #fafbfc;
            position: relative;
        }
        
        .section-header {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border);
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
                    <h1 class="mt-2"><i class="fas fa-bed me-2"></i>Hotel Voucher Form</h1>
                </div>
                <img src="logo.png" alt="CYN Tourism" class="header-logo">
            </div>
        </div>
    </div>
    
    <div class="form-container">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-alt me-2"></i>Create New Hotel Voucher</h5>
            </div>
            <div class="card-body">
                <form id="voucherForm">
                    <div class="form-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="voucher_no"><i class="fas fa-hashtag mr-1 text-muted"></i>Voucher No:</label>
                                    <input type="text" class="form-control" id="voucher_no" name="voucher_no" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name"><i class="fas fa-building mr-1 text-muted"></i>Company Name:</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" value="MOLLA GURANI MAH. OGUSHAN CAD. KARAKOYUNLU SOK. NO: 2 D: 4 FINDIKZADE / FATIH" readonly>
            </div>
            <div class="form-group">
                <label for="telephone">Telephone:</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" value="+90 5318176770" readonly>
            </div>
            <div class="form-group">
                <label for="hotel">Hotel:</label>
                <input type="text" class="form-control" id="hotel" name="hotel" required>
            </div>
            <div class="form-group">
                <label for="room_count">Room Count:</label>
                <input type="number" class="form-control" id="room_count" name="room_count" required>
            </div>
            <div class="form-group">
                <label for="accommodation_start">Check-in Date:</label>
                <input type="text" class="form-control" id="accommodation_start" name="accommodation_start" placeholder="Select check-in date..." required>
            </div>
            <div class="form-group">
                <label for="accommodation_end">Check-out Date:</label>
                <input type="text" class="form-control" id="accommodation_end" name="accommodation_end" placeholder="Select check-out date..." required>
            </div>
            <div class="form-group">
                <label for="nights">Nights:</label>
                <input type="number" class="form-control" id="nights" name="nights" readonly>
            </div>
            <div class="form-group">
                <label for="room">Room:</label>
                <input type="text" class="form-control" id="room" name="room" required>
            </div>
            <div class="form-group">
                <label for="transfer_type">Transfer Type:</label>
                <select class="form-control" id="transfer_type" name="transfer_type">
                    <option value="">Select transfer type</option>
                    <option value="Arrival - Return">Arrival - Return</option>
                    <option value="One Way">One Way</option>
                </select>
            </div>
            <div class="form-group">
                <label for="board">Board:</label>
                <input type="text" class="form-control" id="board" name="board" required>
            </div>
            <div class="form-group">
                <label for="adult">Adult:</label>
                <input type="number" class="form-control" id="adult" name="adult" required>
            </div>
            <div class="form-group">
                <label for="child">Child:</label>
                <input type="number" class="form-control" id="child" name="child" required>
            </div>
            <div class="form-group">
                <label for="infant">Infant:</label>
                <input type="number" class="form-control" id="infant" name="infant" required>
            </div>
        </div>

        <h2>Customers</h2>
        <div id="customers">
            <div class="customer-info">
                <div class="form-group">
                    <label for="title1">Title:</label>
                    <select class="form-control" id="title1" name="title1" required>
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Ms">Ms</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name1">Name:</label>
                    <input type="text" class="form-control" id="name1" name="name1" required>
                </div>
                <div class="form-group">
                    <label for="nationality1">Nationality:</label>
                    <input type="text" class="form-control" id="nationality1" name="nationality1" required>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="addCustomer()"><i class="fas fa-plus me-1"></i>Add Customer</button>
            <div>
                <a href="Vcdashboard.php" class="btn btn-outline-secondary me-2"><i class="fas fa-times me-1"></i>Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-file-alt me-1"></i>Generate Voucher</button>
            </div>
        </div>
    </form>
            </div>
        </div>
    </div>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        let customerCount = 1;
        let fp_start, fp_end; // Declare flatpickr instances in a wider scope

        function addCustomer() {
            customerCount++;
            const customersDiv = document.getElementById('customers');
            const newCustomer = document.createElement('div');
            newCustomer.className = 'customer-info';
            newCustomer.innerHTML = `
                <div class="form-group">
                    <label for="title${customerCount}">Title:</label>
                    <select class="form-control" id="title${customerCount}" name="title${customerCount}" required>
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Ms">Ms</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name${customerCount}">Name:</label>
                    <input type="text" class="form-control" id="name${customerCount}" name="name${customerCount}" required>
                </div>
                <div class="form-group">
                    <label for="nationality${customerCount}">Nationality:</label>
                    <input type="text" class="form-control" id="nationality${customerCount}" name="nationality${customerCount}" required>
                </div>
            `;
            customersDiv.appendChild(newCustomer);
        }

        // A new function to handle the calculation
        function calculateAndSetNights() {
            const startDate = fp_start.selectedDates[0];
            const endDate = fp_end.selectedDates[0];

            if (startDate && endDate && startDate < endDate) {
                const diffTime = endDate.getTime() - startDate.getTime();
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                document.getElementById('nights').value = diffDays;
            } else {
                document.getElementById('nights').value = '';
            }
        }

        // Initialize flatpickr on page load
        document.addEventListener('DOMContentLoaded', (event) => {
            fp_start = flatpickr("#accommodation_start", {
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    // When start date changes, set the minimum date for the end date picker
                    if (selectedDates[0]) {
                        fp_end.set('minDate', new Date(selectedDates[0].getTime() + 86400000)); // next day
                    }
                    calculateAndSetNights();
                }
            });

            fp_end = flatpickr("#accommodation_end", {
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    calculateAndSetNights();
                }
            });
        });

        document.getElementById('voucherForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Collect form data
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            // Ensure 'room' is treated as text
            data.room = data.room.toString();

            // Collect customer data
            data.customers = [];
            const customerDivs = document.querySelectorAll('.customer-info');
            customerDivs.forEach((div, index) => {
                const title = div.querySelector(`select[name="title${index + 1}"]`).value;
                const name = div.querySelector(`input[name="name${index + 1}"]`).value;
                const nationality = div.querySelector(`input[name="nationality${index + 1}"]`).value;
                data.customers.push({ title, name, nationality });
            });

            console.log('Data being sent:', data);

            // Send data to this same page for processing by the PHP block at the top
            fetch(window.location.href, { // Using window.location.href to post to the same page
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Store data in localStorage
                    localStorage.setItem('voucherData', JSON.stringify(data));
                    // Redirect to voucher.php
                    window.location.href = 'voucher.php';
                } else {
                    // Display error message
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the voucher.');
            });
        });
    </script>
    <!-- jQuery and Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>