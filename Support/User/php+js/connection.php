<?php
$server = "localhost";
$username = "root";
$password = "";
$database_name = "dreamride";

$conn = mysqli_connect($server, $username, $password, $database_name);

if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}
?>