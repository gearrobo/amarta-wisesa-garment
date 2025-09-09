<?php

// Include header and database configuration
include 'includes/header.php';
include 'config/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: inventory.php");
    exit();
}

$id = $_GET['id'];

// Get inventory details
$sql = "SELECT * FROM inventory WHERE id = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$inventory = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$inventory) {
    header("Location: inventory.php");
    exit();
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Detail Inventory</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="inventory.php">Inventory Barang</a></li>
                    <li class="breadcrumb-item active">Detail Inventory</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Barang</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Kode Barang</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($inventory['kode_barang'] ?? '-'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Nama Barang</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($inventory['nama_barang']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Warehouse</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($inventory['warehouse']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                <div class="mb-3">
                                        <label class="form-label"><strong>Unit</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($inventory['unit']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Jumlah</strong></label>
                                        <p class="form-control-plaintext"><?= number_format($inventory['jumlah']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Harga per Unit</strong></label>
                                        <p class="form-control-plaintext">Rp <?= number_format($inventory['harga_per_unit'], 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Total Nilai</strong></label>
                                        <p class="form-control-plaintext"><strong>Rp <?= number_format($inventory['jumlah'] * $inventory['harga_per_unit'], 0, ',', '.'); ?></strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Keterangan</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($inventory['keterangan'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tanggal Input</strong></label>
                                        <p class="form-control-plaintext"><?= date('d/m/Y H:i', strtotime($inventory['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Terakhir Diupdate</strong></label>
                                        <p class="form-control-plaintext"><?= $inventory['updated_at'] ? date('d/m/Y H:i', strtotime($inventory['updated_at'])) : '-'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Aksi</h5>
                        </div>
                        <div class="card-body">
                            <a href="edit-inventory.php?id=<?= $inventory['id']; ?>" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                            <button type="button" class="btn btn-danger w-100" onclick="hapusInventory(<?= $inventory['id']; ?>)">
                                <i class="fas fa-trash"></i> Hapus Data
                            </button>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Ringkasan</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Kode Barang:</strong><br>
                            <?= htmlspecialchars($inventory['kode_barang'] ?? '-'); ?></p>
                            
                            <p><strong>Nama Barang:</strong><br>
                            <?= htmlspecialchars($inventory['nama_barang']); ?></p>
                            
                            <p><strong>Warehouse:</strong><br>
                            <?= htmlspecialchars($inventory['warehouse']); ?></p>
                            
                            <p><strong>Jumlah:</strong><br>
                            <?= number_format($inventory['jumlah']); ?> <?= htmlspecialchars($inventory['unit']); ?></p>
                            
                            <p><strong>Total Nilai:</strong><br>
                            Rp <?= number_format($inventory['jumlah'] * $inventory['harga_per_unit'], 0, ',', '.'); ?></p>
                            
                            <p><strong>Tanggal Input:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($inventory['created_at'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Statistik</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Nilai:</strong><br>
                            Rp <?= number_format($inventory['jumlah'] * $inventory['harga_per_unit'], 0, ',', '.'); ?></p>
                            
                            <p><strong>Unit:</strong><br>
                            <?= htmlspecialchars($inventory['unit']); ?></p>
                            
                            <p><strong>Warehouse:</strong><br>
                            <?= htmlspecialchars($inventory['warehouse']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function hapusInventory(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data inventory ini?')) {
        window.location.href = 'delete-inventory.php?id=' + id;
    }
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
