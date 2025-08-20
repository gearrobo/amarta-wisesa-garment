<?php
// db.php
include "includes/header.php";
include "config/db.php";

// Cek apakah ID tersedia
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'ID inventory tidak valid!';
    header("Location: inventory.php");
    exit();
}

$id = intval($_GET['id']);

// Hapus data inventory
$sql = "DELETE FROM inventory WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Data inventory berhasil dihapus!';
    header("Location: inventory.php?deleted=1");
    exit();
} else {
    $_SESSION['error'] = 'Gagal menghapus data inventory!';
    header("Location: inventory.php");
    exit();
}

$stmt->close();
$conn->close();
?>
