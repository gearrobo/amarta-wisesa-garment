<?php
$host = '127.0.0.1';
$username = 'root';
$password = '';
$db_name = 'db_amarta_wisesa';

// Remove socket parameter for Windows compatibility
$conn = mysqli_connect($host, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
