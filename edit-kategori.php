<?php

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
$sql = "SELECT * FROM kategori_barang WHERE id = ?";
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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_kategori'])) {
        // Start transaction
        mysqli_autocommit($conn, FALSE);
        
        try {
            // Update kategori data
            $sql = "UPDATE kategori_barang SET nama_kategori = ?, keterangan = ? WHERE id = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt, "ssi", 
                $_POST['nama_kategori'],
                $_POST['keterangan'],
                $id_kategori
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
            }
            
            mysqli_stmt_close($stmt);
            
            // Commit transaction
            mysqli_commit($conn);
            
            $_SESSION['success'] = "Data kategori berhasil diperbarui!";
            header("Location: kategori-barang.php");
            exit();
            
        } catch(Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal memperbarui data kategori: " . $e->getMessage();
        }
    }
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Edit Kategori Barang</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="kategori-barang.php">Kategori Barang</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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

            <!-- Edit Kategori Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Data Kategori</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">ID Kategori</label>
                                    <input type="text" class="form-control" value="<?= $kategori['id']; ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($kategori['nama_kategori']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($kategori['keterangan']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Dibuat Pada</label>
                                    <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($kategori['created_at'])); ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Diupdate Pada</label>
                                    <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($kategori['updated_at'])); ?>" disabled>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="kategori-barang.php" class="btn btn-secondary">Batal</a>
                            <button type="submit" name="update_kategori" class="btn btn-primary">Simpan Perubahan</button>
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
