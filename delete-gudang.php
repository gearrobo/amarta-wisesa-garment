<?php
// Start session
session_start();

// Include database configuration
include "config/db.php";

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID gudang tidak ditemukan!";
    header("Location: data-gudang.php");
    exit();
}

$id = intval($_GET['id']);

// Start transaction for safe deletion
mysqli_autocommit($conn, FALSE);

try {
    // First, check if the warehouse exists
    $stmt = $conn->prepare("SELECT * FROM gudang WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    if ($data) {
        // Check if there are any inventory records associated with this warehouse
        $check_inventory = $conn->prepare("SELECT COUNT(*) as count FROM inventory_gudang WHERE id_gudang = ?");
        $check_inventory->bind_param("i", $id);
        $check_inventory->execute();
        $inventory_result = $check_inventory->get_result();
        $inventory_count = $inventory_result->fetch_assoc()['count'];
        $check_inventory->close();

        if ($inventory_count > 0) {
            // If there are inventory records, we cannot delete the warehouse due to foreign key constraints
            // Rollback transaction
            mysqli_rollback($conn);
            $_SESSION['error'] = "Tidak dapat menghapus gudang '" . htmlspecialchars($data['nama']) . "' karena masih terdapat " . $inventory_count . " item inventory yang terkait. Hapus semua inventory terlebih dahulu.";
            header("Location: data-gudang.php");
            exit();
        }

        // Now delete the warehouse
        $stmt = $conn->prepare("DELETE FROM gudang WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Commit transaction
            mysqli_commit($conn);
            
            // Set success message in session
            $_SESSION['success'] = "Data gudang '" . htmlspecialchars($data['nama']) . "' berhasil dihapus!";
            header("Location: data-gudang.php");
        } else {
            // Rollback transaction
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal menghapus data gudang: " . $conn->error;
            header("Location: data-gudang.php");
        }
        $stmt->close();
    } else {
        // Rollback transaction
        mysqli_rollback($conn);
        $_SESSION['error'] = "Data gudang tidak ditemukan!";
        header("Location: data-gudang.php");
    }
    
} catch (Exception $e) {
    // Rollback transaction on any error
    mysqli_rollback($conn);
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    header("Location: data-gudang.php");
}

// Close connection
$conn->close();
?>
