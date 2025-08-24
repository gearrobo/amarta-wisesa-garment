<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include database configuration
include 'config/db.php';

// Include header
include 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];

    // Insert into database
    $sql = "INSERT INTO produksi (nama, tanggal) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $nama, $tanggal);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION['success'] = "Data produksi berhasil ditambahkan!";
    header("Location: produksi.php");
    exit();
}

$sql = "SELECT * FROM produksi ORDER BY id_produksi DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "<!-- Database query failed: " . mysqli_error($conn) . " -->"; // Log query error
    $produksiList = []; // Ensure $produksiList is defined
} else {
    $produksiList = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>



<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Data Produksi</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Produksi</li>
                </ol>
            </nav>

            <!-- Form for adding new production -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Tambah Produksi</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Produksi</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

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

            <!-- Production Data Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Produksi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tabelProduksi">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produksi</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Populate with production data -->
                                <?php $no = 1; foreach ($produksiList as $produksi): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($produksi['nama']); ?></td>
                                    <td><?= htmlspecialchars($produksi['tanggal']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="detail-produksi.php?id=<?= $produksi['id_produksi']; ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit-produksi.php?id=<?= $produksi['id_produksi']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="hapusProduksi(<?= $produksi['id_produksi']; ?>)" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function hapusProduksi(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data produksi ini?')) {
        window.location.href = 'delete-produksi.php?id=' + id;
    }
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
