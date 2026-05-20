<?php
include 'db.php';

if(isset($_POST['submit'])){

    $name = $_POST['accountname'];
    $accountnumber = $_POST['accountnum'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $complaint = $_POST['complaint'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO complaint 
    (user_id, name, accountnumber, address, contact, complaint)
    VALUES 
    ('$user_id', '$name','$accountnumber','$address','$contact','$complaint')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Complaint submitted successfully');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
