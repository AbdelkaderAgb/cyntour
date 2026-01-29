<?php
/* ───── 0.  Include authentication ────────────────────────── */
include 'auth.php';

/* ───── 1.  DB connection ───────────────────────────────────── */
require_once 'config.php';
$conn = getMysqliConnection();

/* ───── 2.  Handle POST save then redirect ─────────────────── */
if($_SERVER['REQUEST_METHOD']==='POST'){
    /* 2-a: insert voucher header and grab its id */
    $stmt = $conn->prepare(
        "INSERT INTO city_tour_vouchers
         (voucher_no,company_name,customer_phone,hotel_name,adult,child,infant)
         VALUES (?,?,?,?,?,?,?)"
    );
    $stmt->bind_param(
        'ssssiii',
        $_POST['voucher_no'],
        $_POST['company_name'],
        $_POST['customer_phone'],
        $_POST['hotel_name'],
        $_POST['adult'],
        $_POST['child'],
        $_POST['infant']
    );
    $stmt->execute();
    $voucher_id = $stmt->insert_id;
    $stmt->close();

    /* 2-b: insert each tour row */
    if(!empty($_POST['tour_name'])){
        $stmt=$conn->prepare(
            "INSERT INTO city_tour_voucher_tours
             (voucher_id,tour_name,tour_date,duration)
             VALUES (?,?,?,?)"
        );
        for($i=0;$i<count($_POST['tour_name']);$i++){
            $stmt->bind_param(
                'isss',
                $voucher_id,
                $_POST['tour_name'][$i],
                $_POST['tour_date'][$i],
                $_POST['tour_duration'][$i]
            );
            $stmt->execute();
        }
        $stmt->close();
    }

    /* 2-c: insert each passenger name */
    if(!empty($_POST['customer_name'])){
        $stmt=$conn->prepare(
            "INSERT INTO city_tour_voucher_customers (voucher_id,customer_name)
             VALUES (?,?)"
        );
        foreach($_POST['customer_name'] as $name){
            $stmt->bind_param('is',$voucher_id,$name);
            $stmt->execute();
        }
        $stmt->close();
    }

    /* 2-d: all done – jump to the PDF page */
    header("Location: tour_voucher.php?voucher_id=$voucher_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tour Voucher Form - CYN Tourism</title>

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
}
.customer-info, .tour-info {
    border: 1px solid var(--border);
    padding: 1.25rem;
    margin-bottom: 1rem;
    border-radius: 0.75rem;
    background-color: #fafbfc;
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
                <h1 class="mt-2"><i class="fas fa-map-marked-alt me-2"></i>Tour Voucher Form</h1>
            </div>
            <img src="logo.png" alt="CYN Tourism" class="header-logo">
        </div>
    </div>
</div>

<div class="form-container">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-file-alt me-2"></i>Create New Tour Voucher</h5>
        </div>
        <div class="card-body">

<!-- ───── 3.  Voucher form (POSTs back to same file) ────────── -->
<form method="POST" id="tourVoucherForm">

  <!-- Header -->
  <div class="form-row">
    <div class="form-group col-md-6">
      <label><i class="fas fa-hashtag mr-1 text-muted"></i>Voucher No</label>
      <input type="text" class="form-control" name="voucher_no" required>
    </div>
    <div class="form-group col-md-6">
      <label><i class="fas fa-building mr-1 text-muted"></i>Company Name</label>
      <input type="text" class="form-control" name="company_name" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label>Customer Phone</label>
      <input type="tel" class="form-control" name="customer_phone" required>
    </div>
    <div class="form-group col-md-6">
      <label>Hotel Name</label>
      <input type="text" class="form-control" name="hotel_name" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-4">
      <label>Adult</label>
      <input type="number" class="form-control" name="adult" min="0" value="0" required>
    </div>
    <div class="form-group col-md-4">
      <label>Child</label>
      <input type="number" class="form-control" name="child" min="0" value="0" required>
    </div>
    <div class="form-group col-md-4">
      <label>Infant</label>
      <input type="number" class="form-control" name="infant" min="0" value="0" required>
    </div>
  </div>

  <!-- Tours -->
  <h4 class="mt-4">Tours</h4>
  <div id="tours">
    <div class="tour-info">
      <div class="form-row">
        <div class="form-group col-md-6">
          <label>Tour Name</label>
          <input type="text" class="form-control" name="tour_name[]" required>
        </div>
        <div class="form-group col-md-3">
          <label>Tour Date</label>
          <input type="date" class="form-control" name="tour_date[]" required>
        </div>
        <div class="form-group col-md-3">
          <label>Duration</label>
          <input type="text" class="form-control" name="tour_duration[]" required>
        </div>
      </div>
    </div>
  </div>
  <button type="button" class="btn btn-secondary mb-3" onclick="addTour()">Add Tour</button>

  <!-- Customers -->
  <h4>Customers</h4>
  <div id="customers">
    <div class="customer-info">
      <div class="form-group">
        <label>Name</label>
        <input type="text" class="form-control" name="customer_name[]" required>
      </div>
    </div>
  </div>
  <button type="button" class="btn btn-secondary mb-3" onclick="addCustomer()">Add Customer</button>

  <div class="d-flex justify-content-between mt-4">
    <a href="admin.php" class="btn btn-outline-secondary"><i class="fas fa-times mr-1"></i>Cancel</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-file-alt mr-1"></i>Save &amp; Generate PDF</button>
  </div>
</form>

        </div>
    </div>
</div>

<!-- ───── 4.  JS helpers to clone blocks ───────────────────── -->
<script>
function addTour(){
  const div=document.createElement('div');
  div.className='tour-info';
  div.innerHTML=document.querySelector('.tour-info').innerHTML+
       '<button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.parentNode.remove()">Remove Tour</button>';
  document.getElementById('tours').appendChild(div);
}
function addCustomer(){
  const div=document.createElement('div');
  div.className='customer-info';
  div.innerHTML=document.querySelector('.customer-info').innerHTML+
       '<button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.parentNode.remove()">Remove Customer</button>';
  document.getElementById('customers').appendChild(div);
}
</script>
</body>
</html>
