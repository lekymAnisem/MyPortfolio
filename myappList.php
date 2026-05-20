<!DOCTYPE HTML>
<?php
include('db.php');
session_start();
if (!isset($_SESSION['email'])){ header('Location: usermain.php'); exit(); }
$r = $conn->query("SHOW COLUMNS FROM application LIKE 'app_no'");
if ($r->num_rows == 0) {
    $conn->query("ALTER TABLE application ADD COLUMN app_no VARCHAR(30) AFTER id");
}
$conn->query("UPDATE application SET app_no = CONCAT('PW-', YEAR(date), '-', LPAD(id, 5, '0')) WHERE app_no IS NULL OR app_no = ''");
?>
<html>
<head>
    <title>Application Status - PrimeWater Quezon Metro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="manifest.json">
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
                <li><a href="complaint.php">Complaint</a></li>
                <li><a href="mycomplaint.php">Complaint Status</a></li>
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
    <div class="container">
        <h2>Application Status</h2>
        <p>Track your water connection applications</p>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Application No.</th>
                        <th>Name</th>
                        <th>Date of Application</th>
                        <th>Connection Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql=mysqli_query($conn, "SELECT id, app_no, fname, lname, date, conntype, status from application where user_id='".$_SESSION['user_id']."'");
                while($rows=mysqli_fetch_array($sql))
                {
                ?>
                    <tr>
                        <td><strong><?php echo $rows['app_no'] ?? '—'; ?></strong></td>
                        <td><?php echo $rows['fname']; ?> <?php echo $rows['lname']; ?></td>
                        <td><?php echo $rows['date']; ?></td>
                        <td><?php echo $rows['conntype']; ?></td>
                        <td>
                        <?php
                        $status=$rows['status'];
                        if($status=="For Inspection"){ ?><span class="status-badge status-warning"><i class="fas fa-search"></i> For Inspection</span><?php }
                        if($status=="For Payment") { ?><span class="status-badge status-primary"><i class="fas fa-credit-card"></i> For Payment</span><?php }
                        if($status=="For additional Requirements") { ?><span class="status-badge status-danger"><i class="fas fa-exclamation-circle"></i> For Additional Requirements</span><?php }
                        if($status=="Installed") { ?><span class="status-badge status-success"><i class="fas fa-check-circle"></i> Installed</span><?php } ?>
                        </td>
                        <td>
                            <?php if($status=="For Payment"){ ?>
                                <a href="pay-application.php?id=<?php echo $rows['id']; ?>" class="btn btn-sm btn-accent"><i class="fas fa-credit-card"></i> Pay Now</a>
                            <?php } else { ?>
                                <span class="text-muted" style="font-size:12px;color:#9EA4AE;">--</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
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
