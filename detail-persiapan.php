<?php
// Include header
include 'includes/header.php';
include 'config/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: persiapan.php");
    exit();
}

$id_persiapan = intval($_GET['id']);

// Handle form submissions for HPP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_hpp':
                $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
                $jumlah = intval($_POST['jumlah']);
                $harga = floatval($_POST['harga']);
                $total = $jumlah * $harga;
                
                $sql = "INSERT INTO hpp (id_persiapan, nama_barang, jumlah, harga, total) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "isidd", $id_persiapan, $nama_barang, $jumlah, $harga, $total);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                break;
                
            case 'delete_hpp':
                $id_hpp = intval($_POST['id_hpp']);
                $sql = "DELETE FROM hpp WHERE id = ? AND id_persiapan = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $id_hpp, $id_persiapan);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                break;
                
            case 'update_hpp':
                $id_hpp = intval($_POST['id_hpp']);
                $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
                $jumlah = intval($_POST['jumlah']);
                $harga = floatval($_POST['harga']);
                $total = $jumlah * $harga;
                
                $sql = "UPDATE hpp SET nama_barang = ?, jumlah = ?, harga = ?, total = ? WHERE id = ? AND id_persiapan = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sidiii", $nama_barang, $jumlah, $harga, $total, $id_hpp, $id_persiapan);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                break;
        }
        
        // Redirect to avoid form resubmission
        header("Location: detail-persiapan.php?id=$id_persiapan");
        exit();
    }
}

// Get persiapan details
$sql = "SELECT p.*, s.customer as nama_customer, s.sps_no as kode_sps, s.kirim as tanggal_kirim 
        FROM persiapan p 
        JOIN sps s ON p.id_sps = s.id 
        WHERE p.id = ?";
        
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $id_persiapan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$persiapan = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$persiapan) {
    header("Location: persiapan.php");
    exit();
}

// Get HPP items for this persiapan
$sql_hpp = "SELECT * FROM hpp WHERE id_persiapan = ? ORDER BY created_at DESC";
$stmt_hpp = mysqli_prepare($conn, $sql_hpp);
mysqli_stmt_bind_param($stmt_hpp, "i", $id_persiapan);
mysqli_stmt_execute($stmt_hpp);
$result_hpp = mysqli_stmt_get_result($stmt_hpp);
$hpp_items = mysqli_fetch_all($result_hpp, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_hpp);

// Calculate total HPP
$total_hpp = 0;
foreach ($hpp_items as $item) {
    $total_hpp += $item['total'];
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Detail Persiapan</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="persiapan.php">Persiapan</a></li>
                    <li class="breadcrumb-item active">Detail Persiapan</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Persiapan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Kode SPS</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($persiapan['kode_sps']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Customer</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($persiapan['nama_customer']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Tanggal Kirim</strong></label>
                                        <p class="form-control-plaintext"><?= date('d/m/Y', strtotime($persiapan['tanggal_kirim'])); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Status</strong></label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-<?= $persiapan['status'] == 'selesai' ? 'success' : ($persiapan['status'] == 'proses' ? 'warning' : 'secondary'); ?>">
                                                <?= ucfirst($persiapan['status']); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Keterangan</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($persiapan['keterangan'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Daftar HPP (Harga Pokok Produksi)</h5>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm text-white d-flex align-items-center" 
                                    data-bs-toggle="modal" data-bs-target="#addHppModal">
                                <i class="fas fa-plus me-1"></i> <!-- icon plus -->
                                <span>Tambah HPP</span>          <!-- teks -->
                            </button>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Harga Satuan</th>
                                            <th>Total</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($hpp_items) > 0): ?>
                                            <?php foreach ($hpp_items as $index => $item): ?>
                                                <tr>
                                                    <td><?= $index + 1; ?></td>
                                                    <td><?= htmlspecialchars($item['nama_barang']); ?></td>
                                                    <td><?= number_format($item['jumlah']); ?></td>
                                                    <td><?= htmlspecialchars($item['satuan']); ?></td>
                                                    <td>Rp <?= number_format($item['harga'], 0, ',', '.'); ?></td>
                                                    <td>Rp <?= number_format($item['total'], 0, ',', '.'); ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-warning" onclick="editHpp(<?= $item['id']; ?>, '<?= addslashes($item['nama_barang']); ?>', <?= $item['jumlah']; ?>, <?= $item['harga']; ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                                                            <input type="hidden" name="action" value="delete_hpp">
                                                            <input type="hidden" name="id_hpp" value="<?= $item['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Belum ada data HPP</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <th colspan="4" class="text-end">Total HPP:</th>
                                            <th>Rp <?= number_format($total_hpp, 0, ',', '.'); ?></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Aksi</h5>
                        </div>
                        <div class="card-body">
                            <a href="edit-persiapan.php?id=<?= $persiapan['id']; ?>" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                            <a href="update-status-persiapan.php?id=<?= $persiapan['id']; ?>" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-sync"></i> Update Status
                            </a>
                            <button type="button" class="btn btn-danger w-100" onclick="hapusPersiapan(<?= $persiapan['id']; ?>)">
                                <i class="fas fa-trash"></i> Hapus Data
                            </button>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Tambahan</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Dibuat pada:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($persiapan['created_at'])); ?></p>
                            
                            <?php if ($persiapan['updated_at']): ?>
                                <p><strong>Diupdate pada:</strong><br>
                                <?= date('d/m/Y H:i', strtotime($persiapan['updated_at'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add HPP Modal -->
<div class="modal fade" id="addHppModal" tabindex="-1" aria-labelledby="addHppModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addHppModalLabel">Tambah HPP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_hpp">
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga Satuan</label>
                        <input type="number" class="form-control" id="harga" name="harga" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit HPP Modal -->
<div class="modal fade" id="editHppModal" tabindex="-1" aria-labelledby="editHppModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHppModalLabel">Edit HPP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_hpp">
                    <input type="hidden" name="id_hpp" id="edit_id_hpp">
                    <div class="mb-3">
                        <label for="edit_nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="edit_nama_barang" name="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="edit_jumlah" name="jumlah" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_harga" class="form-label">Harga Satuan</label>
                        <input type="number" class="form-control" id="edit_harga" name="harga" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function hapusPersiapan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data persiapan ini?')) {
        window.location.href = 'delete-persiapan.php?id=' + id;
    }
}

function editHpp(id, nama_barang, jumlah, harga) {
    document.getElementById('edit_id_hpp').value = id;
    document.getElementById('edit_nama_barang').value = nama_barang;
    document.getElementById('edit_jumlah').value = jumlah;
    document.getElementById('edit_harga').value = harga;
    
    var modal = new bootstrap.Modal(document.getElementById('editHppModal'));
    modal.show();
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
