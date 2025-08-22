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
    
    if (isset($_POST['tambah_inventory_gudang'])) {
        // Start transaction
        mysqli_autocommit($conn, FALSE);
        
        try {
            // Insert inventory gudang data with stok_akhir
            $sql = "INSERT INTO inventory_gudang (nama_barang, id_gudang, jumlah, stok_akhir, satuan, tanggal_masuk) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt, "siisss", 
                $_POST['nama_barang'],
                $_POST['id_gudang'],
                $_POST['jumlah'],
                $_POST['jumlah'], // stok_akhir sama dengan jumlah awal
                $_POST['satuan'],
                $_POST['tanggal_masuk']
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
            }
            
            $inventory_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            // Insert initial transaction record
            $sql_transaksi = "INSERT INTO inventory_transaksi_gudang 
                            (inventory_gudang_id, jenis, jumlah_masuk, keterangan, tanggal_transaksi, user_id) 
                            VALUES (?, 'masuk', ?, 'Stok awal masuk', ?, ?)";
            
            $stmt_transaksi = mysqli_prepare($conn, $sql_transaksi);
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            mysqli_stmt_bind_param($stmt_transaksi, "issi", 
                $inventory_id,
                $_POST['jumlah'],
                $_POST['tanggal_masuk'],
                $user_id
            );
            
            if (!mysqli_stmt_execute($stmt_transaksi)) {
                throw new Exception("Transaksi failed: " . mysqli_stmt_error($stmt_transaksi));
            }
            
            mysqli_stmt_close($stmt_transaksi);
            
            // Commit transaction
            mysqli_commit($conn);
            
            $_SESSION['success'] = "Data inventory gudang berhasil ditambahkan!";
            header("Location: data-gudang.php#inventory-gudang");
            exit();
            
        } catch(Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal menambahkan data inventory gudang: " . $e->getMessage();
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

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs" id="gudangTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= (!isset($_GET['filter_gudang']) && !isset($_GET['search_nama'])) ? 'active' : '' ?>" id="daftar-gudang-tab" data-bs-toggle="tab" data-bs-target="#daftar-gudang" type="button" role="tab">
                        <i class="fas fa-warehouse"></i> Daftar Gudang
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= (isset($_GET['filter_gudang']) || isset($_GET['search_nama'])) ? 'active' : '' ?>" id="inventory-gudang-tab" data-bs-toggle="tab" data-bs-target="#inventory-gudang" type="button" role="tab">
                        <i class="fas fa-boxes"></i> Inventory Gudang
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="gudangTabsContent">
                <!-- Daftar Gudang Tab -->
                <div class="tab-pane fade <?= (!isset($_GET['filter_gudang']) && !isset($_GET['search_nama'])) ? 'show active' : '' ?>" id="daftar-gudang" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Daftar Gudang</h5>
                        
                        </div>
                        <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahGudangModal">
                                    <i class="fas fa-plus"></i> <strong>TAMBAH DAFTAR GUDANG</strong>
                                </button>
                                <button type="button" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> <strong>EXPORT EXCEL</strong>
                                </button>
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
                                                <a href="detail-gudang.php?id=<?= $gudang['id_gudang']; ?>" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit-gudang.php?id=<?= $gudang['id_gudang']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="hapusGudang(<?= $gudang['id_gudang']; ?>)" title="Hapus">
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

                <!-- Inventory Gudang Tab -->
                <div class="tab-pane fade <?= (isset($_GET['filter_gudang']) || isset($_GET['search_nama'])) ? 'show active' : '' ?>" id="inventory-gudang" role="tabpanel">
                    <?php
                    // Handle filter parameters
                    $filter_gudang = isset($_GET['filter_gudang']) ? $_GET['filter_gudang'] : '';
                    $search_nama = isset($_GET['search_nama']) ? $_GET['search_nama'] : '';
                    
                    // Build query with filters
                    $inventoryGudangQuery = "
                        SELECT ig.*, g.nama as nama_gudang,
                               COALESCE(ig.stok_akhir, ig.jumlah) as stok_tersedia
                        FROM inventory_gudang ig 
                        JOIN gudang g ON ig.id_gudang = g.id_gudang 
                        WHERE 1=1
                    ";
                    
                    if (!empty($filter_gudang)) {
                        $inventoryGudangQuery .= " AND ig.id_gudang = " . intval($filter_gudang);
                    }
                    
                    if (!empty($search_nama)) {
                        $search_nama_escaped = mysqli_real_escape_string($conn, $search_nama);
                        $inventoryGudangQuery .= " AND ig.nama_barang LIKE '%$search_nama_escaped%'";
                    }
                    
                    $inventoryGudangQuery .= " ORDER BY ig.nama_barang ASC";
                    
                    $inventoryGudangResult = mysqli_query($conn, $inventoryGudangQuery);
                    $inventoryGudangList = mysqli_fetch_all($inventoryGudangResult, MYSQLI_ASSOC);
                    ?>
                    
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Inventory Gudang</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahInventoryGudangModal">
                                <i class="fas fa-plus"></i> <strong>TAMBAH INVENTORY GUDANG</strong>
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Filter Controls -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="GET" class="row g-2">
                                        <input type="hidden" name="tab" value="inventory-gudang">
                                        <div class="col-md-5">
                                            <select name="filter_gudang" class="form-control" onchange="this.form.submit()">
                                                <option value="">Semua Gudang</option>
                                                <?php foreach ($gudangList as $gudang): ?>
                                                <option value="<?= $gudang['id_gudang']; ?>" <?= ($filter_gudang == $gudang['id_gudang']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($gudang['nama']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" name="search_nama" class="form-control" placeholder="Cari nama barang..." 
                                                   value="<?= htmlspecialchars($search_nama); ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tabelInventoryGudang">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Barang</th>
                                            <th>Gudang</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Tanggal Masuk</th>
                                            <!-- <th>Aksi</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($inventoryGudangList as $item): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($item['nama_barang']); ?></td>
                                            <td><?= htmlspecialchars($item['nama_gudang']); ?></td>
                                            <td><?= number_format($item['jumlah']); ?></td>
                                            <td><?= htmlspecialchars($item['satuan']); ?></td>
                                            <td><?= $item['tanggal_masuk'] ? date('d/m/Y', strtotime($item['tanggal_masuk'])) : '-'; ?></td>
                                            <!-- <td>
                                                <button type="button" class="btn btn-sm btn-info" onclick="detailInventoryGudang(<?= $item['id_inventory']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editInventoryGudang(<?= $item['id_inventory']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="hapusInventoryGudang(<?= $item['id_inventory']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td> -->
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

<!-- Tambah Inventory Gudang Modal -->
<div class="modal fade" id="tambahInventoryGudangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Inventory Gudang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gudang</label>
                        <select name="id_gudang" class="form-control" required>
                            <option value="">Pilih Gudang</option>
                            <?php foreach ($gudangList as $gudang): ?>
                            <option value="<?= $gudang['id_gudang']; ?>"><?= htmlspecialchars($gudang['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" required min="0">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_inventory_gudang" class="btn btn-primary">Simpan</button>
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
    
    $('#tabelInventoryGudang').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });

    // Check if we have filter parameters and activate the inventory tab
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('filter_gudang') || urlParams.has('search_nama')) {
        // Activate the inventory gudang tab
        const inventoryTab = new bootstrap.Tab(document.getElementById('inventory-gudang-tab'));
        inventoryTab.show();
        
        // Also update the URL hash to maintain tab state
        window.location.hash = 'inventory-gudang';
    }
});

function hapusGudang(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data gudang ini?')) {
        // Implement delete functionality
        window.location.href = 'delete-gudang.php?id=' + id;
    }
}

function detailInventoryGudang(id) {
    window.location.href = 'detail-inventory-gudang.php?id=' + id;
}

function editInventoryGudang(id) {
    window.location.href = 'edit-inventory-gudang.php?id=' + id;
}

function hapusInventoryGudang(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data inventory gudang ini?')) {
        window.location.href = 'delete-inventory-gudang.php?id=' + id;
    }
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>