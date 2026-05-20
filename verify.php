<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Verification - PrimeWater Quezon Metro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#003A70">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/meralco.css">
</head>
<body>

<header class="site-header">
    <div class="container">
        <div class="logo"><a href="index.php"><img src="images/prime.jpg" alt="PrimeWater Quezon Metro"></a></div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Log In</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="page-header">
    <h2>Email Verification</h2>
</section>

<section class="content-section">
    <div class="container">
        <div class="form-container" style="max-width:500px;text-align:center;">
<?php
$token = $_GET['token'];
$sql = "UPDATE users SET is_verified=1, verify_token=NULL WHERE verify_token='$token'";
if(mysqli_query($conn,$sql)){
    echo '<div style="font-size:64px;color:#10B981;margin-bottom:15px;"><i class="fas fa-check-circle"></i></div>';
    echo '<h3 style="color:#10B981;margin-bottom:12px;">Email Verified Successfully!</h3>';
    echo '<p style="color:var(--gray-500);margin-bottom:24px;">You can now login to your account.</p>';
    echo '<a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Log In</a>';
} else {
    echo '<div style="font-size:64px;color:#EF4444;margin-bottom:15px;"><i class="fas fa-exclamation-circle"></i></div>';
    echo '<h3 style="color:#EF4444;margin-bottom:12px;">Invalid Verification Link</h3>';
    echo '<p style="color:var(--gray-500);margin-bottom:24px;">This link is invalid or has expired.</p>';
    echo '<a href="login.php" class="btn btn-primary">Go to Login</a>';
}
?>
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
