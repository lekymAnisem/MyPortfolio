<?php
require_once('db.php');
session_start();

if (isset($_POST['loginAdmin'])){
    $user = ($_POST['user']);
    $password = ( $_POST['password']);
    
   
   


$sql = "SELECT * from admin where user='$user' and password='$password'";
$result =mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
  	  $_SESSION['user'] =$_POST['user'];
  	
       
            header('location: admin.php');
        } 
        else
        {
            echo '<script language="javascript">';
            echo 'alert("Wrong Email and Password")';
            echo '</script>';
        }
    }


?>