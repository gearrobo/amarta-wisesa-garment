<?php
// Start session
session_start();

// Include database configuration
include 'config/db.php';

// Include header
include 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah_produk'])) {
        // Start transaction
        mysqli_autocommit($conn, FALSE);
        
        try {
            // Insert product data
            $sql = "INSERT INTO produk (kode_produk, nama_produk, id_kategori, harga, deskripsi, status) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt, "ssidss", 
                $_POST['kode_produk'],
                $_POST['nama_produk'],
                $_POST['id_kategori'],
                $_POST['harga'],
                $_POST['deskripsi'],
                $_POST['status']
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
            }
            
            mysqli_stmt_close($stmt);
            
            // Commit transaction
            mysqli_commit($conn);
            
            $_SESSION['success'] = "Data produk berhasil ditambahkan!";
            header("Location: data-produk.php");
            exit();
            
        } catch(Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal menambahkan data produk: " . $e->getMessage();
        }
    }
    
    // Handle delete
    if (isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id_produk'])) {
        $id_produk = intval($_POST['id_produk']);
        
        try {
            $sql = "DELETE FROM produk WHERE id_produk = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_produk);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Data produk berhasil dihapus!";
            } else {
                throw new Exception("Gagal menghapus data produk: " . mysqli_stmt_error($stmt));
            }
            
            mysqli_stmt_close($stmt);
            
        } catch(Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header("Location: data-produk.php");
        exit();
    }
}

$sql = "SELECT p.*, k.nama_kategori 
        FROM produk p 
        LEFT JOIN kategori_barang k ON p.id_kategori = k.id_kategori 
        ORDER BY p.nama_produk";
$result = mysqli_query($conn, $sql);

if ($result) {
    $produkList = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $_SESSION['error'] = "Gagal mengambil data produk: " . mysqli_error($conn);
    $produkList = []; // Ensure it's an empty array to avoid warnings
}

// Get kategori list
$sql = "SELECT * FROM kategori_barang ORDER BY nama_kategori";
$result = mysqli_query($conn, $sql);
$kategoriList = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Data Produk</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item active">Data Produk</li>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahProdukModal">
                    <i class="fas fa-plus"></i> Tambah Produk
                </button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>

            <!-- Product Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Produk</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelProduk" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($produkList as $produk): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($produk['kode_produk']); ?></td>
                                    <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                                    <td><?= htmlspecialchars($produk['nama_kategori'] ?? '-'); ?></td>
                                    <td>Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge bg-<?= $produk['status'] == 'aktif' ? 'success' : 'secondary'; ?>">
                                            <?= ucfirst($produk['status']); ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($produk['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="detail-produk.php?id=<?= $produk['id_produk']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit-produk.php?id=<?= $produk['id_produk']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="hapusProduk(<?= $produk['id_produk']; ?>, '<?= htmlspecialchars($produk['nama_produk']); ?>')">
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

<!-- Tambah Produk Modal -->
<div class="modal fade" id="tambahProdukModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Produk</label>
                                <input type="text" name="kode_produk" class="form-control" required 
                                       placeholder="Contoh: PRD001">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" name="nama_produk" class="form-control" required 
                                       placeholder="Contoh: Kaos Polos L">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="id_kategori" class="form-select">
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kategoriList as $kategori): ?>
                                    <option value="<?= $kategori['id_kategori']; ?>">
                                        <?= htmlspecialchars($kategori['nama_kategori']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harga</label>
                                <input type="number" name="harga" class="form-control" required 
                                       placeholder="Contoh: 50000" min="0" step="1000">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" 
                                  placeholder="Deskripsi produk..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_produk" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form method="POST" id="deleteForm" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id_produk" id="deleteId">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabelProduk').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        columnDefs: [
            { orderable: false, targets: [7] } // Disable sorting on action column
        ]
    });
});

function hapusProduk(id, nama) {
    if (confirm('Apakah Anda yakin ingin menghapus produk "' + nama + '"?')) {
        $('#deleteId').val(id);
        $('#deleteForm').submit();
    }
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
