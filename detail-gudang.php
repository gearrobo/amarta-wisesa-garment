<?php
// Start session
session_start();

// Include header and database configuration
include 'includes/header.php';
include 'config/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: data-gudang.php");
    exit();
}

$id_gudang = $_GET['id'];

// Get warehouse details
$sql = "SELECT * FROM gudang WHERE id_gudang = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_gudang);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$gudang = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$gudang) {
    header("Location: data-gudang.php");
    exit();
}

// Get inventory data for this warehouse (if inventory_gudang table exists)
$inventory_data = [];
$inventory_sql = "SELECT * FROM inventory_gudang WHERE id_gudang = ? ORDER BY nama_barang ASC";
$stmt_inventory = mysqli_prepare($conn, $inventory_sql);
if ($stmt_inventory) {
    mysqli_stmt_bind_param($stmt_inventory, "i", $id_gudang);
    mysqli_stmt_execute($stmt_inventory);
    $inventory_result = mysqli_stmt_get_result($stmt_inventory);
    $inventory_data = mysqli_fetch_all($inventory_result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt_inventory);
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Detail Gudang</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="data-gudang.php">Data Gudang</a></li>
                    <li class="breadcrumb-item active">Detail Gudang</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Gudang</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Nama Gudang</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($gudang['nama']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Kepala Gudang</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($gudang['kepala_gudang']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Alamat</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($gudang['alamat']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Kapasitas</strong></label>
                                        <p class="form-control-plaintext"><?= number_format($gudang['kapasitas']); ?> m²</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Keterangan</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($gudang['keterangan'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tanggal Dibuat</strong></label>
                                        <p class="form-control-plaintext"><?= date('d/m/Y H:i', strtotime($gudang['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Terakhir Diupdate</strong></label>
                                        <p class="form-control-plaintext"><?= date('d/m/Y H:i', strtotime($gudang['updated_at'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Section (if data exists) -->
                    <?php if (!empty($inventory_data)): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Inventori Gudang</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Tanggal Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inventory_data as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['nama_barang']); ?></td>
                                            <td><?= number_format($item['jumlah']); ?></td>
                                            <td><?= htmlspecialchars($item['satuan']); ?></td>
                                            <td><?= date('d/m/Y', strtotime($item['tanggal_masuk'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Aksi</h5>
                        </div>
                        <div class="card-body">
                            <a href="edit-gudang.php?id=<?= $gudang['id_gudang']; ?>" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                            <button type="button" class="btn btn-danger w-100" onclick="hapusGudang(<?= $gudang['id_gudang']; ?>)">
                                <i class="fas fa-trash"></i> Hapus Data
                            </button>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Ringkasan</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Nama Gudang:</strong><br>
                            <?= htmlspecialchars($gudang['nama']); ?></p>
                            
                            <p><strong>Kapasitas:</strong><br>
                            <?= number_format($gudang['kapasitas']); ?> m²</p>
                            
                            <p><strong>Kepala Gudang:</strong><br>
                            <?= htmlspecialchars($gudang['kepala_gudang']); ?></p>
                            
                            <p><strong>Tanggal Dibuat:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($gudang['created_at'])); ?></p>
                            
                            <p><strong>Terakhir Diupdate:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($gudang['updated_at'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Statistik</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Barang:</strong><br>
                            <?= count($inventory_data); ?> jenis</p>
                            
                            <p><strong>Total Stok:</strong><br>
                            <?= count($inventory_data) > 0 ? number_format(array_sum(array_column($inventory_data, 'jumlah'))) : 0; ?> unit</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function hapusGudang(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data gudang ini?')) {
        window.location.href = 'delete-gudang.php?id=' + id;
    }
}

$(document).ready(function() {
    // Initialize DataTable for inventory if it exists
    if ($('.table').length > 0) {
        $('.table').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            searching: false,
            paging: false,
            info: false
        });
    }
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
