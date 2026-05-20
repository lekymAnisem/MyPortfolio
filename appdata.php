<?php
session_start();
include 'db.php';

$r = $conn->query("SHOW COLUMNS FROM application LIKE 'app_no'");
if ($r->num_rows == 0) {
    $conn->query("ALTER TABLE application ADD COLUMN app_no VARCHAR(30) AFTER id");
}

if (isset($_POST['apply'])){

    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $occupation = trim($_POST['occupation']);
    $bday = $_POST['bday'];
    $classification = $_POST['classification'];
    $connection = $_POST['connection'];

    $errors = array();
    if ($fname == '') $errors[] = 'First Name';
    if ($lname == '') $errors[] = 'Last Name';
    if ($address == '') $errors[] = 'Address';
    if ($contact == '') $errors[] = 'Contact No.';
    if ($occupation == '') $errors[] = 'Occupation';
    if ($bday == '') $errors[] = 'Birthday';
    if ($classification == '' || $classification == 'Choose...') $errors[] = 'Classification';
    if ($connection == '' || $connection == 'Choose...') $errors[] = 'Type of Connection';

    if (!empty($errors)) {
        echo "<script>alert('Please fill in the following fields:\\n- " . implode("\\n- ", $errors) . "');</script>";
        exit();
    }

    $last = $conn->query("SELECT MAX(id) AS max_id FROM application");
    $last_row = $last->fetch_assoc();
    $next_num = ($last_row['max_id'] ?? 0) + 1;
    $app_no = 'PW-' . date('Y') . '-' . str_pad($next_num, 5, '0', STR_PAD_LEFT);

    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO application 
    (app_no, user_id, fname, lname, address, contact, occupation, bday, class, conntype)
    VALUES 
    ('$app_no', '$user_id', '$fname', '$lname', '$address', '$contact', '$occupation', '$bday', '$classification', '$connection')";

    if ($conn->query($sql) === TRUE) {
        $app_id = $conn->insert_id;
        if (isset($_FILES['files']) && $_FILES['files']['error'] == 0) {
            $ext = pathinfo($_FILES['files']['name'], PATHINFO_EXTENSION);
            $new_name = $fname . '_' . $lname . '.' . $ext;
            $file_store = "upload/" . $new_name;
            move_uploaded_file($_FILES['files']['tmp_name'], $file_store);
        }
        echo "<script>alert('Application Submitted Successfully. Your App No: $app_no');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
