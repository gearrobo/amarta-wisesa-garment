<?php

// Include header and database configuration
include 'includes/header.php';
include 'config/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: inventory.php");
    exit();
}

$id = intval($_GET['id']);
$error = '';
$success = '';

// Get inventory details
$sql = "SELECT * FROM inventory WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$inventory = mysqli_fetch_assoc($result);

if (!$inventory) {
    header("Location: inventory.php");
    exit();
}

// Get data for dropdowns
$nama_gudang = $conn->query("SELECT * FROM gudang ORDER BY nama");
$kategori_barang = $conn->query("SELECT * FROM kategori_barang ORDER BY nama_kategori");

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang'] ?? '');
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $warehouse = mysqli_real_escape_string($conn, $_POST['warehouse']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $jumlah_baru = intval($_POST['jumlah']);
    $harga_per_unit = floatval($_POST['harga_per_unit']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan'] ?? '');
    
    // Simpan nilai jumlah lama sebelum diubah
    $jumlah_lama = $inventory['jumlah'];

    // Validate required fields
    if (empty($nama_barang) || empty($warehouse) || empty($unit) || $jumlah_baru <= 0 || $harga_per_unit <= 0) {
        $error = "Semua field wajib diisi dengan nilai yang valid!";
    } else {
        // Update inventory data
        $sql = "UPDATE inventory SET 
                kode_barang = ?, nama_barang = ?, warehouse = ?, unit = ?, 
                jumlah = ?, harga_per_unit = ?, keterangan = ?, updated_at = NOW()
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssidsi", 
            $kode_barang, $nama_barang, $warehouse, $unit, 
            $jumlah_baru, $harga_per_unit, $keterangan, $id
        );

        if (mysqli_stmt_execute($stmt)) {
            // Dapatkan ID gudang berdasarkan nama
            $stmt_gudang = $conn->prepare("SELECT id FROM gudang WHERE nama = ?");
            $stmt_gudang->bind_param("s", $warehouse);
            $stmt_gudang->execute();
            $result_gudang = $stmt_gudang->get_result();
            
            if ($row_gudang = $result_gudang->fetch_assoc()) {
                $id_gudang = $row_gudang['id'];
                
                // Cek apakah data sudah ada di inventory_gudang
                $stmt_check = $conn->prepare("SELECT id, jumlah FROM inventory_gudang WHERE id_gudang = ? AND nama_barang = ?");
                $stmt_check->bind_param("is", $id_gudang, $nama_barang);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                
                if ($result_check->num_rows > 0) {
                    // Data sudah ada, lakukan update
                    $row_check = $result_check->fetch_assoc();
                    $id_inventory_gudang = $row_check['id'];
                    $stok_sekarang = $row_check['jumlah'];
                    
                    // Hitung stok akhir (stok sekarang + (jumlah baru - jumlah lama))
                    $stok_akhir = $stok_sekarang + ($jumlah_baru - $jumlah_lama);
                    
                    // Update inventory_gudang
                    $sql_update = "UPDATE inventory_gudang 
                                   SET jumlah = ?, stok_akhir = ?, tanggal_update = NOW() 
                                   WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("iii", $stok_akhir, $stok_akhir, $id_inventory_gudang);
                    
                    if ($stmt_update->execute()) {
                        $success = "Data inventory berhasil diperbarui dan stok gudang telah diupdate!";
                    } else {
                        $error = "Data inventory berhasil diperbarui tetapi gagal update stok gudang: " . $stmt_update->error;
                    }
                } else {
                    // Data belum ada, buat record baru
                    $sql_insert = "INSERT INTO inventory_gudang (id_gudang, nama_barang, jumlah, stok_akhir, tanggal_update) 
                                   VALUES (?, ?, ?, ?, NOW())";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("isii", $id_gudang, $nama_barang, $jumlah_baru, $jumlah_baru);
                    
                    if ($stmt_insert->execute()) {
                        $success = "Data inventory berhasil diperbarui dan record stok gudang baru telah dibuat!";
                    } else {
                        $error = "Data inventory berhasil diperbarui tetapi gagal membuat record stok gudang: " . $stmt_insert->error;
                    }
                }
            } else {
                $error = "Gudang tidak ditemukan!";
            }
            
            // Refresh data inventory
            $sql = "SELECT * FROM inventory WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $inventory = mysqli_fetch_assoc($result);
        } else {
            $error = "Gagal memperbarui data inventory: " . mysqli_error($conn);
        }
    }
}
?>

<!-- HTML form remains the same -->
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Edit Data Inventory</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="inventory.php">Inventory Barang</a></li>
                    <li class="breadcrumb-item active">Edit Inventory</li>
                </ol>
            </nav>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Form Edit Inventory</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kode_barang" class="form-label">Kode Barang</label>
                                    <input type="text" class="form-control" id="kode_barang" name="kode_barang" 
                                           value="<?= htmlspecialchars($inventory['kode_barang'] ?? ''); ?>" 
                                           placeholder="Masukkan kode barang (opsional)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                    <select name="nama_barang" class="form-control" required>
                                        <option value="">Pilih Kategori Barang</option>
                                        <?php 
                                        $kategori_barang->data_seek(0);
                                        while($row_kategori = $kategori_barang->fetch_assoc()): 
                                        ?>
                                        <option value="<?= htmlspecialchars($row_kategori['nama_kategori']); ?>" 
                                            <?= ($inventory['nama_barang'] == $row_kategori['nama_kategori']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($row_kategori['nama_kategori']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warehouse" class="form-label">Warehouse <span class="text-danger">*</span></label>
                                    <select name="warehouse" class="form-control" required>
                                        <option value="">Pilih Warehouse</option>
                                        <?php 
                                        $nama_gudang->data_seek(0);
                                        while($row_gudang = $nama_gudang->fetch_assoc()): 
                                        ?>
                                        <option value="<?= htmlspecialchars($row_gudang['nama']); ?>" 
                                            <?= ($inventory['warehouse'] == $row_gudang['nama']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($row_gudang['nama']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                                    <select name="unit" class="form-control" required>
                                        <option value="">Pilih Unit</option>
                                        <option value="pcs" <?= ($inventory['unit'] == 'pcs') ? 'selected' : '' ?>>pcs</option>
                                        <option value="m²" <?= ($inventory['unit'] == 'm²') ? 'selected' : '' ?>>m²</option>
                                        <option value="kg" <?= ($inventory['unit'] == 'kg') ? 'selected' : '' ?>>kg</option>
                                        <option value="roll" <?= ($inventory['unit'] == 'roll') ? 'selected' : '' ?>>roll</option>
                                        <option value="meter" <?= ($inventory['unit'] == 'meter') ? 'selected' : '' ?>>meter</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" 
                                           value="<?= $inventory['jumlah']; ?>" required min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="harga_per_unit" class="form-label">Harga per Unit <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="harga_per_unit" name="harga_per_unit" 
                                               value="<?= $inventory['harga_per_unit']; ?>" required min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= htmlspecialchars($inventory['keterangan'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="detail-inventory.php?id=<?= $id; ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize any date pickers or other JS components
    if ($('input[type="date"]').length > 0) {
        $('input[type="date"]').attr('autocomplete', 'off');
    }
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>