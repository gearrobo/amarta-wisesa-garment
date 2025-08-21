<?php
// Include header
include 'includes/header.php';
include 'config/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: persiapan.php");
    exit();
}

$id_persiapan = intval($_GET['id']);

// Get persiapan details
$sql = "SELECT p.*, s.customer as nama_customer, s.sps_no as kode_sps, s.kirim as tanggal_kirim 
        FROM persiapan p 
        JOIN sps s ON p.id_sps = s.id 
        WHERE p.id = ?";
        
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $id_persiapan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$persiapan = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$persiapan) {
    header("Location: persiapan.php");
    exit();
}

// Since persiapan_item table doesn't exist, we'll use the persiapan table directly
// The persiapan table already contains the item details
$items = [$persiapan]; // Single item array for consistent display
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Detail Persiapan</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="persiapan.php">Persiapan</a></li>
                    <li class="breadcrumb-item active">Detail Persiapan</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Persiapan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Kode SPS</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($persiapan['kode_sps']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Customer</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($persiapan['nama_customer']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tanggal Kirim</strong></label>
                                        <p class="form-control-plaintext"><?= date('d/m/Y', strtotime($persiapan['tanggal_kirim'])); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Status</strong></label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-<?= $persiapan['status'] == 'selesai' ? 'success' : ($persiapan['status'] == 'proses' ? 'warning' : 'secondary'); ?>">
                                                <?= ucfirst($persiapan['status']); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Keterangan</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($persiapan['keterangan'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Daftar Item Persiapan</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Item</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($items) > 0): ?>
                                            <?php foreach ($items as $index => $item): ?>
                                                <tr>
                                                    <td><?= $index + 1; ?></td>
                                                    <td><?= htmlspecialchars($item['nama_barang']); ?></td>
                                                    <td><?= htmlspecialchars($item['jumlah']); ?></td>
                                                    <td><?= htmlspecialchars($item['satuan']); ?></td>
                                                    <td><?= htmlspecialchars($item['keterangan'] ?? '-'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada item persiapan</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
                            <a href="edit-persiapan.php?id=<?= $persiapan['id']; ?>" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                            <a href="update-status-persiapan.php?id=<?= $persiapan['id']; ?>" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-sync"></i> Update Status
                            </a>
                            <button type="button" class="btn btn-danger w-100" onclick="hapusPersiapan(<?= $persiapan['id']; ?>)">
                                <i class="fas fa-trash"></i> Hapus Data
                            </button>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Tambahan</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Dibuat pada:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($persiapan['created_at'])); ?></p>
                            
                            <?php if ($persiapan['updated_at']): ?>
                                <p><strong>Diupdate pada:</strong><br>
                                <?= date('d/m/Y H:i', strtotime($persiapan['updated_at'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function hapusPersiapan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data persiapan ini?')) {
        window.location.href = 'delete-persiapan.php?id=' + id;
    }
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
