<?php
// Start session
session_start();

// Include database configuration
include 'config/db.php';

// Check if ID parameter is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID departemen tidak valid!";
    header("Location: data-departemen.php");
    exit();
}

$id_departemen = intval($_GET['id']);

// Start transaction
mysqli_autocommit($conn, FALSE);

try {
    // First check if there are employees in this department
    $sql_check = "SELECT COUNT(*) as total FROM karyawan WHERE id_departemen = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $id_departemen);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $row = mysqli_fetch_assoc($result_check);
    mysqli_stmt_close($stmt_check);
    
    if ($row['total'] > 0) {
        throw new Exception("Tidak dapat menghapus departemen karena masih terdapat " . $row['total'] . " karyawan di departemen ini. Pindahkan semua karyawan ke departemen lain terlebih dahulu.");
    }
    
    // Delete department
    $sql = "DELETE FROM departemen WHERE id_departemen = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id_departemen);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }
    
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    
    if ($affected_rows === 0) {
        throw new Exception("Departemen tidak ditemukan atau sudah dihapus.");
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    $_SESSION['success'] = "Data departemen berhasil dihapus!";
    
} catch(Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error'] = "Gagal menghapus data departemen: " . $e->getMessage();
}

// Redirect back to departments page
header("Location: data-departemen.php");
exit();
?>
