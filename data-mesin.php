<?php
// Include database configuration
include 'config/db.php';

// Include header
include 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah_mesin'])) {
        // Start transaction
        mysqli_autocommit($conn, FALSE);
        
        try {
            // Insert machine data
            $sql = "INSERT INTO mesin (seri_number, nama_mesin, lokasi, tanggal_masuk, keterangan) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt, "sssss", 
                $_POST['seri_number'],
                $_POST['nama_mesin'],
                $_POST['lokasi'],
                $_POST['tanggal_masuk'],
                $_POST['keterangan']
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
            }
            
            mysqli_stmt_close($stmt);
            
            // Commit transaction
            mysqli_commit($conn);
            
            $_SESSION['success'] = "Data mesin berhasil ditambahkan!";
            header("Location: data-mesin.php");
            exit();
            
        } catch(Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal menambahkan data mesin: " . $e->getMessage();
        }
    }
}

// Get all machines
$sql = "SELECT * FROM mesin ORDER BY tanggal_masuk DESC";
$result = mysqli_query($conn, $sql);
$mesinList = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Data Mesin</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item active">Data Mesin</li>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahMesinModal">
                    <i class="fas fa-plus"></i> Tambah Mesin
                </button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>

            <!-- Machine Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Mesin</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tabelMesin">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Seri Number</th>
                                    <th>Nama Mesin</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($mesinList as $mesin): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($mesin['seri_number']); ?></td>
                                    <td><?= htmlspecialchars($mesin['nama_mesin']); ?></td>
                                    <td><?= htmlspecialchars($mesin['lokasi']); ?></td>
                                    <td><?= date('d/m/Y', strtotime($mesin['tanggal_masuk'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-info" onclick="detailMesin(<?= $mesin['id']; ?>)" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-warning" onclick="editMesin(<?= $mesin['id']; ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger" onclick="hapusMesin(<?= $mesin['id']; ?>)" title="Hapus">
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

<!-- Tambah Mesin Modal -->
<div class="modal fade" id="tambahMesinModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Mesin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Seri Number</label>
                        <input type="text" name="seri_number" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Mesin</label>
                        <input type="text" name="nama_mesin" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_mesin" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
<script>
$(document).ready(function() {
    $('#tabelMesin').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});

function hapusMesin(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data mesin ini?')) {
        window.location.href = 'delete-mesin.php?id=' + id;
    }
}

function editMesin(id) {
    window.location.href = 'edit-mesin.php?id=' + id;
}

function detailMesin(id) {
    window.location.href = 'detail-mesin.php?id=' + id;
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
