<?php

// Include database configuration
include 'config/db.php';

// Include header
include 'includes/header.php';

// Get department ID from URL
$id_departemen = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get department details
$sql = "SELECT * FROM departemen WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    $_SESSION['error'] = "Error dalam query departemen: " . mysqli_error($conn);
    header("Location: data-departemen.php");
    exit();
}
mysqli_stmt_bind_param($stmt, "i", $id_departemen);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$departemen = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$departemen) {
    $_SESSION['error'] = "Departemen tidak ditemukan!";
    header("Location: data-departemen.php");
    exit();
}

// Get employees in this department
$sql = "SELECT k.*, j.nama_jabatan 
        FROM karyawan k 
        LEFT JOIN jabatan j ON k.id_jabatan = j.id 
        WHERE k.id_departemen = ? 
        ORDER BY k.nama_lengkap";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    $_SESSION['error'] = "Error dalam query karyawan: " . mysqli_error($conn);
    header("Location: data-departemen.php");
    exit();
}
mysqli_stmt_bind_param($stmt, "i", $id_departemen);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$karyawanList = [];
if ($result !== false) {
    $karyawanList = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
mysqli_stmt_close($stmt);

// Get total employees count
$totalKaryawan = is_array($karyawanList) ? count($karyawanList) : 0;
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Detail Departemen: <?= htmlspecialchars($departemen['nama_departemen']); ?></h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="data-departemen.php">Data Departemen</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($departemen['nama_departemen']); ?></li>
                </ol>
            </nav>

            <!-- Alert Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Department Details Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Departemen</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Nama Departemen</th>
                                    <td><?= htmlspecialchars($departemen['nama_departemen']); ?></td>
                                </tr>
                                <tr>
                                    <th>Deskripsi</th>
                                    <td><?= htmlspecialchars($departemen['deskripsi']); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Karyawan</th>
                                    <td>
                                        <span class="badge bg-primary fs-6">
                                            <?= $totalKaryawan; ?> karyawan
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Tanggal Dibuat</th>
                                    <td><?= date('d/m/Y H:i', strtotime($departemen['created_at'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diupdate</th>
                                    <td><?= date('d/m/Y H:i', strtotime($departemen['updated_at'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employees in Department Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Karyawan di Departemen Ini</h5>
                </div>
                <div class="card-body">
                    <?php if ($totalKaryawan > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tabelKaryawanDept">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jabatan</th>
                                        <th>Type Karyawan</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($karyawanList as $karyawan): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($karyawan['nik']); ?></td>
                                        <td><?= htmlspecialchars($karyawan['nama_lengkap']); ?></td>
                                        <td><?= htmlspecialchars($karyawan['nama_jabatan']); ?></td>
                                        <td>
                                            <span class="badge bg-<?= $karyawan['type_karyawan'] == 'tetap' ? 'primary' : 'secondary'; ?>">
                                                <?= ucfirst($karyawan['type_karyawan']); ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($karyawan['tanggal_masuk'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?= $karyawan['status_karyawan'] == 'aktif' ? 'success' : 'danger'; ?>">
                                                <?= ucfirst($karyawan['status_karyawan']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="detail-karyawan.php?id=<?= $karyawan['id_karyawan']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit-karyawan.php?id=<?= $karyawan['id_karyawan']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Tidak ada karyawan di departemen ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-3">
                <a href="data-departemen.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Data Departemen
                </a>
            </div>
        </div>
    </div>
</div>

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabelKaryawanDept').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
