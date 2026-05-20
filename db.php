<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "prime2";
$port = 8889;

$conn = new mysqli("127.0.0.1", $username, $password, $dbname, $port);

if ($conn->connect_error) {
    echo "DB ERROR: " . $conn->connect_error;
}

$conn->query("SET time_zone = '+00:00'");
?>
