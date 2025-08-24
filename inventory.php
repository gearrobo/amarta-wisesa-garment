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
        $kode_barang = $_POST['kode_barang'] ?? '';
        $nama_barang = $_POST['nama_barang'];
        $warehouse = $_POST['warehouse'];
        $unit = $_POST['unit'];
        $jumlah = intval($_POST['jumlah']);
        $harga_per_unit = floatval($_POST['harga_per_unit']);
        $keterangan = $_POST['keterangan'] ?? '';

        // Mulai transaction
        $conn->begin_transaction();
        
        try {
            // 1. Simpan ke tabel inventory (sistem lama)
            $sql = "INSERT INTO inventory 
                    (kode_barang, nama_barang, warehouse, unit, jumlah, harga_per_unit, keterangan, created_at)
                    VALUES 
                    (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            // Jika kode_barang tidak diisi, gunakan auto-generated
            if (empty($kode_barang)) {
                // Simpan dulu untuk mendapatkan ID, lalu generate kode
                $stmt->bind_param("ssssids", 
                    null, $nama_barang, $warehouse, $unit, $jumlah, $harga_per_unit, $keterangan
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                
                $inventory_id = $stmt->insert_id;
                $kode_barang = 'INV' . str_pad($inventory_id, 4, '0', STR_PAD_LEFT);
                
                // Update dengan kode barang yang digenerate
                $sql_update_kode = "UPDATE inventory SET kode_barang = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update_kode);
                $stmt_update->bind_param("si", $kode_barang, $inventory_id);
                if (!$stmt_update->execute()) {
                    throw new Exception("Update kode barang failed: " . $stmt_update->error);
                }
                $stmt_update->close();
            } else {
                // Gunakan kode_barang yang diinput manual
                $stmt->bind_param("ssssids", 
                    $kode_barang, $nama_barang, $warehouse, $unit, $jumlah, $harga_per_unit, $keterangan
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                
                $inventory_id = $stmt->insert_id;
            }
            $stmt->close();

            // 2. Dapatkan ID gudang berdasarkan nama warehouse
            $sql_gudang = "SELECT id FROM gudang WHERE nama = ?";
            $stmt_gudang = $conn->prepare($sql_gudang);
            if (!$stmt_gudang) {
                throw new Exception("Prepare gudang failed: " . $conn->error);
            }
            
            $stmt_gudang->bind_param("s", $warehouse);
            if (!$stmt_gudang->execute()) {
                throw new Exception("Execute gudang failed: " . $stmt_gudang->error);
            }
            
            $result_gudang = $stmt_gudang->get_result();
            if ($result_gudang->num_rows === 0) {
                throw new Exception("Gudang '$warehouse' tidak ditemukan");
            }
            
            $gudang_data = $result_gudang->fetch_assoc();
            $id_gudang = $gudang_data['id'];
            $stmt_gudang->close();

            // 4. Cek apakah barang sudah ada di inventory_gudang
            $sql_check = "SELECT id_inventory, jumlah, stok_akhir FROM inventory_gudang 
                         WHERE nama_barang = ? AND id_gudang = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("si", $nama_barang, $id_gudang);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                // Update existing barang
                $existing_data = $result_check->fetch_assoc();
                $existing_id = $existing_data['id_inventory'];
                $existing_jumlah = $existing_data['jumlah'];
                $existing_stok = $existing_data['stok_akhir'];
                
                $new_jumlah = $existing_jumlah + $jumlah;
                $new_stok = $existing_stok + $jumlah;
                
                $sql_update = "UPDATE inventory_gudang 
                              SET jumlah = ?, stok_akhir = ?, tanggal_update = NOW()
                              WHERE id_inventory = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("iii", $new_jumlah, $new_stok, $existing_id);
                
                if (!$stmt_update->execute()) {
                    throw new Exception("Update inventory_gudang failed: " . $stmt_update->error);
                }
                $stmt_update->close();
                
                $inventory_gudang_id = $existing_id;
            } else {
                // Insert new barang ke inventory_gudang
                $sql_insert_gudang = "INSERT INTO inventory_gudang 
                                    (id_gudang, nama_barang, jumlah, stok_akhir, satuan, tanggal_masuk)
                                    VALUES (?, ?, ?, ?, ?, CURDATE())";
                
                $stmt_insert = $conn->prepare($sql_insert_gudang);
                $stmt_insert->bind_param("isiis", $id_gudang, $nama_barang, $jumlah, $jumlah, $unit);
                
                if (!$stmt_insert->execute()) {
                    throw new Exception("Insert inventory_gudang failed: " . $stmt_insert->error);
                }
                
                $inventory_gudang_id = $stmt_insert->insert_id;
                $stmt_insert->close();
            }

            // 5. Buat transaksi di inventory_transaksi_gudang
            $sql_transaksi = "INSERT INTO inventory_transaksi_gudang 
                            (inventory_gudang_id, jenis, jumlah_masuk, keterangan, tanggal_transaksi, user_id)
                            VALUES (?, 'masuk', ?, ?, NOW(), ?)";
            
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $keterangan_transaksi = "Tambah stok dari inventory.php: " . $keterangan;
            
            $stmt_transaksi = $conn->prepare($sql_transaksi);
            $stmt_transaksi->bind_param("iisi", $inventory_gudang_id, $jumlah, $keterangan_transaksi, $user_id);
            
            if (!$stmt_transaksi->execute()) {
                throw new Exception("Insert transaksi failed: " . $stmt_transaksi->error);
            }
            $stmt_transaksi->close();

            // Commit transaction
            $conn->commit();
            
            $message = 'Data inventory berhasil disimpan dan terintegrasi dengan sistem gudang!';
            $messageType = 'success';
            
            // Redirect untuk mencegah duplicate submission
            header("Location: inventory.php?success=1");
            exit();
            
        } catch (Exception $e) {
            // Rollback transaction jika ada error
            $conn->rollback();
            $message = 'Error: ' . $e->getMessage();
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

// Ambil data kategori barang
$kategori_barang = $conn->query("SELECT * FROM kategori_barang ORDER BY nama_kategori");

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
                <table id="inventoryTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
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
                            <td><strong><?= $row['kode_barang'] ?? '-' ?></strong></td>
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

        <!-- Baris 1: Kode Barang dan Kategori -->
        <div class="col-md-6">
            <label class="form-label">Kode Barang</label>
            <input type="text" name="kode_barang" class="form-control" placeholder="Masukkan kode barang">
            <!-- <small class="form-text text-muted">Biarkan kosong untuk generate otomatis</small> -->
        </div>
        <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <select name="nama_barang" class="form-control" required>
                <option value="">Pilih Kategori</option>
                <?php while($row_kategori = $kategori_barang->fetch_assoc()): ?>
                <option value="<?= $row_kategori['nama_kategori'] ?>"><?= $row_kategori['nama_kategori'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Baris 2: Warehouse dan Unit -->
        <div class="col-md-6">
            <label class="form-label">Warehouse</label>
            <select name="warehouse" id="warehouse" class="form-control" required onchange="loadGudang()">
                <option value="">Pilih Warehouse</option>
                <?php $no=1; while($row_dg = $nama_gudang->fetch_assoc()): ?>
                <option value="<?= $row_dg['nama']; ?>"><?= $row_dg['nama']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Unit</label>
            <select name="unit" class="form-control" required>
                <option value="">Pilih Unit</option>
                <option value="pcs">pcs</option>
                <option value="m²">m²</option>
                <option value="kg">kg</option>
                <option value="roll">roll</option>
                <option value="meter">meter</option>
            </select>
        </div>

        <!-- Baris 3: Jumlah dan Harga -->
        <div class="col-md-6">
            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required min="0" placeholder="0">
        </div>
        <div class="col-md-6">
            <label class="form-label">Harga per Unit</label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" name="harga_per_unit" class="form-control" required min="0" placeholder="0">
            </div>
        </div>

        <!-- Baris 4: Keterangan -->
        <div class="col-md-12">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan jika diperlukan"></textarea>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="save" class="btn btn-success">Simpan</button>
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
            $('#inventoryTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                order: [[0, 'asc']]
            });
        });
        
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