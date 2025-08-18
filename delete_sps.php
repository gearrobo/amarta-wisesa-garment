<?php
include "config/db.php";

$id = intval($_GET['id']);

// Ambil nama file untuk dihapus
$result = $conn->query("SELECT sample_product, design, st_chart, material_sm, pola_sample, buat_sample FROM sps WHERE id = $id");
$data = $result->fetch_assoc();

if ($data) {
    // Hapus file dari server
    $files = ['sample_product', 'design', 'st_chart', 'material_sm', 'pola_sample', 'buat_sample'];
    foreach ($files as $file) {
        if ($data[$file] && file_exists('uploads/' . $data[$file])) {
            unlink('uploads/' . $data[$file]);
        }
    }
    
    // Hapus data dari database
    $stmt = $conn->prepare("DELETE FROM sps WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?deleted=1");
    } else {
        header("Location: index.php?error=1");
    }
} else {
    header("Location: index.php?error=1");
}
?>
