<?php
// Include header
include 'includes/header.php';
include 'config/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: data-mesin.php");
    exit();
}

$id_mesin = $_GET['id'];

// Get machine details
$sql = "SELECT * FROM mesin WHERE id_mesin = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_mesin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$mesin = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$mesin) {
    header("Location: data-mesin.php");
    exit();
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Detail Mesin</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="data-mesin.php">Data Mesin</a></li>
                    <li class="breadcrumb-item active">Detail Mesin</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Mesin</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Seri Number</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($mesin['seri_number']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Nama Mesin</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($mesin['nama_mesin']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Lokasi</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($mesin['lokasi']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tanggal Masuk</strong></label>
                                        <p class="form-control-plaintext"><?= date('d/m/Y', strtotime($mesin['tanggal_masuk'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Keterangan</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($mesin['keterangan'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tanggal Dibuat</strong></label>
                                        <p class="form-control-plaintext"><?= date('d/m/Y H:i', strtotime($mesin['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Terakhir Diupdate</strong></label>
                                        <p class="form-control-plaintext"><?= date('d/m/Y H:i', strtotime($mesin['updated_at'])); ?></p>
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
                            <a href="edit-mesin.php?id=<?= $mesin['id_mesin']; ?>" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                            <button type="button" class="btn btn-danger w-100" onclick="hapusMesin(<?= $mesin['id_mesin']; ?>)">
                                <i class="fas fa-trash"></i> Hapus Data
                            </button>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Riwayat</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Tanggal Masuk:</strong><br>
                            <?= date('d/m/Y', strtotime($mesin['tanggal_masuk'])); ?></p>
                            
                            <p><strong>Tanggal Dibuat:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($mesin['created_at'])); ?></p>
                            
                            <p><strong>Terakhir Diupdate:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($mesin['updated_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function hapusMesin(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data mesin ini?')) {
        window.location.href = 'hapus-mesin.php?id=' + id;
    }
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
