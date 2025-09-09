<?php
include 'config/db.php';

// Cek apakah ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);

    if ($id > 0) {
        // Query hapus
        $sql = "DELETE FROM karyawan WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Redirect kembali dengan pesan sukses
            header("Location: data-karyawan.php");
            exit;
        } else {
            die("Gagal menghapus data: " . $conn->error);
        }
    } else {
        die("ID tidak valid.");
    }
} else {
    die("Akses tidak diizinkan.");
}
