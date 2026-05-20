<!DOCTYPE html>
<?php
require_once('db.php');
session_start();
if (!isset($_SESSION['email'])) { header('Location: login.php'); exit(); }
if (!isset($_GET['id'])) { echo "No application specified"; exit(); }
$id = intval($_GET['id']);
$sql = "SELECT * FROM application WHERE id = $id AND user_id = '".$_SESSION['user_id']."' LIMIT 1";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) { echo "Application not found."; exit(); }
$app = $result->fetch_assoc();
if ($app['status'] !== 'For Payment') { echo "<script>alert('This application is not yet ready for payment.'); window.location='myappList.php';</script>"; exit(); }
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
        $reference_no = 'APP-PAY-' . strtoupper(uniqid());
        $conn->query("UPDATE application SET status='Installed' WHERE id=$id");
        $payment_success = true;
    }
}
?>
<html>
<head>
    <title>Pay Application - PrimeWater Quezon Metro</title>
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
                <li class="active"><a href="myappList.php">Application Status</a></li>
                <li><a href="mybill.php">Bill</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <span class="welcome-msg"><i class="fas fa-user-circle"></i> <?php echo $_SESSION['email']; ?></span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</header>

<section class="page-header">
    <h2><?php echo $payment_success ? 'Payment Successful' : 'Pay Application Fee'; ?></h2>
    <p><?php echo $payment_success ? 'Your application payment has been processed' : 'Complete payment for your water connection application'; ?></p>
</section>

<section class="content-section">
    <div class="form-container" style="max-width:600px;">

<?php if ($payment_success): ?>

        <div style="text-align:center;padding:20px 0;">
            <div style="font-size:64px;color:#10B981;margin-bottom:15px;"><i class="fas fa-check-circle"></i></div>
            <h3 style="color:#10B981;margin-bottom:10px;">Payment Successful!</h3>
            <div style="background:var(--gray-50);padding:20px;border-radius:8px;margin:20px 0;text-align:left;">
                <p style="margin-bottom:8px;font-size:14px;"><strong>Reference No:</strong> <?php echo $reference_no; ?></p>
                <p style="margin-bottom:8px;font-size:14px;"><strong>Application No:</strong> <?php echo $app['app_no']; ?></p>
                <p style="margin-bottom:8px;font-size:14px;"><strong>Applicant:</strong> <?php echo $app['fname']; ?> <?php echo $app['lname']; ?></p>
            </div>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <a href="myappList.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back to Applications</a>
            </div>
        </div>

<?php else: ?>

        <div style="background:var(--gray-50);padding:24px;border-radius:8px;margin-bottom:28px;">
            <h4 style="margin-bottom:16px;color:var(--primary);font-weight:600;"><img src="images/prime.jpg" alt="PrimeWater" style="height:30px;width:auto;vertical-align:middle;margin-right:8px;"> Application Summary</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:14px;">
                <span style="color:var(--gray-500);">App No:</span><span style="font-weight:500;"><?php echo $app['app_no']; ?></span>
                <span style="color:var(--gray-500);">Applicant:</span><span style="font-weight:500;"><?php echo $app['fname']; ?> <?php echo $app['lname']; ?></span>
                <span style="color:var(--gray-500);">Connection Type:</span><span style="font-weight:500;"><?php echo $app['conntype']; ?></span>
                <span style="color:var(--gray-500);">Amount Due:</span><span style="font-size:22px;font-weight:700;color:var(--primary);">&#8369;1,500.00</span>
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
                <button type="submit" name="pay" class="btn btn-accent btn-lg" style="width:100%;justify-content:center;"><i class="fas fa-lock"></i> Pay &#8369;1,500.00</button>
                <a href="myappList.php" style="display:inline-block;margin-top:12px;font-size:13px;color:var(--gray-400);">Cancel</a>
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
