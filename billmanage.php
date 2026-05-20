<!DOCTYPE HTML>
<?php
include('db.php');
session_start();
if (!isset($_SESSION['user'])){ header('Location: adminLogin.php'); exit(); }

if(isset($_POST['add'])){
    $cust_name = $_POST['cust_name'];
    $cust_account = $_POST['cust_account'];
    $cust_address = $_POST['cust_address'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
    $billing_month = $_POST['billing_month'];
    $sql = "INSERT INTO customer (cust_name,cust_account,cust_address,amount,due_date,billing_month) VALUES ('$cust_name','$cust_account','$cust_address','$amount','$due_date','$billing_month')";
    mysqli_query($conn,$sql);
    echo "<script>alert('Inserted Successfully');</script>";
}

if (isset($_POST['upload_csv']) && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
    $uploaded = 0; $errors = 0;
    $tmp = $_FILES['csv_file']['tmp_name'];
    if (($handle = fopen($tmp, 'r')) !== false) {
        $header = fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 6) { $errors++; continue; }
            $name = $conn->real_escape_string(trim($data[0]));
            $account = $conn->real_escape_string(trim($data[1]));
            $address = $conn->real_escape_string(trim($data[2]));
            $amount = floatval($data[3]);
            $due_raw = trim($data[4]);
            $due_parts = preg_split('/[\/\-.]/', $due_raw);
            if (count($due_parts) === 3) {
                $due = strlen($due_parts[0]) === 4 ? $due_parts[0] . '-' . $due_parts[1] . '-' . $due_parts[2] : $due_parts[2] . '-' . $due_parts[0] . '-' . $due_parts[1];
            } else { $due = $due_raw; }
            $due = $conn->real_escape_string($due);
            $month = $conn->real_escape_string(trim($data[5]));
            $sql = "INSERT INTO customer (cust_name,cust_account,cust_address,amount,due_date,billing_month) VALUES ('$name','$account','$address',$amount,'$due','$month')";
            if ($conn->query($sql)) { $uploaded++; } else { $errors++; }
        }
        fclose($handle);
    }
    echo "<script>alert('Upload complete: $uploaded bills added, $errors failed.');</script>";
}
?>
<html>
<head>
    <title>Billing - PrimeWater Quezon Metro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#003A70">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/meralco.css">
</head>
<body>

<div class="top-utility">
    <div class="container">
        <div class="util-left"><a href="index.php"><i class="fas fa-arrow-left"></i> Return to PrimeWater.ph</a></div>
        <div class="util-right"><a href="#">About</a><a href="#">Contact</a></div>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <div class="logo"><a href="admin.php"><img src="images/prime.jpg" alt="PrimeWater Quezon Metro"></a></div>
        <nav class="main-nav">
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="applicationList.php">Applications</a></li>
                <li><a href="complaintList.php">Complaints</a></li>
                <li class="active"><a href="billmanage.php">Billing</a></li>
                <li><a href="allbill.php">All Bills</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <span class="welcome-msg"><i class="fas fa-user-shield"></i> <?php echo $_SESSION['user']; ?></span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</header>

<section class="page-header">
    <div class="container">
        <h2>Customer Billing Management</h2>
        <p>Add new water bills for customers</p>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="cards-grid" style="max-width:800px;margin:0 auto;">
            <div class="form-container">
                <h2>Add New Bill</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" name="cust_name" placeholder="Customer Name" required>
                        </div>
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="cust_account" placeholder="Account Number" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="cust_address" placeholder="Address" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="0.01" name="amount" placeholder="Amount" required>
                        </div>
                        <div class="form-group">
                            <label>Due Date</label>
                            <input type="date" name="due_date" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Billing Month</label>
                        <input type="text" name="billing_month" placeholder="Billing Month" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="add" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;"><i class="fas fa-plus-circle"></i> Add Customer Bill</button>
                    </div>
                </form>
            </div>

            <hr style="margin:40px 0;border-color:var(--gray-200);">

            <div class="form-container">
                <h2>Bulk Upload via CSV</h2>
                <p style="font-size:13px;color:var(--gray-500);margin-bottom:20px;text-align:center;">Upload a CSV file with columns: Customer Name, Account Number, Address, Amount, Due Date, Billing Month</p>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>CSV File</label>
                        <input type="file" name="csv_file" accept=".csv" required style="padding:8px;">
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="upload_csv" class="btn btn-accent btn-lg" style="width:100%;justify-content:center;"><i class="fas fa-upload"></i> Upload CSV</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-logo">Prime<span>Water</span></div>
                <p>Providing potable, reliable, and sustainable water to Filipino communities.</p>
                <p>PrimeWater Building, Quezon City, Philippines</p>
                <div class="footer-hotline"><i class="fas fa-phone-alt"></i> Hotline: 1626</div>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Customer Support</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Service Advisories</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> How to Read your Water Bill</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Bills Payment Partners</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> FAQs</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>About PrimeWater</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> About Us</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Business Profile</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Management</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Sustainability Reports</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2024 PrimeWater Quezon Metro. All rights reserved.
        </div>
    </div>
</footer>

</body>
</html>
