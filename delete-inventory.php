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

// Mulai transaction untuk memastikan konsistensi data
$conn->begin_transaction();

try {
    // 1. Ambil data inventory yang akan dihapus
    $sql_select = "SELECT nama_barang, warehouse, jumlah FROM inventory WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Data inventory tidak ditemukan!");
    }
    
    $inventory_data = $result->fetch_assoc();
    $nama_barang = $inventory_data['nama_barang'];
    $warehouse = $inventory_data['warehouse'];
    $jumlah_dihapus = $inventory_data['jumlah'];
    $stmt_select->close();

    // 2. Dapatkan ID gudang berdasarkan nama warehouse
    $sql_gudang = "SELECT id FROM gudang WHERE nama = ?";
    $stmt_gudang = $conn->prepare($sql_gudang);
    $stmt_gudang->bind_param("s", $warehouse);
    $stmt_gudang->execute();
    $result_gudang = $stmt_gudang->get_result();
    
    if ($result_gudang->num_rows === 0) {
        throw new Exception("Gudang '$warehouse' tidak ditemukan!");
    }
    
    $gudang_data = $result_gudang->fetch_assoc();
    $id_gudang = $gudang_data['id'];
    $stmt_gudang->close();

    // 3. Update stok di inventory_gudang
    $sql_update_gudang = "UPDATE inventory_gudang 
                          SET jumlah = jumlah - ?, 
                              stok_akhir = stok_akhir - ?,
                              tanggal_update = NOW()
                          WHERE nama_barang = ? AND id_gudang = ?";
    
    $stmt_update = $conn->prepare($sql_update_gudang);
    $stmt_update->bind_param("iisi", $jumlah_dihapus, $jumlah_dihapus, $nama_barang, $id_gudang);
    
    if (!$stmt_update->execute()) {
        throw new Exception("Gagal update stok gudang: " . $stmt_update->error);
    }
    $stmt_update->close();

    // 4. Hapus data dari inventory
    $sql_delete = "DELETE FROM inventory WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id);
    
    if (!$stmt_delete->execute()) {
        throw new Exception("Gagal menghapus data inventory: " . $stmt_delete->error);
    }
    $stmt_delete->close();

    // Commit transaction jika semua berhasil
    $conn->commit();
    
    $_SESSION['success'] = 'Data inventory berhasil dihapus dan stok gudang diperbarui!';
    header("Location: inventory.php?deleted=1");
    exit();
    
} catch (Exception $e) {
    // Rollback transaction jika ada error
    $conn->rollback();
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
    header("Location: inventory.php");
    exit();
}

$conn->close();
?>
