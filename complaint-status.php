<?php
include 'db.php';
if (isset($_POST['update'])){
    $status =  $_POST['status'];
   
    


$sql = "UPDATE INTO complaint (status)
VALUES ('$status')";


if ($conn->query($sql) === TRUE) {
    echo '<script language="javascript">';
    echo 'alert("Complaint Updated")';
    echo '</script>';
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
}
$conn->close();
?>