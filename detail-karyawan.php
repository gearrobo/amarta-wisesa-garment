<?php
// Include header
include 'includes/header.php';
include 'config/db.php';



// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: data-karyawan.php");
    exit();
}

$id_karyawan = $_GET['id'];

// Get employee details
$sql = "SELECT k.*, j.nama_jabatan, d.nama_departemen 
        FROM karyawan k 
        LEFT JOIN jabatan j ON k.id_jabatan = j.id
        LEFT JOIN departemen d ON k.id_departemen = d.id
        WHERE k.id = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$karyawan = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$karyawan) {
    header("Location: data-karyawan.php");
    exit();
}

// Get type-specific data
$karyawan_tetap = null;
$karyawan_non_tetap = null;

if ($karyawan['type_karyawan'] == 'tetap') {
    $sql = "SELECT * FROM karyawan_tetap WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $karyawan_tetap = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
} else {
    $sql = "SELECT * FROM karyawan_harian_borongan WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $karyawan_non_tetap = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Detail Karyawan</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="data-karyawan.php">Data Karyawan</a></li>
                    <li class="breadcrumb-item active">Detail Karyawan</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Karyawan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>NIK</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan['nik']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Nama Lengkap</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan['nama_lengkap']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Jenis Kelamin</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan['jenis_kelamin']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tempat, Tanggal Lahir</strong></label>
                                        <p class="form-control-plaintext">
                                            <?= htmlspecialchars($karyawan['tempat_lahir']); ?>, 
                                            <?= date('d/m/Y', strtotime($karyawan['tanggal_lahir'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Alamat</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan['alamat']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>No Telepon</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan['no_telepon']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Email</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan['email']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Type Karyawan</strong></label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-<?= $karyawan['type_karyawan'] == 'tetap' ? 'primary' : 'secondary'; ?>">
                                                <?= ucfirst($karyawan['type_karyawan']); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Jabatan</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan['nama_jabatan']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Departemen</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan['nama_departemen']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tanggal Masuk</strong></label>
                                        <p class="form-control-plaintext"><?= date('d/m/Y', strtotime($karyawan['tanggal_masuk'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($karyawan['type_karyawan'] == 'tetap' && isset($karyawan_tetap)): ?>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Karyawan Tetap</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>NPWP</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan_tetap['npwp'] ?? '-'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>BPJS Ketenagakerjaan</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan_tetap['bpjs_ketenagakerjaan'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>BPJS Kesehatan</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan_tetap['bpjs_kesehatan'] ?? '-'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Gaji Pokok</strong></label>
                                        <p class="form-control-plaintext">Rp <?= number_format($karyawan_tetap['gaji_pokok'] ?? 0, 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tunjangan Jabatan</strong></label>
                                        <p class="form-control-plaintext">Rp <?= number_format($karyawan_tetap['tunjangan_jabatan'] ?? 0, 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tunjangan Transport</strong></label>
                                        <p class="form-control-plaintext">Rp <?= number_format($karyawan_tetap['tunjangan_transport'] ?? 0, 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tunjangan Makan</strong></label>
                                        <p class="form-control-plaintext">Rp <?= number_format($karyawan_tetap['tunjangan_makan'] ?? 0, 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Rekening Bank</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan_tetap['rekening_bank'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Nama Bank</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan_tetap['nama_bank'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php elseif (isset($karyawan_non_tetap)): ?>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Karyawan Non Tetap</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Upah per Hari</strong></label>
                                        <p class="form-control-plaintext">Rp <?= number_format($karyawan_non_tetap['upah_per_hari'] ?? 0, 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Upah per Jam</strong></label>
                                        <p class="form-control-plaintext">Rp <?= number_format($karyawan_non_tetap['upah_per_jam'] ?? 0, 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Upah Borongan</strong></label>
                                        <p class="form-control-plaintext">Rp <?= number_format($karyawan_non_tetap['upah_borongan'] ?? 0, 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Metode Pembayaran</strong></label>
                                        <p class="form-control-plaintext"><?= ucfirst($karyawan_non_tetap['metode_pembayaran'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Rekening Bank</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan_non_tetap['rekening_bank'] ?? '-'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Nama Bank</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($karyawan_non_tetap['nama_bank'] ?? '-'); ?></p>
                                    </div>
                                </div>
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
                            <a href="edit-karyawan.php?id=<?= $karyawan['id']; ?>" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                            <button type="button" class="btn btn-danger w-100" onclick="hapusKaryawan(<?= $karyawan['id']; ?>)">
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
                            <?= date('d/m/Y', strtotime($karyawan['tanggal_masuk'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function hapusKaryawan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data karyawan ini?')) {
        window.location.href = 'hapus-karyawan.php?id=' + id;
    }
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
