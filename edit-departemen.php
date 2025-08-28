<?php
// Start session
session_start();

// Include database configuration
include 'config/db.php';

// Include header
include 'includes/header.php';

// Get department ID from URL
$id_departemen = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize variables
$nama_departemen = '';
$deskripsi = '';
$error = '';
$success = '';

// Get department data
if ($id_departemen > 0) {
    $sql = "SELECT * FROM departemen WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_departemen);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $departemen = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($departemen) {
        $nama_departemen = $departemen['nama_departemen'];
        $deskripsi = $departemen['deskripsi'];
    } else {
        $_SESSION['error'] = "Departemen tidak ditemukan!";
        header("Location: data-departemen.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_departemen'])) {
        // Get form data
        $nama_departemen = trim($_POST['nama_departemen']);
        $deskripsi = trim($_POST['deskripsi']);

        // Validate input
        if (empty($nama_departemen)) {
            $error = "Nama departemen wajib diisi!";
        } else {
            // Start transaction
            mysqli_autocommit($conn, FALSE);
            
            try {
                // Update department data
                $sql = "UPDATE departemen SET nama_departemen = ?, deskripsi = ?, updated_at = NOW() WHERE id = ?";
                
                $stmt = mysqli_prepare($conn, $sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . mysqli_error($conn));
                }
                
                mysqli_stmt_bind_param($stmt, "ssi", $nama_departemen, $deskripsi, $id_departemen);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
                }
                
                mysqli_stmt_close($stmt);
                
                // Commit transaction
                mysqli_commit($conn);
                
                $_SESSION['success'] = "Data departemen berhasil diupdate!";
                header("Location: data-departemen.php");
                exit();
                
            } catch(Exception $e) {
                mysqli_rollback($conn);
                $error = "Gagal mengupdate data departemen: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Edit Data Departemen</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="data-departemen.php">Data Departemen</a></li>
                    <li class="breadcrumb-item active">Edit Departemen</li>
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

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Edit Department Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Form Edit Departemen</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Departemen</label>
                            <input type="text" name="nama_departemen" class="form-control" 
                                   value="<?= htmlspecialchars($nama_departemen); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" required><?= htmlspecialchars($deskripsi); ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" name="update_departemen" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Departemen
                            </button>
                            <a href="data-departemen.php" class="btn btn-secondary">
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
