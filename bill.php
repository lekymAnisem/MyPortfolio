<!DOCTYPE html>
<?php
include('db.php');
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['accountnum'])) { header('Location: login.php'); exit(); }
$accountnum = $_SESSION['accountnum'];
if (!isset($_GET['id'])) { echo "No bill selected"; exit(); }
$id = intval($_GET['id']);
$sql = "SELECT * FROM customer WHERE cust_id = $id AND cust_account = '$accountnum' LIMIT 1";
$result = $conn->query($sql);
if (!$result) { die("Database Error: " . $conn->error); }
if ($result->num_rows == 0) { echo "No bill found for your account."; exit(); }
$rows = $result->fetch_assoc();
$pay_status = isset($rows['pay_status']) ? $rows['pay_status'] : 'unpaid';
$payment_info = null;
if ($pay_status === 'paid') {
    $p_result = $conn->query("SELECT * FROM payments WHERE cust_id = $id ORDER BY payment_id DESC LIMIT 1");
    if ($p_result && $p_result->num_rows > 0) { $payment_info = $p_result->fetch_assoc(); }
}
?>
<html>
<head>
    <title>Bill Receipt - PrimeWater Quezon Metro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#003A70">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
        <div class="logo"><a href="usermain.php"><img src="images/prime.jpg" alt="PrimeWater Quezon Metro"></a></div>
        <nav class="main-nav">
            <ul>
                <li><a href="usermain.php">Home</a></li>
                <li><a href="application.php">Application</a></li>
                <li><a href="complaint.php">Complaint</a></li>
                <li><a href="mycomplaint.php">Complaint Status</a></li>
                <li><a href="myappList.php">Application Status</a></li>
                <li class="active"><a href="mybill.php">Bill</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <span class="welcome-msg"><i class="fas fa-user-circle"></i> <?php echo $_SESSION['email']; ?></span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</header>

<section class="page-header">
    <h2>Bill Receipt</h2>
    <p>View your billing details</p>
</section>

<section class="content-section">
    <div id="receipt" class="receipt-content">
        <h2><img src="images/prime.jpg" alt="PrimeWater" style="height:40px;width:auto;vertical-align:middle;margin-right:10px;"> Bill Receipt</h2>
        <div class="receipt-row"><span class="label">Customer Name</span><span class="value"><?php echo $rows['cust_name']; ?></span></div>
        <div class="receipt-row"><span class="label">Account Number</span><span class="value"><?php echo $rows['cust_account']; ?></span></div>
        <div class="receipt-row"><span class="label">Billing Month</span><span class="value"><?php echo $rows['billing_month']; ?></span></div>
        <div class="receipt-row"><span class="label">Amount Due</span><span class="value" style="font-weight:700;font-size:18px;color:var(--primary);">&#8369;<?php echo number_format($rows['amount'], 2); ?></span></div>
        <div class="receipt-row"><span class="label">Due Date</span><span class="value"><?php echo $rows['due_date']; ?></span></div>
        <div class="receipt-row">
            <span class="label">Status</span>
            <span class="value">
                <span class="status-badge <?php echo $pay_status === 'paid' ? 'status-success' : 'status-warning'; ?>">
                    <?php echo $pay_status === 'paid' ? '<i class="fas fa-check-circle"></i> PAID' : '<i class="fas fa-clock"></i> UNPAID'; ?>
                </span>
            </span>
        </div>
        <?php if ($payment_info): ?>
        <div class="receipt-row"><span class="label">Payment Reference</span><span class="value"><?php echo $payment_info['reference_no']; ?></span></div>
        <div class="receipt-row"><span class="label">Paid On</span><span class="value"><?php echo date('F d, Y h:i A', strtotime($payment_info['payment_date'])); ?></span></div>
        <?php endif; ?>
        <div style="text-align:center;margin-top:28px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <?php if ($pay_status === 'unpaid'): ?>
                <a href="payment.php?id=<?php echo $id; ?>" class="btn btn-accent btn-lg"><i class="fas fa-credit-card"></i> Pay Online</a>
            <?php else: ?>
                <span style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:#D1FAE5;color:#065F46;border-radius:8px;font-weight:600;"><i class="fas fa-check-circle"></i> Paid</span>
            <?php endif; ?>
            <button onclick="printContent('receipt')" class="btn btn-outline"><i class="fas fa-print"></i> Print Receipt</button>
            <a href="mybill.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
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

<script>
function printContent(el) {
    var restorepage = document.body.innerHTML;
    var printcontent = document.getElementById(el).innerHTML;
    document.body.innerHTML = printcontent;
    window.print();
    document.body.innerHTML = restorepage;
}
</script>

</body>
</html>
