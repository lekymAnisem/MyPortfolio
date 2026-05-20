<!DOCTYPE HTML>
<?php 
include('db.php');
session_start();
if (!isset($_SESSION['user'])){ header('Location: adminLogin.php'); exit(); }
?>
<html>
<head>
    <title>Update Complaint - PrimeWater Quezon Metro</title>
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
                <li><a href="applicationList.php">Applications</a></li>
                <li class="active"><a href="complaintList.php">Complaints</a></li>
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
        <h2>Update Complaint</h2>
        <p>Modify complaint status</p>
    </div>
</section>

<section class="content-section">
    <div class="container">
    <?php
    $id=$_GET['id'];
    $sql=("select * from complaint where id='$id'");
    $result=$conn-> query($sql);
    while($rows=$result-> fetch_assoc()) {
    ?>
        <div class="form-container" style="max-width:600px;">
            <h2>Complaint Details</h2>
            <div class="receipt-row"><span class="label">Account Name: </span><span class="value"><?php echo $rows['name']; ?></span></div>
            <div class="receipt-row"><span class="label">Account Number: </span><span class="value"><?php echo $rows['accountnumber']; ?></span></div>
            <div class="receipt-row"><span class="label">Address: </span><span class="value"><?php echo $rows['address']; ?></span></div>
            <div class="receipt-row"><span class="label">Contact No.: </span><span class="value"><?php echo $rows['contact']; ?></span></div>
            <div class="receipt-row"><span class="label">Complaint: </span><span class="value"><?php echo $rows['complaint']; ?></span></div>
            <div class="receipt-row"><span class="label">Remarks: </span><span class="value"><?php echo !empty($rows['remarks']) ? $rows['remarks'] : '<em style="color:#888;">No remarks yet</em>'; ?></span></div>
            <form method="POST" style="margin-top:24px;">
                <div class="form-group">
                    <label for="status1">Update Status</label>
                    <select name="status1" id="status1" required>
                        <option selected>Choose...</option>
                        <option>On Process</option>
                        <option>Schedule for Maintenance</option>
                        <option>Accomplished</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="remarks">Remarks (optional)</label>
                    <textarea name="remarks" id="remarks" rows="3" placeholder="Add remarks for the customer..."><?php echo $rows['remarks']; ?></textarea>
                </div>
                <div class="form-actions" style="display:flex;gap:12px;justify-content:center;">
                    <button type="submit" name="update" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <a href="complaintList.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
            <?php
            if(isset($_POST['update'])) {
                $status = $_POST['status1'];
                $remarks = $_POST['remarks'];
                $stmt = $conn->prepare("UPDATE complaint SET status=?, remarks=? WHERE id=?");
                $stmt->bind_param("ssi", $status, $remarks, $id);
                $query_run = $stmt->execute();
                if($query_run) { echo '<script> alert("Status Updated"); window.location="complaintList.php";</script>'; }
                else { echo '<script> alert("Status Not Updated")</script>'; }
            }
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
