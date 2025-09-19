<?php
include 'config/db.php';

// Check if parameters are provided
if (isset($_GET['gudang_id']) && isset($_GET['kategori_id'])) {
    $gudang_id = intval($_GET['gudang_id']);
    $kategori_id = intval($_GET['kategori_id']);
    
    // Get category name
    $sql_kategori = "SELECT nama_kategori FROM kategori_barang WHERE id = ?";
    $stmt_kategori = mysqli_prepare($conn, $sql_kategori);
    mysqli_stmt_bind_param($stmt_kategori, "i", $kategori_id);
    mysqli_stmt_execute($stmt_kategori);
    $result_kategori = mysqli_stmt_get_result($stmt_kategori);
    
    if ($kategori = mysqli_fetch_assoc($result_kategori)) {
        $nama_kategori = $kategori['nama_kategori'];
        
        // Debug: Log the parameters
        error_log("Searching for: Gudang ID: $gudang_id, Kategori: $nama_kategori");
        
        // Get stock quantity from inventory_gudang - use LIKE for better matching
        $sql_stock = "SELECT ig.stok_akhir, ig.satuan 
                     FROM inventory_gudang ig 
                     WHERE ig.id_gudang = ? AND ig.nama_barang LIKE ? 
                     ORDER BY ig.tanggal_update DESC 
                     LIMIT 1";
        
        $stmt_stock = mysqli_prepare($conn, $sql_stock);
        $search_pattern = "%" . $nama_kategori . "%";
        mysqli_stmt_bind_param($stmt_stock, "is", $gudang_id, $search_pattern);
        mysqli_stmt_execute($stmt_stock);
        $result_stock = mysqli_stmt_get_result($stmt_stock);
        
        if ($stock = mysqli_fetch_assoc($result_stock)) {
            error_log("Found stock: " . $stock['stok_akhir'] . " " . $stock['satuan']);
            echo json_encode([
                'success' => true,
                'stok_material' => $stock['stok_akhir'],
                'satuan' => $stock['satuan']
            ]);
        } else {
            error_log("No stock found for: Gudang ID: $gudang_id, Kategori: $nama_kategori");
            echo json_encode([
                'success' => true,
                'stok_material' => 0,
                'satuan' => '-'
            ]);
        }
        
        mysqli_stmt_close($stmt_stock);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Kategori tidak ditemukan'
        ]);
    }
    
    mysqli_stmt_close($stmt_kategori);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Parameter tidak lengkap'
    ]);
}

mysqli_close($conn);
?>
