<!DOCTYPE HTML>
<?php
session_start();
require_once('complaintdata.php');
if (!isset($_SESSION['email'])){ header('Location: login.php'); exit(); }
$user_address = '';
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $addr_result = $conn->query("SELECT address FROM users WHERE email = '$email' LIMIT 1");
    if ($addr_result && $addr_result->num_rows > 0) {
        $user_address = $addr_result->fetch_assoc()['address'];
    }
}
?>
<html>
<head>
    <title>Complaint - PrimeWater Quezon Metro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="manifest.json">
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
        <div class="util-right"><a href="#">About Us</a><a href="#">CSR</a><a href="#">Careers</a><a href="#">Help & Support</a></div>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <div class="logo"><a href="usermain.php"><img src="images/prime.jpg" alt="PrimeWater Quezon Metro"></a></div>
        <nav class="main-nav">
            <ul>
                <li><a href="usermain.php">Home</a></li>
                <li><a href="application.php">Application</a></li>
                <li class="active"><a href="complaint.php">Complaint</a></li>
                <li><a href="mycomplaint.php">Complaint Status</a></li>
                <li><a href="myappList.php">Application Status</a></li>
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
    <div class="container">
        <h2>Complaint Form</h2>
        <p>Report a water service issue</p>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="form-container">
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $_GET["id"] ?? ''; ?>" />
                <div class="form-row">
                    <div class="form-group">
                        <label for="accountname">Account Name</label>
                        <input type="text" id="accountname" name="accountname" value="<?php echo $_SESSION['name']; ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="accountnum">Account Number</label>
                        <input type="text" id="accountnum" name="accountnum" value="<?php echo $_SESSION['accountnum']; ?>" readonly required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo $user_address; ?>" readonly required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact No.</label>
                    <input type="number" id="contact" name="contact" placeholder="Contact Number" required>
                </div>
                <div class="form-group">
                    <label for="complaint">Complaint</label>
                    <select name="complaint" id="complaint" required>
                        <option selected>Choose...</option>
                        <option>Leaking Pipe</option>
                        <option>High Consumption</option>
                        <option>No Water</option>
                        <option>Dirty Water</option>
                        <option>Defective Meter</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" name="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;"><i class="fas fa-paper-plane"></i> Submit Complaint</button>
                </div>
            </form>
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
