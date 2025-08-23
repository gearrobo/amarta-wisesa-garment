<?php
include 'config/db.php'; // koneksi ke database

// Ambil id_gudang dari POST (kalau ada)
$id_gudang = isset($_POST['gudang']) ? intval($_POST['gudang']) : 0;

// Ambil data gudang
$sql_gudang = "SELECT * FROM gudang ORDER BY nama";
$result_gudang = $conn->query($sql_gudang);

// Kalau gudang dipilih, ambil data kategori/inventory
$result_kategori = false;
if ($id_gudang > 0) {
    $sql_kategori = "SELECT * FROM inventory_gudang WHERE id_gudang = $id_gudang ORDER BY nama_barang";
    $result_kategori = $conn->query($sql_kategori);

    if (!$result_kategori) {
        die("Query error: " . $conn->error . " | SQL: " . $sql_kategori);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Gudang & Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h3>Pilih Gudang & Barang</h3>

    <!-- Form untuk pilih gudang -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="gudang" class="form-label">Gudang</label>
            <select class="form-control" id="gudang" name="gudang" required onchange="this.form.submit()">
                <option value="">Pilih Gudang</option>
                <?php
                if ($result_gudang && $result_gudang->num_rows > 0) {
                    while ($gudang = $result_gudang->fetch_assoc()) {
                        $selected = ($id_gudang == $gudang['id_gudang']) ? 'selected' : '';
                        echo '<option value="' . $gudang['id_gudang'] . '" ' . $selected . '>' . htmlspecialchars($gudang['nama']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </form>

    <!-- Form pilih kategori (hanya muncul kalau gudang sudah dipilih) -->
    <?php if ($id_gudang > 0): ?>
        <form method="POST" action="">
            <input type="hidden" name="gudang" value="<?php echo $id_gudang; ?>">
            <div class="mb-3">
                <label for="kategori_barang" class="form-label">Kategori Barang</label>
                <select class="form-control" id="kategori_barang" name="kategori_barang" required>
                    <option value="">Pilih Kategori</option>
                    <?php
                    if ($result_kategori && $result_kategori->num_rows > 0) {
                        while ($kategori = $result_kategori->fetch_assoc()) {
                            echo '<option value="' . $kategori['id_inventory'] . '">' . htmlspecialchars($kategori['nama_barang']) . '</option>';
                        }
                    } else {
                        echo '<option value="">Tidak ada data</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Lanjut</button>
        </form>
    <?php endif; ?>

</body>
</html>
