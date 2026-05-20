<!DOCTYPE HTML>
<?php
include('db.php');
session_start();
if (!isset($_SESSION['user'])){ header('Location: adminLogin.php'); exit(); }
?>
<html>
<head>
    <title>Complaints - PrimeWater Quezon Metro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <h2>Complaint List</h2>
        <p>Track and resolve customer complaints</p>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <form class="filter-form" action="#" method="post">
            <div class="form-row">
                <div class="form-group">
                    <label>Complaint</label>
                    <select name="complaint">
                        <option>Select</option>
                        <option value="High Consumption">High Consumption</option>
                        <option value="Leaking Pipe">Leaking Pipe</option>
                        <option value="Dirty Water">Dirty Water</option>
                        <option value="No Water">No Water</option>
                        <option value="Defective Meter">Defective Meter</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option>Select</option>
                        <option value="On Process">On Process</option>
                        <option value="Accomplished">Accomplished</option>
                        <option value="Schedule for Maintenance">Schedule for Maintenance</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>From</label>
                    <input type="date" name="from_date">
                </div>
                <div class="form-group">
                    <label>To</label>
                    <input type="date" name="to_date">
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;">
                    <input type="submit" name="submit" class="btn btn-primary" value="Filter">
                </div>
            </div>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Complaint</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if(!isset($_POST['submit']))
                {
                    $query ="select id, name, complaint, date, status, remarks from complaint";
                    $result =$conn->query($query);
                    if(mysqli_num_rows($result) >0) {
                        foreach($result as $rows) { ?>
                            <tr>
                                <td><?php echo $rows['name']; ?></td>
                                <td><?php echo $rows['complaint']; ?></td>
                                <td><?php echo $rows['date']; ?></td>
                                <td><span class="status-badge status-warning"><?php echo $rows['status']; ?></span></td>
                                <td><?php echo !empty($rows['remarks']) ? '<span title="'.$rows['remarks'].'">'.substr($rows['remarks'], 0, 20).'...</span>' : '<em style="color:#888;">-</em>'; ?></td>
                                <td><a href="complaintDetails.php?id=<?php echo $rows['id']; ?>"><i class="fas fa-eye"></i> View</a></td>
                                <td><a href="complaint-update.php?id=<?php echo $rows['id']; ?>" class="btn btn-sm btn-primary">Update</a></td>
                            </tr>
                        <?php }
                    }
                }
                else{
                    $complaint =$_POST['complaint'];
                    $status =$_POST['status'];
                    $start_date = $_POST['from_date'];
                    $last_date =$_POST['to_date'];
                    $conditions = [];
                    if (!empty($status)) $conditions[] = "status = '$status'";
                    if (!empty($start_date) && !empty($last_date)) $conditions[] = "date BETWEEN '$start_date' AND '$last_date'";
                    if (!empty($complaint)) $conditions[] = "complaint = '$complaint'";
                    $where = count($conditions) > 0 ? implode(' OR ', $conditions) : '1';
                    $query = "SELECT * FROM complaint WHERE $where";
                    $result = $conn->query($query);
                    if(mysqli_num_rows($result) >0) {
                        foreach($result as $rows) { ?>
                            <tr>
                                <td><?php echo $rows['name']; ?></td>
                                <td><?php echo $rows['complaint']; ?></td>
                                <td><?php echo $rows['date']; ?></td>
                                <td><span class="status-badge status-warning"><?php echo $rows['status']; ?></span></td>
                                <td><?php echo !empty($rows['remarks']) ? '<span title="'.$rows['remarks'].'">'.substr($rows['remarks'], 0, 20).'...</span>' : '<em style="color:#888;">-</em>'; ?></td>
                                <td><a href="complaintDetails.php?id=<?php echo $rows['id']; ?>"><i class="fas fa-eye"></i> View</a></td>
                                <td><a href="complaint-update.php?id=<?php echo $rows['id']; ?>" class="btn btn-sm btn-primary">Update</a></td>
                            </tr>
                        <?php }
                    }
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <a href="export.php" class="btn btn-outline" target="_blank"><i class="fas fa-file-excel"></i> Export to Excel</a>
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
