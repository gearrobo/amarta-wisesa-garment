<?php
// Start session
session_start();

// Include database configuration
include 'config/db.php';

// Check if ID is provided
if (isset($_GET['id'])) {
    $id_kategori = $_GET['id'];
    
    // Start transaction
    mysqli_autocommit($conn, FALSE);
    
    try {
        // Delete kategori
        $sql = "DELETE FROM kategori_barang WHERE id_kategori = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "i", $id_kategori);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }
        
        mysqli_stmt_close($stmt);
        
        // Commit transaction
        mysqli_commit($conn);
        
        $_SESSION['success'] = "Data kategori berhasil dihapus!";
        
    } catch(Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Gagal menghapus data kategori: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "ID kategori tidak valid!";
}

// Redirect back to kategori-barang.php
header("Location: kategori-barang.php");
exit();
?>
