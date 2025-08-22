<?php
// Start session
session_start();

// Include database configuration
include 'config/db.php';

// Include header
include 'includes/header.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID kategori tidak valid!";
    header("Location: kategori-barang.php");
    exit();
}

$id_kategori = $_GET['id'];

// Get kategori details
$sql = "SELECT * FROM kategori_barang WHERE id_kategori = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_kategori);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$kategori = mysqli_fetch_assoc($result);

if (!$kategori) {
    $_SESSION['error'] = "Data kategori tidak ditemukan!";
    header("Location: kategori-barang.php");
    exit();
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Detail Kategori Barang</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="kategori-barang.php">Kategori Barang</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>

            <!-- Alert Messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Kategori Details Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ID Kategori</label>
                                <p class="form-control-plaintext"><?= $kategori['id_kategori']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Kategori</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($kategori['nama_kategori']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($kategori['keterangan'] ?: '-'); ?></p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Dibuat Pada</label>
                                <p class="form-control-plaintext"><?= date('d/m/Y H:i', strtotime($kategori['created_at'])); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Diupdate Pada</label>
                                <p class="form-control-plaintext"><?= date('d/m/Y H:i', strtotime($kategori['updated_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="kategori-barang.php" class="btn btn-secondary">Kembali</a>
                    <a href="edit-kategori.php?id=<?= $kategori['id_kategori']; ?>" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>
