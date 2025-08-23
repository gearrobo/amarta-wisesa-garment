<?php
include 'config/db.php';

// Test the HPP query
$id_persiapan = 2; // Test with ID 2 from the database

$sql_hpp = "SELECT *, 
                   gudang, 
                   kategori_barang, 
                   satuan, 
                   barang_jadi, 
                   consp, 
                   stok_material, 
                   purchase_order, 
                   sppo,
                   total
            FROM hpp WHERE id_persiapan = ? ORDER BY created_at DESC";

$stmt_hpp = mysqli_prepare($conn, $sql_hpp);
if (!$stmt_hpp) {
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt_hpp, "i", $id_persiapan);
mysqli_stmt_execute($stmt_hpp);
$result_hpp = mysqli_stmt_get_result($stmt_hpp);
$hpp_items = mysqli_fetch_all($result_hpp, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_hpp);

echo "HPP Items for persiapan ID $id_persiapan:\n";
echo "Number of items: " . count($hpp_items) . "\n\n";

foreach ($hpp_items as $index => $item) {
    echo "Item " . ($index + 1) . ":\n";
    echo "ID: " . ($item['id'] ?? 'N/A') . "\n";
    echo "Total: " . ($item['total'] ?? 'N/A') . "\n";
    echo "Jumlah: " . ($item['jumlah'] ?? 'N/A') . "\n";
    echo "Harga: " . ($item['harga'] ?? 'N/A') . "\n";
    echo "---\n";
    
    // Test the total calculation
    if (isset($item['total']) && is_numeric($item['total'])) {
        echo "Total from DB: " . $item['total'] . "\n";
    } else {
        $jumlah = isset($item['jumlah']) ? floatval($item['jumlah']) : 0;
        $harga = isset($item['harga']) ? floatval($item['harga']) : 0;
        $calculated_total = $jumlah * $harga;
        echo "Calculated total: " . $calculated_total . "\n";
    }
    echo "==========\n";
}
?>
