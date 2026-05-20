<!DOCTYPE HTML>
<?php
require_once('admindata.php');
if (!isset($_SESSION['user'])){ header('Location: login.php'); exit(); }
?>
<html>
<head>
    <title>Admin Dashboard - PrimeWater Quezon Metro</title>
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
        <div class="util-left">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Return to PrimeWater.ph</a>
        </div>
        <div class="util-right">
            <a href="#">About</a>
            <a href="#">Contact</a>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <div class="logo">
            <a href="admin.php"><img src="images/prime.jpg" alt="PrimeWater Quezon Metro"></a>
        </div>
        <nav class="main-nav">
            <ul>
                <li class="active"><a href="admin.php">Dashboard</a></li>
                <li><a href="#billCalc">Bill Calculator</a></li>
                <li><a href="applicationList.php">Applications</a></li>
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

<section class="hero-carousel" id="hero" style="height:300px;">
    <div class="slide active" style="background-image: url('images/header.jpg');">
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <h2>Admin Dashboard</h2>
            <p>Manage applications, complaints, and billing for PrimeWater Quezon Metro</p>
        </div>
    </div>
    <div class="slide" style="background-image: url('images/header2.jpg');">
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <h2>Application Management</h2>
            <p>Review and process water service applications</p>
        </div>
    </div>
    <div class="slide" style="background-image: url('images/header4.jpg');">
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <h2>Complaint Resolution</h2>
            <p>Track and resolve customer service issues</p>
        </div>
    </div>
    <div class="slide" style="background-image: url('images/header5.jpg');">
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <h2>Billing Management</h2>
            <p>Create and manage customer water bills</p>
        </div>
    </div>
    <div class="slide" style="background-image: url('images/header6.jpg');">
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <h2>Data & Reports</h2>
            <p>Export and analyze billing and service data</p>
        </div>
    </div>
    <div class="slide" style="background-image: url('images/header7.jpg');">
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <h2>PrimeWater Quezon Metro</h2>
            <p>Serving Filipino communities with excellence</p>
        </div>
    </div>
    <div class="indicators">
        <button class="active"></button><button></button><button></button><button></button><button></button><button></button>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="section-title">
            <h2>PrimeWater in Action</h2>
            <p>See how we provide reliable water service to Filipino communities</p>
            <div class="title-bar"></div>
        </div>
        <div class="video-wrapper">
            <iframe src="https://www.youtube.com/embed/0oqw_eHKOKA" title="PrimeWater Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</section>

<section class="content-section light" id="billCalc">
    <div class="container">
        <div class="section-title">
            <h2>Bill Calculator</h2>
            <p>Estimate your water bill — select your rate type and enter consumption</p>
            <div class="title-bar"></div>
        </div>
        <div class="bill-calculator">
            <div class="calc-row">
                <label for="custType"><i class="fas fa-tag"></i> Customer Type</label>
                <select id="custType">
                    <option value="24.25">Residential — ₱24.25 / cu.m.</option>
                    <option value="36.50">Commercial — ₱36.50 / cu.m.</option>
                    <option value="54.75">Bulk/Wholesale — ₱54.75 / cu.m.</option>
                </select>
                <div class="calc-rate-display">Rate: <strong id="rateDisplay">₱24.25</strong> per cubic meter</div>
            </div>
            <div class="calc-row">
                <label for="consumption"><i class="fas fa-tint"></i> Water Consumption (cubic meters)</label>
                <input type="number" id="consumption" min="0" step="0.1" placeholder="e.g. 30">
            </div>
            <button class="calc-btn" onclick="calculateBill()"><i class="fas fa-calculator"></i> Calculate Bill</button>
            <div class="calc-results" id="calcResults" style="display:none;">
                <h3><i class="fas fa-file-invoice"></i> Bill Summary</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="text-align:right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Consumption (<span id="resConsumption">0</span> cu.m.)</td>
                            <td style="text-align:right">₱<span id="resBasic">0.00</span></td>
                        </tr>
                        <tr>
                            <td>VAT (12%)</td>
                            <td style="text-align:right">₱<span id="resVat">0.00</span></td>
                        </tr>
                        <tr class="total-row">
                            <td>Total Amount Due</td>
                            <td style="text-align:right">₱<span id="resTotal">0.00</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="section-title">
            <h2>Admin Panel</h2>
            <p>Manage all aspects of water service operations</p>
            <div class="title-bar"></div>
        </div>
        <div class="cards-grid">
            <div class="card">
                <div class="card-icon"><i class="fas fa-file-alt"></i></div>
                <h3>Application List</h3>
                <p>Review and manage water service applications from residents.</p>
                <a href="applicationList.php" class="btn btn-primary">View Applications</a>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>
                <h3>Complaint List</h3>
                <p>Track and resolve customer complaints and service issues.</p>
                <a href="complaintList.php" class="btn btn-primary">View Complaints</a>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <h3>Billing</h3>
                <p>Create and manage customer water bills.</p>
                <a href="billmanage.php" class="btn btn-primary">Manage Billing</a>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fas fa-database"></i></div>
                <h3>All Bills</h3>
                <p>View complete billing records and history.</p>
                <a href="allbill.php" class="btn btn-primary">View All Bills</a>
            </div>
        </div>
    </div>
