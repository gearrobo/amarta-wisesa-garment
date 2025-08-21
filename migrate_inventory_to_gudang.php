<?php
// Script untuk migrasi data dari inventory ke inventory_gudang
include "config/db.php";

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

echo "Memulai migrasi data dari inventory ke inventory_gudang...<br>";

// Mulai transaction
$conn->begin_transaction();

try {
    // Ambil semua data dari inventory
    $sql = "SELECT * FROM inventory";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 0) {
        echo "Tidak ada data inventory untuk dimigrasi.<br>";
        exit();
    }
    
    $migrated_count = 0;
    $skipped_count = 0;
    
    while ($row = $result->fetch_assoc()) {
        // Dapatkan ID gudang berdasarkan nama warehouse
        $warehouse = $row['warehouse'];
        $sql_gudang = "SELECT id_gudang FROM gudang WHERE nama = ?";
        $stmt_gudang = $conn->prepare($sql_gudang);
        $stmt_gudang->bind_param("s", $warehouse);
        $stmt_gudang->execute();
        $result_gudang = $stmt_gudang->get_result();
        
        if ($result_gudang->num_rows === 0) {
            echo "Skipping: Gudang '$warehouse' tidak ditemukan untuk barang '{$row['nama_barang']}'<br>";
            $skipped_count++;
            continue;
        }
        
        $gudang_data = $result_gudang->fetch_assoc();
        $id_gudang = $gudang_data['id_gudang'];
        $stmt_gudang->close();
        
        // Generate kode barang
        $kode_barang = 'INV' . str_pad($row['id'], 4, '0', STR_PAD_LEFT);
        
        // Cek apakah barang sudah ada di inventory_gudang
        $sql_check = "SELECT id_inventory FROM inventory_gudang 
                     WHERE nama_barang = ? AND id_gudang = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("si", $row['nama_barang'], $id_gudang);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            // Update existing barang
            $existing_data = $result_check->fetch_assoc();
            $existing_id = $existing_data['id_inventory'];
            
            $sql_update = "UPDATE inventory_gudang 
                          SET jumlah = jumlah + ?, stok_akhir = stok_akhir + ?, tanggal_update = NOW()
                          WHERE id_inventory = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("iii", $row['jumlah'], $row['jumlah'], $existing_id);
            
            if (!$stmt_update->execute()) {
                throw new Exception("Update inventory_gudang failed: " . $stmt_update->error);
            }
            $stmt_update->close();
            
            $inventory_gudang_id = $existing_id;
        } else {
            // Insert new barang ke inventory_gudang
            $sql_insert = "INSERT INTO inventory_gudang 
                          (id_gudang, kode_barang, nama_barang, jumlah, stok_akhir, satuan, tanggal_masuk)
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_insert = $conn->prepare($sql_insert);
            $tanggal_masuk = $row['created_at'] ? date('Y-m-d', strtotime($row['created_at'])) : date('Y-m-d');
            $stmt_insert->bind_param("issiiss", $id_gudang, $kode_barang, $row['nama_barang'], 
                                   $row['jumlah'], $row['jumlah'], $row['unit'], $tanggal_masuk);
            
            if (!$stmt_insert->execute()) {
                throw new Exception("Insert inventory_gudang failed: " . $stmt_insert->error);
            }
            
            $inventory_gudang_id = $stmt_insert->insert_id;
            $stmt_insert->close();
        }
        
        // Buat transaksi untuk data yang dimigrasi
        $sql_transaksi = "INSERT INTO inventory_transaksi_gudang 
                        (inventory_gudang_id, jenis, jumlah_masuk, keterangan, tanggal_transaksi)
                        VALUES (?, 'masuk', ?, ?, ?)";
        
        $keterangan = "Migrasi data dari inventory lama - " . ($row['keterangan'] ?? '');
        $tanggal_transaksi = $row['created_at'] ?: date('Y-m-d H:i:s');
        
        $stmt_transaksi = $conn->prepare($sql_transaksi);
        $stmt_transaksi->bind_param("iiss", $inventory_gudang_id, $row['jumlah'], $keterangan, $tanggal_transaksi);
        
        if (!$stmt_transaksi->execute()) {
            throw new Exception("Insert transaksi failed: " . $stmt_transaksi->error);
        }
        $stmt_transaksi->close();
        
        $migrated_count++;
        echo "Berhasil migrasi: {$row['nama_barang']} ({$row['jumlah']} {$row['unit']}) di {$warehouse}<br>";
    }
    
    // Commit transaction
    $conn->commit();
    
    echo "<br><strong>Migrasi selesai!</strong><br>";
    echo "Total data berhasil dimigrasi: $migrated_count<br>";
    echo "Total data skipped: $skipped_count<br>";
    
} catch (Exception $e) {
    // Rollback transaction jika ada error
    $conn->rollback();
    echo "Error selama migrasi: " . $e->getMessage() . "<br>";
}

$conn->close();
?>
