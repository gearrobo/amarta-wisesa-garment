<?php
// Include header
include 'includes/header.php';
include 'config/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: data-mesin.php");
    exit();
}

$id_mesin = intval($_GET['id']); // Sanitize ID
$error = '';
$success = '';

// Get machine details
$sql = "SELECT * FROM mesin WHERE id = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_mesin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$mesin = mysqli_fetch_assoc($result);

if (!$mesin) {
    header("Location: data-mesin.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $seri_number = mysqli_real_escape_string($conn, $_POST['seri_number']);
    $nama_mesin = mysqli_real_escape_string($conn, $_POST['nama_mesin']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $tanggal_masuk = mysqli_real_escape_string($conn, $_POST['tanggal_masuk']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Update machine data
    $sql = "UPDATE mesin SET 
            seri_number = ?, nama_mesin = ?, lokasi = ?, tanggal_masuk = ?, keterangan = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", 
        $seri_number, $nama_mesin, $lokasi, $tanggal_masuk, $keterangan, $id_mesin
    );

    if (mysqli_stmt_execute($stmt)) {
        $success = "Data mesin berhasil diperbarui!";
        
        // Refresh data
        $sql = "SELECT * FROM mesin WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_mesin);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $mesin = mysqli_fetch_assoc($result);
    } else {
        $error = "Gagal memperbarui data: " . mysqli_error($conn);
    }
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Edit Data Mesin</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="data-mesin.php">Data Mesin</a></li>
                    <li class="breadcrumb-item active">Edit Mesin</li>
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
                    <h5 class="card-title mb-0">Form Edit Mesin</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seri_number" class="form-label">Seri Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="seri_number" name="seri_number" 
                                           value="<?= htmlspecialchars($mesin['seri_number']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_mesin" class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_mesin" name="nama_mesin" 
                                           value="<?= htmlspecialchars($mesin['nama_mesin']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lokasi" name="lokasi" 
                                           value="<?= htmlspecialchars($mesin['lokasi']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_masuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" 
                                           value="<?= $mesin['tanggal_masuk']; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= htmlspecialchars($mesin['keterangan'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Data
                            </button>
                            <a href="detail-mesin.php?id=<?= $id_mesin; ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>
