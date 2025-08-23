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
                $gudang = intval($_POST['gudang'] ?? 0);
                $kategori_barang = intval($_POST['kategori_barang'] ?? 0);
                $jumlah = intval($_POST['jumlah'] ?? 0);
                $satuan = trim($_POST['satuan'] ?? '');
                $barang_jadi = intval($_POST['barang_jadi'] ?? 0);
                $consp = floatval($_POST['consp'] ?? 0);
                $stok_material = intval($_POST['stok_material'] ?? 0);
                $purchase_order = trim($_POST['purchase_order'] ?? '');
                $sppo = trim($_POST['sppo'] ?? '');
                $harga = floatval($_POST['harga'] ?? 0);

                $sql = "INSERT INTO hpp 
                        (id_persiapan, gudang, kategori_barang, jumlah, satuan, barang_jadi, consp, stok_material, purchase_order, sppo, harga) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param(
                    $stmt, "iiiisidsssd",
                    $id_persiapan, $gudang, $kategori_barang, $jumlah, $satuan,
                    $barang_jadi, $consp, $stok_material, $purchase_order, $sppo, $harga
                );
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                break;

            case 'delete_hpp':
                $id_hpp = intval($_POST['id_hpp'] ?? 0);
                $sql = "DELETE FROM hpp WHERE id = ? AND id_persiapan = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $id_hpp, $id_persiapan);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                break;

            case 'update_hpp':
                $id_hpp = intval($_POST['id_hpp'] ?? 0);
                $gudang = intval($_POST['gudang'] ?? 0);
                $kategori_barang = intval($_POST['kategori_barang'] ?? 0);
                $jumlah = intval($_POST['jumlah'] ?? 0);
                $satuan = trim($_POST['satuan'] ?? '');
                $barang_jadi = intval($_POST['barang_jadi'] ?? 0);
                $consp = floatval($_POST['consp'] ?? 0);
                $stok_material = intval($_POST['stok_material'] ?? 0);
                $purchase_order = trim($_POST['purchase_order'] ?? '');
                $sppo = trim($_POST['sppo'] ?? '');
                $harga = floatval($_POST['harga'] ?? 0);

                $sql = "UPDATE hpp 
                        SET gudang=?, kategori_barang=?, jumlah=?, satuan=?, barang_jadi=?, consp=?, stok_material=?, purchase_order=?, sppo=?, harga=? 
                        WHERE id=? AND id_persiapan=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param(
                    $stmt, "iiisidsssiii",
                    $gudang, $kategori_barang, $jumlah, $satuan, $barang_jadi, $consp,
                    $stok_material, $purchase_order, $sppo, $harga, $id_hpp, $id_persiapan
                );
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

// Get HPP items
$sql_hpp = "SELECT h.*, g.nama as nama_gudang, k.nama_kategori as nama_kategori
            FROM hpp h
            LEFT JOIN gudang g ON h.gudang = g.id_gudang
            LEFT JOIN kategori_barang k ON h.kategori_barang = k.id_kategori
            WHERE h.id_persiapan = ? ORDER BY h.created_at DESC";
$stmt_hpp = mysqli_prepare($conn, $sql_hpp);
mysqli_stmt_bind_param($stmt_hpp, "i", $id_persiapan);
mysqli_stmt_execute($stmt_hpp);
$result_hpp = mysqli_stmt_get_result($stmt_hpp);
$hpp_items = mysqli_fetch_all($result_hpp, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_hpp);

