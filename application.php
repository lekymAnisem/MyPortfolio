<!DOCTYPE HTML>
<?php 
include_once('appdata.php');
if (!isset($_SESSION['email'])){ header('Location: login.php'); exit(); }
?>
<html>
<head>
    <title>Application - PrimeWater Quezon Metro</title>
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
                <li class="active"><a href="application.php">Application</a></li>
                <li><a href="complaint.php">Complaint</a></li>
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
        <h2>Application Form</h2>
        <p>Apply for a new water connection</p>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="form-container">
            <form action="#" method="POST" role="form" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" required>
                    </div>
                    <div class="form-group">
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact">Contact No.</label>
                        <input type="text" id="contact" name="contact" required>
                    </div>
                    <div class="form-group">
                        <label for="occupation">Occupation</label>
                        <input type="text" id="occupation" name="occupation" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="bday">Birthday</label>
                        <input type="date" id="bday" name="bday" required>
                    </div>
                    <div class="form-group">
                        <label for="classification">Classification</label>
                        <select name="classification" id="classification" required>
                            <option selected>Choose...</option>
                            <option>Residential</option>
                            <option>Commercial</option>
                            <option>Bulk</option>
                            <option>Public Faucet</option>
                            <option>Government</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="connection">Type of Connection</label>
                    <select name="connection" id="connection" required>
                        <option selected>Choose...</option>
                        <option>New Connection</option>
                        <option>Sub-Connection</option>
                        <option>Transfer of Tapping</option>
                        <option>Seperation Of Line</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="files">Requirements</label>
                    <input type="file" id="files" name="files" required style="padding:8px;">
                </div>
                <div class="form-actions">
                    <button type="submit" name="apply" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;"><i class="fas fa-paper-plane"></i> Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    var fields = [
        { el: document.getElementById('fname'), label: 'First Name' },
        { el: document.getElementById('lname'), label: 'Last Name' },
        { el: document.getElementById('address'), label: 'Address' },
        { el: document.getElementById('contact'), label: 'Contact No.' },
        { el: document.getElementById('occupation'), label: 'Occupation' },
        { el: document.getElementById('bday'), label: 'Birthday' },
        { el: document.getElementById('classification'), label: 'Classification' },
        { el: document.getElementById('connection'), label: 'Type of Connection' },
        { el: document.getElementById('files'), label: 'Requirements' }
    ];
    var missing = [];
    fields.forEach(function(f) {
        var val = f.el.value ? f.el.value.trim() : '';
        if (f.el.tagName === 'SELECT' && (val === '' || val === 'Choose...')) {
            missing.push(f.label);
            f.el.style.borderColor = '#EF4444';
        } else if (val === '') {
            missing.push(f.label);
            f.el.style.borderColor = '#EF4444';
        } else {
            f.el.style.borderColor = '';
        }
    });
    if (missing.length > 0) {
        e.preventDefault();
        alert('Please fill in the following fields:\n- ' + missing.join('\n- '));
    }
});
document.querySelectorAll('input, select').forEach(function(el) {
    el.addEventListener('change', function() { this.style.borderColor = ''; });
    el.addEventListener('input', function() { this.style.borderColor = ''; });
});
</script>

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
