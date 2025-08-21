<?php
$host = 'localhost';
$username = 'root';
$password = '';
$db_name = 'db_amarta_wisesa';
$socket = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';

$conn = mysqli_connect($host, $username, $password, $db_name, null, $socket);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
