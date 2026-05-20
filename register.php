<?php
include 'db.php';

if (isset($_POST['register'])){

$email = $_POST['email'];
$password = $_POST['password'];
$name = $_POST['name'];
$accountnum = $_POST['accountnum'];
$address = $_POST['address'];

$token = md5(rand().time());

$duplicate = mysqli_query($conn,
"SELECT * FROM users WHERE accountnum='$accountnum'");

if(mysqli_num_rows($duplicate) > 0){
echo "<script>alert('Account already registered')</script>";
exit();
}

$emailCheck = mysqli_query($conn,
"SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($emailCheck) > 0){
echo "<script>alert('Email already registered')</script>";
exit();
}

    $otp = rand(100000, 999999);
    $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    $sql = "INSERT INTO users 
    (email,password,name,accountnum,address,is_verified,otp_code,otp_expiry)
    VALUES 
    ('$email','$password','$name','$accountnum','$address',0,'$otp','$expiry')";

    mysqli_query($conn,$sql);
    
    echo "<script>
    alert('Your OTP is: $otp');
    window.location='verify_otp.php?email=$email';
    </script>";
    exit();


$verify_link = "http://testinglang.ap-southeast-2.elasticbeanstalk.com/verify.php?token=$token";

$subject = "Verify your account";
$message = "Click link to verify account: $verify_link";

$headers = "From: noreply@primewater.com";

mail($email,$subject,$message,$headers);

echo "<script>
alert('Registration successful. Check your email to verify account');
window.location='login.php';
</script>";
}
?>