</section>

<section class="content-section light">
    <div class="container">
        <div class="section-title">
            <h2>Service Overview</h2>
            <p>PrimeWater Quezon Metro operations</p>
            <div class="title-bar"></div>
        </div>
        <div class="two-col">
            <div class="col">
                <img src="images/pic01.jpg" alt="">
                <h2>Water Service Applications</h2>
                <p>Processing new connections, sub-connections, transfers, and line separations for the Quezon Metro area. Our team ensures timely and efficient service.</p>
            </div>
            <div class="col">
                <img src="images/pic02.jpg" alt="">
                <h2>Customer Support</h2>
                <p>Handling complaints and service requests with prompt attention. We are committed to providing reliable water service to every Filipino community.</p>
            </div>
        </div>
    </div>
</section>

<section class="quote-section">
    <div class="container">
        <blockquote>&ldquo;The PrimeWater Mission: To provide potable, reliable, and sustainable water to Filipino communities.&rdquo;</blockquote>
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
(function(){
    var hero = document.getElementById('hero');
    var slides = hero.querySelectorAll('.slide');
    var indicators = hero.querySelectorAll('.indicators button');
    var current = 0, interval;
    function showSlide(idx) {
        slides.forEach(function(s, i){ s.classList.toggle('active', i === idx); });
        indicators.forEach(function(b, i){ b.classList.toggle('active', i === idx); });
        current = idx;
    }
    function nextSlide() { showSlide((current + 1) % slides.length); }
    function startSlider() { if (slides.length > 1) interval = setInterval(nextSlide, 5000); }
    function stopSlider() { clearInterval(interval); }
    indicators.forEach(function(btn, i){ btn.addEventListener('click', function(){ showSlide(i); stopSlider(); startSlider(); }); });
    if (slides.length) { showSlide(0); startSlider(); hero.addEventListener('mouseenter', stopSlider); hero.addEventListener('mouseleave', startSlider); }
})();

function calculateBill() {
    var rate = parseFloat(document.getElementById('custType').value);
    var consumption = parseFloat(document.getElementById('consumption').value) || 0;
    var basic = rate * consumption;
    var vat = basic * 0.12;
    var total = basic + vat;
    document.getElementById('resConsumption').textContent = consumption.toFixed(1);
    document.getElementById('resBasic').textContent = basic.toFixed(2);
    document.getElementById('resVat').textContent = vat.toFixed(2);
    document.getElementById('resTotal').textContent = total.toFixed(2);
    document.getElementById('calcResults').style.display = 'block';
    document.getElementById('calcResults').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'custType') {
        var rates = {'24.25': '₱24.25', '36.50': '₱36.50', '54.75': '₱54.75'};
        document.getElementById('rateDisplay').textContent = rates[e.target.value] || '₱' + e.target.value;
    }
});
</script>
</body>
</html>
