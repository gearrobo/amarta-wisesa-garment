<?php
// Start session
session_start();

// Include database configuration
include 'config/db.php';

// Include header
include 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah_gudang'])) {
        // Start transaction
        mysqli_autocommit($conn, FALSE);
        
        try {
            // Insert warehouse data
            $sql = "INSERT INTO gudang (nama, alamat, kepala_gudang, kapasitas, keterangan) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt, "sssis", 
                $_POST['nama'],
                $_POST['alamat'],
                $_POST['kepala_gudang'],
                $_POST['kapasitas'],
                $_POST['keterangan']
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
            }
            
            mysqli_stmt_close($stmt);
            
            // Commit transaction
            mysqli_commit($conn);
            
            $_SESSION['success'] = "Data gudang berhasil ditambahkan!";
            header("Location: data-gudang.php");
            exit();
            
        } catch(Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal menambahkan data gudang: " . $e->getMessage();
        }
    }
}

// Get all warehouses
$sql = "SELECT * FROM gudang ORDER BY id_gudang DESC";
$result = mysqli_query($conn, $sql);
$gudangList = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Data Gudang</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item active">Data Gudang</li>
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

            <!-- Action Buttons -->
            <div class="mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahGudangModal">
                    <i class="fas fa-plus"></i> Tambah Gudang
                </button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>

            <!-- Warehouse Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Gudang</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tabelGudang">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Kepala Gudang</th>
                                    <th>Kapasitas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($gudangList as $gudang): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($gudang['nama']); ?></td>
                                    <td><?= htmlspecialchars($gudang['alamat']); ?></td>
                                    <td><?= htmlspecialchars($gudang['kepala_gudang']); ?></td>
                                    <td><?= number_format($gudang['kapasitas']); ?> m²</td>
                                    <td>
                                        <a href="detail-gudang.php?id=<?= $gudang['id_gudang']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit-gudang.php?id=<?= $gudang['id_gudang']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusGudang(<?= $gudang['id_gudang']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<!-- Tambah Gudang Modal -->
<div class="modal fade" id="tambahGudangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Gudang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Gudang</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kepala Gudang</label>
                        <input type="text" name="kepala_gudang" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kapasitas (m²)</label>
                        <input type="number" name="kapasitas" class="form-control" required min="0">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_gudang" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tabelGudang').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});

function hapusGudang(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data gudang ini?')) {
        // Implement delete functionality
        window.location.href = 'delete-gudang.php?id=' + id;
    }
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
