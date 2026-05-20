<?php
require_once('db.php');
session_start();

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if($user['is_verified'] == 0){
        echo "<script>
        alert('Please verify OTP first');
        window.location='login.php';
        </script>";
        exit();
    }

    if ($user && $password === $user['password']) {

        if ($user['is_verified'] == 0) {
            echo "<script>
            alert('Please verify your email first');
            window.location='login.php';
            </script>";
            exit();
        }

        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['password'] = $user['password'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['accountnum'] = $user['accountnum'];

        header("Location: usermain.php");
        exit();

    } else {
        echo "<script>
        alert('Wrong Password or Email');
        window.location='login.php';
        </script>";
    }

} // ✅ THIS CLOSING BRACE WAS THE PROBLEM
?>
