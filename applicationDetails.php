<!DOCTYPE HTML>
<?php 
include_once('db.php');
session_start();
if (!isset($_SESSION['user'])){ header('Location: login.php'); exit(); }
?>
<html>
<head>
    <title>Application Details - PrimeWater Quezon Metro</title>
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
        <div class="logo"><a href="admin.php"><img src="images/prime.jpg" alt="PrimeWater Quezon Metro"></a></div>
        <nav class="main-nav">
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li class="active"><a href="applicationList.php">Applications</a></li>
                <li><a href="complaintList.php">Complaints</a></li>
                <li><a href="billmanage.php">Billing</a></li>
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
        <h2>Application Details</h2>
        <p>View water service application information</p>
    </div>
</section>

<section class="content-section">
    <div class="container">
    <?php
    $id=$_GET['id'];
    $sql=("select * from application where id='$id'");
    $result=$conn-> query($sql);
    while($rows=$result-> fetch_assoc()) {
    ?>
        <div class="form-container" style="max-width:600px;">
            <div style="text-align:center;margin-bottom:24px;">
                <span style="font-size:48px;color:var(--primary);"><i class="fas fa-file-alt"></i></span>
            </div>
            <div class="receipt-row"><span class="label">Application No.</span><span class="value"><strong><?php echo $rows['app_no'] ?? '—'; ?></strong></span></div>
            <div class="receipt-row"><span class="label">First Name</span><span class="value"><?php echo $rows['fname']; ?></span></div>
            <div class="receipt-row"><span class="label">Last Name</span><span class="value"><?php echo $rows['lname']; ?></span></div>
            <div class="receipt-row"><span class="label">Address</span><span class="value"><?php echo $rows['address']; ?></span></div>
            <div class="receipt-row"><span class="label">Contact No.</span><span class="value"><?php echo $rows['contact']; ?></span></div>
            <div class="receipt-row"><span class="label">Occupation</span><span class="value"><?php echo $rows['occupation']; ?></span></div>
            <div class="receipt-row"><span class="label">Birthday</span><span class="value"><?php echo $rows['bday']; ?></span></div>
            <div class="receipt-row"><span class="label">Classification</span><span class="value"><?php echo $rows['class']; ?></span></div>
            <div class="receipt-row"><span class="label">Status</span><span class="value"><span class="status-badge status-info"><?php echo $rows['status']; ?></span></span></div>
            <div class="form-actions">
                <a href="applicationList.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back to List</a>
            </div>
        </div>
    <?php } ?>
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
