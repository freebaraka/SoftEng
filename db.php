<?php
$host = 'localhost';
$user = 'root';     // change if needed
$pass = '1234';         // change if needed
$dbname = 'SaveEat';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
