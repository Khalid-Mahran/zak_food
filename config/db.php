<?php
$host = 'localhost';
$user = 'zakuser';
$password = 'zakpass123';
$dbname = 'zak_market';

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
