<?php
session_start(); 
// Pastikan setelah login Anda sudah set session, misalnya:
// $_SESSION['username'] = $row['username'];
// $_SESSION['role'] = $row['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV. Amarta Wisesa</title>
    <link rel="icon" href="img/amarta-logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Navbar Top -->
    <?php include 'navbar.php'; ?>

    <!-- Sidebar Navigation -->
    <?php include 'sidebar.php';?> 