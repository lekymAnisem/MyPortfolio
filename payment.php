<!DOCTYPE html>
<?php
require_once('db.php');
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['accountnum'])) { header('Location: login.php'); exit(); }
if (!isset($_GET['id'])) { echo "No bill specified"; exit(); }
$id = intval($_GET['id']);
$accountnum = $_SESSION['accountnum'];
$sql = "SELECT * FROM customer WHERE cust_id = $id AND cust_account = '$accountnum' LIMIT 1";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) { echo "Bill not found."; exit(); }
$bill = $result->fetch_assoc();
$pay_status = isset($bill['pay_status']) ? $bill['pay_status'] : 'unpaid';
if ($pay_status === 'paid') { echo "<script>alert('This bill is already paid.'); window.location='bill.php?id=$id';</script>"; exit(); }
$payment_success = false;
$reference_no = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $card_name = trim($_POST['card_name']);
    $card_number = str_replace(' ', '', trim($_POST['card_number']));
    $card_expiry = trim($_POST['card_expiry']);
    $card_cvv = trim($_POST['card_cvv']);
    if (empty($card_name) || empty($card_number) || empty($card_expiry) || empty($card_cvv)) {
        $error = 'Please fill in all card details.';
    } elseif (strlen($card_number) < 13) { $error = 'Invalid card number.'; }
    elseif (strlen($card_cvv) < 3) { $error = 'Invalid CVV.'; }
    else {
        $reference_no = 'PAY-' . strtoupper(uniqid());
        $amount = $bill['amount'];
        $stmt = $conn->prepare("INSERT INTO payments (cust_id, cust_account, amount, payment_method, status, reference_no) VALUES (?, ?, ?, 'Card', 'completed', ?)");
        $stmt->bind_param("isds", $id, $accountnum, $amount, $reference_no);
        if ($stmt->execute()) {
            $update = $conn->prepare("UPDATE customer SET pay_status = 'paid' WHERE cust_id = ?");
            $update->bind_param("i", $id);
            $update->execute();
            $payment_success = true;
        } else { $error = 'Payment processing failed. Please try again.'; }
        $stmt->close();
    }
}
?>
<html>
<head>
    <title>Pay Bill - PrimeWater Quezon Metro</title>
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
    <h2><?php echo $payment_success ? 'Payment Successful' : 'Pay Online'; ?></h2>
    <p><?php echo $payment_success ? 'Your payment has been processed' : 'Complete your payment securely'; ?></p>
</section>

<section class="content-section">
    <div class="form-container" style="max-width:600px;">

<?php if ($payment_success): ?>

        <div style="text-align:center;padding:20px 0;">
            <div style="font-size:64px;color:#10B981;margin-bottom:15px;"><i class="fas fa-check-circle"></i></div>
            <h3 style="color:#10B981;margin-bottom:10px;">Payment Successful!</h3>
            <div style="background:var(--gray-50);padding:20px;border-radius:8px;margin:20px 0;text-align:left;">
                <p style="margin-bottom:8px;font-size:14px;"><strong>Reference No:</strong> <?php echo $reference_no; ?></p>
                <p style="margin-bottom:8px;font-size:14px;"><strong>Amount Paid:</strong> &#8369;<?php echo number_format($bill['amount'], 2); ?></p>
                <p style="font-size:14px;"><strong>Billing Month:</strong> <?php echo $bill['billing_month']; ?></p>
            </div>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <a href="bill.php?id=<?php echo $id; ?>" class="btn btn-primary"><i class="fas fa-file-invoice"></i> View Receipt</a>
                <a href="mybill.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Bills</a>
            </div>
        </div>

<?php else: ?>

        <div style="background:var(--gray-50);padding:24px;border-radius:8px;margin-bottom:28px;">
            <h4 style="margin-bottom:16px;color:var(--primary);font-weight:600;"><i class="fas fa-file-invoice-dollar"></i> Bill Summary</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:14px;">
                <span style="color:var(--gray-500);">Account:</span><span style="font-weight:500;"><?php echo $bill['cust_account']; ?></span>
                <span style="color:var(--gray-500);">Period:</span><span style="font-weight:500;"><?php echo $bill['billing_month']; ?></span>
                <span style="color:var(--gray-500);">Due Date:</span><span style="font-weight:500;"><?php echo $bill['due_date']; ?></span>
                <span style="color:var(--gray-500);">Amount Due:</span><span style="font-size:22px;font-weight:700;color:var(--primary);">&#8369;<?php echo number_format($bill['amount'], 2); ?></span>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Cardholder Name</label>
                <input type="text" name="card_name" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-credit-card"></i> Card Number</label>
                <input type="text" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required oninput="this.value=this.value.replace(/[^0-9 ]/g,'').replace(/(.{4})/g,'$1 ').trim()">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Expiry Date</label>
                    <input type="text" name="card_expiry" placeholder="MM/YY" maxlength="5" required oninput="this.value=this.value.replace(/[^0-9/]/g,'')">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> CVV</label>
                    <input type="text" name="card_cvv" placeholder="123" maxlength="4" required oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="pay" class="btn btn-accent btn-lg" style="width:100%;justify-content:center;"><i class="fas fa-lock"></i> Pay &#8369;<?php echo number_format($bill['amount'], 2); ?></button>
                <a href="bill.php?id=<?php echo $id; ?>" style="display:inline-block;margin-top:12px;font-size:13px;color:var(--gray-400);">Cancel</a>
            </div>
        </form>

<?php endif; ?>

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
