<?php
// Start session
session_start();

// Include database configuration
include 'config/db.php';

// Include header
include 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah_departemen'])) {
        // Start transaction
        mysqli_autocommit($conn, FALSE);
        
        try {
            // Insert department data
            $sql = "INSERT INTO departemen (nama_departemen, deskripsi) 
                    VALUES (?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt, "ss", 
                $_POST['nama_departemen'],
                $_POST['deskripsi']
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
            }
            
            mysqli_stmt_close($stmt);
            
            // Commit transaction
            mysqli_commit($conn);
            
            $_SESSION['success'] = "Data departemen berhasil ditambahkan!";
            header("Location: data-departemen.php");
            exit();
            
        } catch(Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal menambahkan data departemen: " . $e->getMessage();
        }
    }
}

// Get all departments
$sql = "SELECT * FROM departemen ORDER BY nama_departemen";
$result = mysqli_query($conn, $sql);
$departemenList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get employees count by department
$sql = "SELECT d.id, d.nama_departemen, COUNT(k.id) as jumlah_karyawan 
        FROM departemen d 
        LEFT JOIN karyawan k ON d.id = k.id_departemen
        GROUP BY d.id 
        ORDER BY d.nama_departemen";
$result = mysqli_query($conn, $sql);
$karyawanByDept = [];
while ($row = mysqli_fetch_assoc($result)) {
    $karyawanByDept[$row['id']] = $row['jumlah_karyawan'];
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Data Departemen</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item active">Data Departemen</li>
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

            <!-- Department Tabs -->
            <div class="card mb-4">
                
                <div class="card-body">
                    <div class="tab-content" id="departmentTabsContent">
                        
                        <!-- Semua Departemen -->
                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                            <!-- Action Buttons -->
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDepartemenModal">
                                    <i class="fas fa-plus"></i> Tambah Departemen
                                </button>
                                <button type="button" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                            </div>

                            <!-- Department Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Departemen</th>
                                            <th>Deskripsi</th>
                                            <th>Jumlah Karyawan</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($departemenList as $departemen): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($departemen['nama_departemen']); ?></td>
                                            <td><?= htmlspecialchars($departemen['deskripsi']); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= isset($karyawanByDept[$departemen['id']]) ? $karyawanByDept[$departemen['id']] : 0; ?> karyawan
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($departemen['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="detail-departemen.php?id=<?= $departemen['id']; ?>" class="btn btn-info" title="Lihat Detail Departemen">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit-departemen.php?id=<?= $departemen['id']; ?>" class="btn btn-warning" title="Edit Departemen">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger" onclick="hapusDepartemen(<?= $departemen['id']; ?>)" title="Hapus Departemen">
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

                        <!-- HR -->
                        <div class="tab-pane fade" id="hr" role="tabpanel">
                            <p>Data Departemen HR akan tampil di sini.</p>
                        </div>

                        <!-- Keuangan -->
                        <div class="tab-pane fade" id="keuangan" role="tabpanel">
                            <p>Data Departemen Keuangan akan tampil di sini.</p>
                        </div>

                        <!-- Produksi -->
                        <div class="tab-pane fade" id="produksi" role="tabpanel">
                            <p>Data Departemen Produksi akan tampil di sini.</p>
                        </div>

                        <!-- Gudang -->
                        <div class="tab-pane fade" id="gudang" role="tabpanel">
                            <p>Data Departemen Gudang akan tampil di sini.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tambah Departemen Modal -->
<div class="modal fade" id="tambahDepartemenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Departemen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Departemen</label>
                        <input type="text" name="nama_departemen" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_departemen" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    // aktifkan DataTables untuk SEMUA tabel
    $('table').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});

function hapusDepartemen(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data departemen ini?')) {
        window.location.href = 'delete-departemen.php?id=' + id;
    }
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>