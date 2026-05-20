<?php include 'db.php'; $email = $_GET['email']; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP - PrimeWater Quezon Metro</title>
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
    <p>Enter the OTP sent to your email</p>
</section>

<section class="content-section">
    <div class="container">
        <div class="form-container" style="max-width:480px;">
            <form method="POST">
                <div class="form-group">
                    <label for="otp"><i class="fas fa-key"></i> One-Time PIN</label>
                    <input type="text" name="otp" id="otp" placeholder="Enter 6-digit code" maxlength="6" required>
                </div>
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <div class="form-actions">
                    <button name="verify" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;"><i class="fas fa-check-circle"></i> Verify</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
if(isset($_POST['verify'])){
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND otp_code='$otp' AND otp_expiry > NOW()");
    if(mysqli_num_rows($result) > 0){
        mysqli_query($conn, "UPDATE users SET is_verified=1, otp_code=NULL, otp_expiry=NULL WHERE email='$email'");
        echo "<script>alert('Verified Successfully'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Invalid or expired OTP');</script>";
    }
}
?>

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
