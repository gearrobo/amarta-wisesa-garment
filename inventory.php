<?php
// db.php
include "includes/header.php";
include "config/db.php";

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses simpan inventory
$message = '';
$messageType = '';

if (isset($_POST['save'])) {
    // Validasi input required
    $required_fields = ['nama_barang', 'warehouse', 'unit', 'jumlah', 'harga_per_unit'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $message = 'Error: Field berikut wajib diisi: ' . implode(', ', $missing_fields);
        $messageType = 'danger';
    } else {
        // Ambil data dengan aman
        $nama_barang = $_POST['nama_barang'];
        $warehouse = $_POST['warehouse'];
        $unit = $_POST['unit'];
        $jumlah = intval($_POST['jumlah']);
        $harga_per_unit = floatval($_POST['harga_per_unit']);
        $keterangan = $_POST['keterangan'] ?? '';

        // Gunakan prepared statement untuk keamanan
        $sql = "INSERT INTO inventory 
                (nama_barang, warehouse, unit, jumlah, harga_per_unit, keterangan, created_at)
                VALUES 
                (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssids", 
                $nama_barang, $warehouse, $unit, $jumlah, $harga_per_unit, $keterangan
            );
            
            if ($stmt->execute()) {
                $message = 'Data inventory berhasil disimpan!';
                $messageType = 'success';
                
                // Redirect untuk mencegah duplicate submission
                header("Location: inventory.php?success=1");
                exit();
            } else {
                $message = 'Error: Gagal menyimpan data - ' . $stmt->error;
                $messageType = 'danger';
            }
            $stmt->close();
        } else {
            $message = 'Error: Gagal prepare statement - ' . $conn->error;
            $messageType = 'danger';
        }
    }
}

// Cek parameter success dari redirect
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = 'Data inventory berhasil disimpan!';
    $messageType = 'success';
}

// Ambil data inventory
$result = $conn->query("SELECT * FROM inventory ORDER BY id DESC");

// Ambil data gudang
$nama_gudang = $conn->query("SELECT * FROM gudang ");

// Hitung total stok barang
$total_stok = $conn->query("SELECT SUM(jumlah) as total FROM inventory")->fetch_assoc()['total'] ?? 0;

// Hitung jumlah barang keluar (dengan asumsi ada tabel transaksi)
$barang_keluar = $conn->query("SELECT SUM(jumlah_keluar) as total FROM inventory_transaksi WHERE jenis = 'keluar'")->fetch_assoc()['total'] ?? 0;

// Hitung jumlah barang masuk (total jenis barang/nama barang yang berbeda)
$barang_masuk = $conn->query("SELECT COUNT(DISTINCT nama_barang) as total FROM inventory")->fetch_assoc()['total'] ?? 0;

// Hitung jumlah barang masuk dari transaksi (dengan asumsi ada tabel transaksi)
$barang_masuk_transaksi = $conn->query("SELECT SUM(jumlah_masuk) as total FROM inventory_transaksi WHERE jenis = 'masuk'")->fetch_assoc()['total'] ?? 0;

// Jika tabel inventory_transaksi belum ada, gunakan jumlah jenis barang
if ($barang_keluar == 0 && $barang_masuk_transaksi == 0) {
    // Jangan ubah barang_masuk, biarkan sebagai jumlah jenis barang
    $barang_keluar = 0;
}
?>

    <!-- Main Content -->
    <main class="main-content">
       
            <div>
                <h1 class="h3 mb-4">Inventory Barang</h1>
                
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Inventory Barang</li>
                    </ol>
                </nav>
            </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <h3><?php echo number_format($total_stok); ?></h3>
                    <p>Stok Barang</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h3><?php echo number_format($barang_keluar); ?></h3>
                    <p>Jumlah Keluar</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h3><?php echo number_format($barang_masuk); ?></h3>
                    <p>Jumlah Masuk</p>
                </div>
            </div>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inventoryModal">
                <i class="fas fa-plus"></i> Tambah Barang
            </button>
        </div><br>

        <!-- Alerts -->
        <?php
        if (isset($_GET['updated'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Data berhasil diupdate!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
        if (isset($_GET['deleted'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Data berhasil dihapus!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
        ?>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Warehouse</th>
                            <th>Unit</th>
                            <th>Jumlah</th>
                            <th>Harga per Unit</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= $row['nama_barang'] ?></strong></td>
                            <td><?= $row['warehouse'] ?></td>
                            <td><?= $row['unit'] ?></td>
                            <td><?= number_format($row['jumlah']) ?></td>
                            <td>Rp <?= number_format($row['harga_per_unit'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['jumlah'] * $row['harga_per_unit'], 0, ',', '.') ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-warning" onclick="editInventory(<?= $row['id'] ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteInventory(<?= $row['id'] ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-info" onclick="detailInventory(<?= $row['id'] ?>)" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

<!-- Modal Form Input -->
<div class="modal fade" id="inventoryModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Barang Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">

        <div class="col-md-6">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>Warehouse</label>
            <select name="warehouse" id="warehouse" class="form-control" required onchange="loadGudang()">
                <?php $no=1; while($row_dg = $nama_gudang->fetch_assoc()): ?>
                <option value="">Pilih Warehouse</option>
                <option value=<?= $row_dg['nama']; ?>><?= $row_dg['nama']; ?></option>
             <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label>Gudang</label>
            <select name="gudang" id="gudang" class="form-control" required>
                <option value="">Pilih Gudang</option>
            </select>
        </div>
        <div class="col-md-4">
            <label>Unit</label>
            <select name="unit" class="form-control" required>
                <option value="">Pilih Unit</option>
                <option value="pcs">pcs</option>
                <option value="m²">m²</option>
            </select>
        </div>
        <div class="col-md-4">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required min="0">
        </div>
        <div class="col-md-4">
            <label>Harga per Unit</label>
            <input type="number" name="harga_per_unit" class="form-control" required min="0">
        </div>
        <div class="col-md-12">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3"></textarea>
        </div>

      </div>
      <div class="modal-footer">
        <button type="submit" name="save" class="btn btn-success">Simpan</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// JavaScript functions for action buttons
function editInventory(id) {
    window.location.href = 'edit-inventory.php?id=' + id;
}

function deleteInventory(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = 'delete-inventory.php?id=' + id;
    }
}

function detailInventory(id) {
    window.location.href = 'detail-inventory.php?id=' + id;
}
</script>

<?php include 'includes/footer.php'; ?>
