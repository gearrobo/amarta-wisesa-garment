<?php
include "config/db.php";

$id = intval($_GET['id']);

// Ambil data mesin untuk konfirmasi
$result = $conn->query("SELECT * FROM mesin WHERE id_mesin = $id");
$data = $result->fetch_assoc();

if ($data) {
    // Hapus data dari database
    $stmt = $conn->prepare("DELETE FROM mesin WHERE id_mesin = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: data-mesin.php?deleted=1");
    } else {
        header("Location: data-mesin.php?error=1");
    }
} else {
    header("Location: data-mesin.php?error=1");
}
?>
