<?php
include 'includes/config.php';

// Proses tambah persiapan
if (isset($_POST['no_sps'])) {
    $no_sps = mysqli_real_escape_string($conn, $_POST['no_sps']);
    $nama_buyer = mysqli_real_escape_string($conn, $_POST['nama_buyer']);
    $style = mysqli_real_escape_string($conn, $_POST['style']);
    $tanggal_persiapan = $_POST['tanggal_persiapan'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $query = "INSERT INTO persiapan (no_sps, nama_buyer, style, tanggal_persiapan, status, created_at) 
              VALUES ('$no_sps', '$nama_buyer', '$style', '$tanggal_persiapan', '$status', NOW())";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data persiapan berhasil ditambahkan";
    } else {
        $_SESSION['error'] = "Gagal menambahkan data persiapan";
    }
    
    header("Location: persiapan.php");
    exit();
}

// Proses hapus persiapan
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    
    $query = "DELETE FROM persiapan WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data persiapan berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus data persiapan";
    }
    
    header("Location: persiapan.php");
    exit();
}

// Proses update persiapan
if (isset($_POST['update_persiapan'])) {
    $id = intval($_POST['id']);
    $no_sps = mysqli_real_escape_string($conn, $_POST['no_sps']);
    $nama_buyer = mysqli_real_escape_string($conn, $_POST['nama_buyer']);
    $style = mysqli_real_escape_string($conn, $_POST['style']);
    $tanggal_persiapan = $_POST['tanggal_persiapan'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $query = "UPDATE persiapan SET 
              no_sps = '$no_sps',
              nama_buyer = '$nama_buyer',
              style = '$style',
              tanggal_persiapan = '$tanggal_persiapan',
              status = '$status',
              updated_at = NOW()
              WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data persiapan berhasil diupdate";
    } else {
        $_SESSION['error'] = "Gagal mengupdate data persiapan";
    }
    
    header("Location: persiapan.php");
    exit();
}
?>