// Calculate total HPP
$total_hpp = 0;
foreach ($hpp_items as $item) {
    $jumlah = floatval($item['jumlah'] ?? 0);
    $harga = floatval($item['harga'] ?? 0);
    $total_hpp += $jumlah * $harga;
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
                                        <label class="form-label"><strong>Nama Barang</strong></label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($persiapan['nama_barang'] ?? '-'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Jumlah</strong></label>
                                        <p class="form-control-plaintext"><?= number_format($persiapan['jumlah'] ?? 0); ?> <?= htmlspecialchars($persiapan['satuan'] ?? ''); ?></p>
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
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHppModal">
                                <i class="fas fa-plus"></i> Tambah HPP
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Gudang</th>
                                            <th>Kategori Barang</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Barang Jadi</th>
                                            <th>Consp</th>
                                            <th>Stok Material</th>
                                            <th>Purchase Order</th>
                                            <th>SPPO</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($hpp_items) > 0): ?>
                                            <?php foreach ($hpp_items as $index => $item): ?>
                                                <tr>
                                                    <td><?= $index + 1; ?></td>
                                                    <td><?= htmlspecialchars($item['nama_gudang'] ?? '-'); ?></td>
                                                    <td><?= htmlspecialchars($item['nama_kategori'] ?? '-'); ?></td>
                                                    <td><?= number_format($item['jumlah']); ?></td>
                                                    <td><?= htmlspecialchars($item['satuan'] ?? '-'); ?></td>
                                                    <td><?= htmlspecialchars($item['barang_jadi'] ?? '-'); ?></td>
                                                    <td><?= htmlspecialchars($item['consp'] ?? '-'); ?></td>
                                                    <td><?= htmlspecialchars($item['stok_material'] ?? '-'); ?></td>
                                                    <td><?= htmlspecialchars($item['purchase_order'] ?? '-'); ?></td>
                                                    <td><?= htmlspecialchars($item['sppo'] ?? '-'); ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-warning" onclick="editHpp(<?= $item['id']; ?>, <?= $item['gudang']; ?>, <?= $item['kategori_barang']; ?>, <?= $item['jumlah']; ?>, '<?= addslashes($item['satuan']); ?>', <?= $item['barang_jadi']; ?>, <?= $item['consp']; ?>)">
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
                                                <td colspan="11" class="text-center">Belum ada data HPP</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <!-- <tfoot>
                                        <tr class="table-active">
                                            <th colspan="4" class="text-end">Total HPP:</th>
                                            <th>Rp <?= number_format($total_hpp, 0, ',', '.'); ?></th>
                                            <th></th>
                                        </tr>
                                    </tfoot> -->
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
                        <label for="gudang" class="form-label">Gudang</label>
                        <select class="form-control" id="gudang" name="gudang" required>
                            <option value="">Pilih Gudang</option>
                            <?php
                            $sql_gudang = "SELECT * FROM gudang ORDER BY nama";
                            $result_gudang = mysqli_query($conn, $sql_gudang);
                            while ($gudang = mysqli_fetch_assoc($result_gudang)) {
                                echo '<option value="' . $gudang['id_gudang'] . '">' . htmlspecialchars($gudang['nama']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kategori_barang" class="form-label">Kategori Barang</label>
                        <select class="form-control" id="kategori_barang" name="kategori_barang" required>
                            <option value="">Pilih Kategori</option>
                            <?php
                            $sql_kategori = "SELECT * FROM kategori_barang ORDER BY nama_kategori";
                            $result_kategori = mysqli_query($conn, $sql_kategori);
                            while ($kategori = mysqli_fetch_assoc($result_kategori)) {
                                echo '<option value="' . $kategori['id_kategori'] . '">' . htmlspecialchars($kategori['nama_kategori']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="satuan" class="form-label">Satuan</label>
                        <select class="form-control" id="satuan" name="satuan" required>
                            <option value="pcs">Pcs</option>
                            <option value="m">Meter</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="barang_jadi" class="form-label">Barang Jadi</label>
                        <input type="number" class="form-control" id="barang_jadi" name="barang_jadi" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="consp" class="form-label">Consp</label>
                        <input type="number" class="form-control" id="consp" name="consp" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="stok_material" class="form-label">Stok Material</label>
                        <input type="number" class="form-control" id="stok_material" name="stok_material" min="0" readonly>
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
                        <label for="edit_gudang" class="form-label">Gudang</label>
                        <select class="form-control" id="edit_gudang" name="gudang" required>
                            <option value="">Pilih Gudang</option>
                            <?php
                            $sql_gudang = "SELECT * FROM gudang ORDER BY nama";
                            $result_gudang = mysqli_query($conn, $sql_gudang);
                            while ($gudang = mysqli_fetch_assoc($result_gudang)) {
                                echo '<option value="' . $gudang['id_gudang'] . '">' . htmlspecialchars($gudang['nama']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_kategori_barang" class="form-label">Kategori Barang</label>
                        <select class="form-control" id="edit_kategori_barang" name="kategori_barang" required>
                            <option value="">Pilih Kategori</option>
                            <?php
                            $sql_kategori = "SELECT * FROM kategori_barang ORDER BY nama_kategori";
                            $result_kategori = mysqli_query($conn, $sql_kategori);
                            while ($kategori = mysqli_fetch_assoc($result_kategori)) {
                                echo '<option value="' . $kategori['id_kategori'] . '">' . htmlspecialchars($kategori['nama_kategori']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="edit_jumlah" name="jumlah" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_satuan" class="form-label">Satuan</label>
                        <select class="form-control" id="edit_satuan" name="satuan" required>
                            <option value="pcs">Pcs</option>
                            <option value="m">Meter</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_barang_jadi" class="form-label">Barang Jadi</label>
                        <input type="number" class="form-control" id="edit_barang_jadi" name="barang_jadi" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_consp" class="form-label">Consp</label>
                        <input type="number" class="form-control" id="edit_consp" name="consp" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stok_material" class="form-label">Stok Material</label>
                        <input type="number" class="form-control" id="edit_stok_material" name="stok_material" min="0" readonly>
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

function editHpp(id, gudang, kategori_barang, jumlah, satuan, barang_jadi, consp) {
    document.getElementById('edit_id_hpp').value = id;
    document.getElementById('edit_gudang').value = gudang;
    document.getElementById('edit_kategori_barang').value = kategori_barang;
    document.getElementById('edit_jumlah').value = jumlah;
    document.getElementById('edit_satuan').value = satuan;
    document.getElementById('edit_barang_jadi').value = barang_jadi;
    document.getElementById('edit_consp').value = consp;
    
    var modal = new bootstrap.Modal(document.getElementById('editHppModal'));
    modal.show();
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to handle stock quantity fetching for both modals
    function setupStockQuantityFetching(gudangSelectId, kategoriSelectId, stokMaterialInputId) {
        const gudangSelect = document.getElementById(gudangSelectId);
        const kategoriSelect = document.getElementById(kategoriSelectId);
        const stokMaterialInput = document.getElementById(stokMaterialInputId);

        function fetchStockQuantity() {
            const gudangId = gudangSelect.value;
            const kategoriId = kategoriSelect.value;

            if (gudangId && kategoriId) {
                fetch(`get_stock_quantity.php?gudang_id=${gudangId}&kategori_id=${kategoriId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            stokMaterialInput.value = data.stok_material;
                        } else {
                            stokMaterialInput.value = 0; // Reset if no stock found
                        }
                    })
                    .catch(error => console.error('Error fetching stock quantity:', error));
            } else {
                stokMaterialInput.value = ''; // Clear if no selection
            }
        }

        if (gudangSelect && kategoriSelect && stokMaterialInput) {
            gudangSelect.addEventListener('change', fetchStockQuantity);
            kategoriSelect.addEventListener('change', fetchStockQuantity);
            
            // Fetch initial value if both are already selected
            if (gudangSelect.value && kategoriSelect.value) {
                fetchStockQuantity();
            }
        }
    }

    // Setup for Add HPP Modal
    setupStockQuantityFetching('gudang', 'kategori_barang', 'stok_material');
    
    // Setup for Edit HPP Modal when it's shown
    const editHppModal = document.getElementById('editHppModal');
    if (editHppModal) {
        editHppModal.addEventListener('shown.bs.modal', function () {
            setupStockQuantityFetching('edit_gudang', 'edit_kategori_barang', 'edit_stok_material');
        });
    }
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
