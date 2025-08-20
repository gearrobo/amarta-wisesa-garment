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

$id_gudang = intval($_GET['id']); // Sanitize ID
$error = '';
$success = '';

// Get warehouse details
$sql = "SELECT * FROM gudang WHERE id_gudang = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_gudang);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$gudang = mysqli_fetch_assoc($result);

if (!$gudang) {
    header("Location: data-gudang.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kepala_gudang = mysqli_real_escape_string($conn, $_POST['kepala_gudang']);
    $kapasitas = intval($_POST['kapasitas']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Update warehouse data
    $sql = "UPDATE gudang SET 
            nama = ?, alamat = ?, kepala_gudang = ?, kapasitas = ?, keterangan = ?
            WHERE id_gudang = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssisi", 
        $nama, $alamat, $kepala_gudang, $kapasitas, $keterangan, $id_gudang
    );

    if (mysqli_stmt_execute($stmt)) {
        $success = "Data gudang berhasil diperbarui!";
        
        // Refresh data
        $sql = "SELECT * FROM gudang WHERE id_gudang = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_gudang);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $gudang = mysqli_fetch_assoc($result);
    } else {
        $error = "Gagal memperbarui data: " . mysqli_error($conn);
    }
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Edit Data Gudang</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="data-gudang.php">Data Gudang</a></li>
                    <li class="breadcrumb-item active">Edit Gudang</li>
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
                    <h5 class="card-title mb-0">Form Edit Gudang</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Gudang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="<?= htmlspecialchars($gudang['nama']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kepala_gudang" class="form-label">Kepala Gudang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kepala_gudang" name="kepala_gudang" 
                                           value="<?= htmlspecialchars($gudang['kepala_gudang']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($gudang['alamat']); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kapasitas" class="form-label">Kapasitas (m²) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="kapasitas" name="kapasitas" 
                                           value="<?= $gudang['kapasitas']; ?>" required min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= htmlspecialchars($gudang['keterangan'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="detail-gudang.php?id=<?= $id_gudang; ?>" class="btn btn-secondary">
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
