<!DOCTYPE HTML>
<?php
include('db.php');
$chk = $conn->query("SHOW COLUMNS FROM customer LIKE 'pay_status'");
if ($chk->num_rows == 0) {
    $conn->query("ALTER TABLE customer ADD COLUMN pay_status VARCHAR(20) NOT NULL DEFAULT 'unpaid'");
    $conn->query("CREATE TABLE IF NOT EXISTS payments (
        payment_id INT AUTO_INCREMENT PRIMARY KEY,
        cust_id INT NOT NULL,
        cust_account VARCHAR(50) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        payment_method VARCHAR(50) NOT NULL DEFAULT 'Card',
        status VARCHAR(20) NOT NULL DEFAULT 'completed',
        reference_no VARCHAR(50) NOT NULL
    )");
}
session_start();
if (!isset($_SESSION['user'])){ header('Location: adminLogin.php'); exit(); }

if (isset($_POST['mark_paid_ajax'])) {
    $cid = intval($_POST['cust_id']);
    $ref = 'ADMIN-' . strtoupper(uniqid());
    header('Content-Type: application/json');
    $bill = $conn->query("SELECT * FROM customer WHERE cust_id = $cid LIMIT 1")->fetch_assoc();
    if ($bill) {
        $conn->query("UPDATE customer SET pay_status = 'paid' WHERE cust_id = $cid");
        $conn->query("INSERT INTO payments (cust_id, cust_account, amount, payment_method, status, reference_no) VALUES ($cid, '{$bill['cust_account']}', {$bill['amount']}, 'Manual', 'completed', '$ref')");
        echo json_encode(['ok' => true, 'ref' => $ref]);
    } else { echo json_encode(['ok' => false]); }
    exit();
}
?>
<html>
<head>
    <title>All Bills - PrimeWater Quezon Metro</title>
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
                <li><a href="complaintList.php">Complaints</a></li>
                <li><a href="billmanage.php">Billing</a></li>
                <li class="active"><a href="allbill.php">All Bills</a></li>
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
        <h2>Customer Billing</h2>
        <p>View all billing records</p>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="mb-4">
            <form method="GET" class="form-inline">
                <label class="mr-2">Filter by Month:</label>
                <select name="billing_month" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="">All Months</option>
                    <?php
                    $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                    $current = isset($_GET['billing_month']) ? $_GET['billing_month'] : '';
                    foreach($months as $m) {
                        $sel = ($current === $m) ? 'selected' : '';
                        echo "<option value=\"$m\" $sel>$m</option>";
                    }
                    ?>
                </select>
            </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Account</th>
                        <th>Address</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Month</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $filter = isset($_GET['billing_month']) && $_GET['billing_month'] !== '' ? "WHERE billing_month = '".$conn->real_escape_string($_GET['billing_month'])."'" : '';
                $query = "SELECT * FROM customer $filter ORDER BY cust_id DESC";
                $result = $conn->query($query);
                while($row = $result->fetch_assoc()){
                    $st = isset($row['pay_status']) ? $row['pay_status'] : 'unpaid';
                ?>
                    <tr>
                        <td><?php echo $row['cust_name']; ?></td>
                        <td><?php echo $row['cust_account']; ?></td>
                        <td><?php echo $row['cust_address']; ?></td>
                        <td><strong>&#8369;<?php echo number_format($row['amount'], 2); ?></strong></td>
                        <td><?php echo $row['due_date']; ?></td>
                        <td><?php echo $row['billing_month']; ?></td>
                        <td><span class="status-badge <?php echo $st === 'paid' ? 'status-success' : 'status-warning'; ?>"><?php echo $st === 'paid' ? '<i class="fas fa-check-circle"></i> PAID' : '<i class="fas fa-clock"></i> UNPAID'; ?></span></td>
                        <td>
                            <?php if ($st === 'unpaid'): ?>
                            <button onclick="markPaid(<?php echo $row['cust_id']; ?>, this)" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Mark Paid</button>
                            <?php else: ?>
                            <span style="color:#10B981;"><i class="fas fa-check-circle"></i></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="form-actions">
            <a href="export2.php" class="btn btn-outline" target="_blank"><i class="fas fa-file-excel"></i> Export to Excel</a>
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

<script>
function markPaid(id, btn) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'allbill.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        var res = JSON.parse(xhr.responseText);
        if (res.ok) {
            btn.outerHTML = '<span style="color:#10B981;"><i class="fas fa-check-circle"></i></span>';
            var badge = btn.closest('tr').querySelector('.status-badge');
            badge.className = 'status-badge status-success';
            badge.innerHTML = '<i class="fas fa-check-circle"></i> PAID';
        } else { alert('Error marking bill as paid.'); }
    };
    xhr.send('mark_paid_ajax=1&cust_id=' + id);
}
</script>
</body>
</html>
