<?php
require_once('db.php');

$sql1 = "CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    cust_id INT NOT NULL,
    cust_account VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'Card',
    status VARCHAR(20) NOT NULL DEFAULT 'completed',
    reference_no VARCHAR(50) NOT NULL
)";

if ($conn->query($sql1) === TRUE) {
    echo "payments table created successfully.<br>";
} else {
    echo "Error creating payments table: " . $conn->error . "<br>";
}

$result = $conn->query("SHOW COLUMNS FROM customer LIKE 'pay_status'");
if ($result->num_rows == 0) {
    $sql2 = "ALTER TABLE customer ADD COLUMN pay_status VARCHAR(20) NOT NULL DEFAULT 'unpaid'";
    if ($conn->query($sql2) === TRUE) {
        echo "pay_status column added to customer table.<br>";
    } else {
        echo "Error adding pay_status: " . $conn->error . "<br>";
    }
} else {
    echo "pay_status column already exists.<br>";
}

echo "<br>Setup complete. <a href='index.php'>Go to Home</a>";
?>
