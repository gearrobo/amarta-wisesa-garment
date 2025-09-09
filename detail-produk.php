<?php
// Start session
session_start();

// Include header
include 'includes/header.php';
// Include database configuration
include 'config/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID produk tidak valid!";
    header("Location: data-produk.php");
    exit();
}

$id_produk = $_GET['id'];

// Get product details
$sql = "SELECT p.*, k.nama_kategori 
        FROM produk p 
        LEFT JOIN kategori_barang k ON p.id_kategori = k.id_kategori 
        WHERE p.id_produk = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$produk) {
    $_SESSION['error'] = "Data produk tidak ditemukan!";
    header("Location: data-produk.php");
    exit();
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Detail Produk</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="data-produk.php">Data Produk</a></li>
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

            <!-- Product Details Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Produk</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ID Produk</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($produk['id_produk']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kode Produk</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($produk['kode_produk']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Produk</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($produk['nama_produk']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kategori</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($produk['nama_kategori'] ?? '-'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Harga</label>
                                <p class="form-control-plaintext">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-<?= $produk['status'] == 'aktif' ? 'success' : 'secondary'; ?>">
                                        <?= ucfirst($produk['status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($produk['deskripsi'] ?: '-'); ?></p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Dibuat</label>
                                <p class="form-control-plaintext"><?= date('d/m/Y', strtotime($produk['created_at'])); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Diupdate</label>
                                <p class="form-control-plaintext"><?= date('d/m/Y', strtotime($produk['updated_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="data-produk.php" class="btn btn-secondary">Kembali</a>
                    <a href="edit-produk.php?id=<?= $produk['id_produk']; ?>" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>
