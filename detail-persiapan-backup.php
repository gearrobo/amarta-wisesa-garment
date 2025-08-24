<?php

include 'config/db.php';   // hanya DB dulu
// ---------------- AJAX Get Barang by Gudang ----------------
if (isset($_GET['action']) && $_GET['action'] === 'get_barang') {
    $id_gudang = intval($_GET['id_gudang'] ?? 0);
    $data = [];

    if ($id_gudang > 0) {
        $sql = "SELECT id_inventory, nama_barang, stok_akhir, satuan 
                FROM inventory_gudang
                WHERE id_gudang = ? 
                ORDER BY nama_barang ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_gudang);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit; // <--- Penting, stop biar tidak render HTML
}
?>

<?php
// setelah handler AJAX selesai, baru load header & tampilan
include 'includes/header.php';

// ---------------------------
// Tambah Data HPP
// ---------------------------



if (isset($_POST['save'])) {
    $no_urut = 'HPP' . rand(100000, 999999);

    $id_persiapan   = intval($_POST['id_persiapan']);
    $gudang         = intval($_POST['gudang']);
    $kategori_barang= intval($_POST['kategori_barang']);
    $jumlah         = floatval($_POST['jumlah']);
    $satuan         = $_POST['satuan'];
    $barang_jadi    = $_POST['barang_jadi'];
    $consp          = $_POST['consp'];
    $stok_material  = $_POST['stok_material'];
    $purchase_order = $_POST['purchase_order'];
    $sppo           = $_POST['sppo'];
    $harga          = floatval($_POST['harga']);

    $sql = "INSERT INTO hpp 
        (id_persiapan, id_inventory, no_urut, qty, satuan, barang_jadi, efisiensi_consp, stok_material, po, total_harga_bahan) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
          $stmt, "iisisssidi",
          $id_persiapan, 
          $kategori_barang,   // ini id_inventory
          $no_urut, 
          $jumlah, 
          $satuan, 
          $barang_jadi, 
          $consp, 
          $stok_material, 
          $purchase_order, 
          $harga   // bisa dipakai untuk total_harga_bahan dulu
      );
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            echo "<script>window.location.href='?id=$id_persiapan&success=1';</script>";
            exit();
        } else {
            mysqli_stmt_close($stmt);
            echo "<script>alert('Gagal menyimpan data HPP: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// ---------------------------
// Edit Data HPP
// ---------------------------
if (isset($_POST['update'])) {
    $id_hpp         = $_POST['id_hpp'];
    $id_persiapan   = $_POST['id_persiapan'];
    $gudang         = $_POST['gudang'];
    $kategori_barang= $_POST['kategori_barang'];
    $jumlah         = $_POST['jumlah'];
    $satuan         = $_POST['satuan'];
    $barang_jadi    = $_POST['barang_jadi'];
    $consp          = $_POST['consp'];
    $stok_material  = $_POST['stok_material'];
    $purchase_order = $_POST['purchase_order'];
    $sppo           = $_POST['sppo'];
    $harga          = $_POST['harga'];

    $sql = "UPDATE hpp 
        SET id_inventory=?, qty=?, satuan=?, barang_jadi=?, efisiensi_consp=?, stok_material=?, po=?, total_harga_bahan=? 
        WHERE id=? AND id_persiapan=?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
        $stmt, "iisssiddi",
        $kategori_barang, // id_inventory
        $jumlah,
        $satuan,
        $barang_jadi,
        $consp,
        $stok_material,
        $purchase_order,
        $harga,
        $id_hpp,
        $id_persiapan
      );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// ---------------------------
// Hapus Data HPP
// ---------------------------
if (isset($_GET['delete'])) {
    $id_hpp = $_GET['delete'];
    $sql = "DELETE FROM hpp WHERE id=?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_hpp);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// ---------------------------
// Ambil Data HPP
// ---------------------------
$hpp_items = [];
$total_hpp = 0;
$id_persiapan = intval($_GET['id'] ?? 0);


$sql = "SELECT hpp.*, g.nama AS nama_gudang, inv.nama_barang 
        FROM hpp 
        LEFT JOIN inventory_gudang ig ON hpp.id_inventory = ig.id_inventory 
        LEFT JOIN gudang g ON ig.id_gudang = g.id 
        LEFT JOIN inventory inv ON ig.id_inventory = inv.id 
        WHERE hpp.id_persiapan=? 
        ORDER BY hpp.no_urut ASC";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id_persiapan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $hpp_items[] = $row;
        $total_hpp += (float)$row['jumlah'] * (float)$row['harga'];
    }
    mysqli_stmt_close($stmt);
}

// Ambil nama SPS dari persiapan
$id_persiapan = intval($_GET['id'] ?? 0);

$sql_sps = "SELECT s.sps_no 
            FROM persiapan p
            LEFT JOIN sps s ON p.id_sps = s.id
            WHERE p.id = ?";

if ($stmt = $conn->prepare($sql_sps)) {
    $stmt->bind_param("i", $id_persiapan);
    $stmt->execute();
    $stmt->bind_result($nama_sps);
    $stmt->fetch();
    $stmt->close();
}
?>

<div class="main-content">
    <h3 class="mb-3">
        Daftar HPP (Harga Pokok Produksi) 
        <?php if ($nama_sps): ?>
            - <small class="text-muted">SPS: <?= htmlspecialchars($nama_sps); ?></small>
        <?php endif; ?>
    </h3>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Data HPP</span>
            <button type="button" class="btn btn-primary btn-sm text-white" data-bs-toggle="modal" data-bs-target="#addHppModal">
                <i class="fas fa-plus"></i> Tambah HPP
            </button>
        </div>
        <div class="card-body">
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
                        <th>Harga</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($hpp_items) > 0): ?>
                    <?php foreach ($hpp_items as $index => $item): 
                        $jumlah   = (float)($item['jumlah'] ?? 0);
                        $harga    = (float)($item['harga'] ?? 0);
                        $subtotal = $jumlah * $harga;
                    ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($item['nama_gudang'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($item['nama_kategori'] ?? '-'); ?></td>
                            <td><?= number_format($jumlah); ?></td>
                            <td><?= htmlspecialchars($item['satuan'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($item['barang_jadi'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($item['consp'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($item['stok_material'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($item['purchase_order'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($item['sppo'] ?? '-'); ?></td>
                            <td>Rp <?= number_format($harga, 0, ',', '.'); ?></td>
                            <td>Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                            <td>
                                <a href="?edit=<?= $item['id_hpp']; ?>&id_persiapan=<?= $id_persiapan; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="?delete=<?= $item['id_hpp']; ?>&id_persiapan=<?= $id_persiapan; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13" class="text-center">Belum ada data HPP</td>
                    </tr>
                <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th colspan="11" class="text-end">Total HPP:</th>
                        <th colspan="2">Rp <?= number_format($total_hpp, 0, ',', '.'); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- ===================== -->
<!-- Modal Tambah HPP -->
<!-- ===================== -->
<div class="modal fade" id="addHppModal" tabindex="-1" aria-labelledby="addHppModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addHppModalLabel">Tambah HPP</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_persiapan" value="<?= $id_persiapan; ?>">
          
          <div class="row g-3">
            <!-- Informasi Utama -->
            <div class="col-12">
              <h6 class="border-bottom pb-2 mb-3 text-primary">Informasi Utama</h6>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Nama SPS</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($nama_sps); ?>" readonly>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Gudang <span class="text-danger">*</span></label>
              <select class="form-select" id="gudang" name="gudang" required>
                <option value="">Pilih Gudang</option>
                <?php
                $sql_gudang = "SELECT * FROM gudang ORDER BY nama";
                $res_gudang = mysqli_query($conn, $sql_gudang);
                while ($g = mysqli_fetch_assoc($res_gudang)) {
                    echo '<option value="'.$g['id'].'">'.htmlspecialchars($g['nama']).'</option>';
                }
                ?>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Kategori Barang <span class="text-danger">*</span></label>
              <select class="form-select" id="kategori_barang" name="kategori_barang" required>
                <option value="">Pilih Barang</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Barang Jadi <span class="text-danger">*</span></label>
              <input type="text" name="barang_jadi" class="form-control" required>
            </div>

            <!-- Detail Jumlah dan Harga -->
            <div class="col-12 mt-4">
              <h6 class="border-bottom pb-2 mb-3 text-primary">Detail Jumlah dan Harga</h6>
            </div>
            
            <div class="col-md-4">
              <label class="form-label">Jumlah <span class="text-danger">*</span></label>
              <input type="number" name="jumlah" class="form-control" required min="0">
            </div>
            
            <div class="col-md-4">
              <label class="form-label">Satuan</label>
              <input type="text" class="form-control" id="satuan" name="satuan" readonly>
            </div>
            
            <div class="col-md-4">
              <label class="form-label">Stok Material</label>
              <input type="text" class="form-control" id="stok_material" name="stok_material" readonly>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Harga <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="harga" class="form-control" required min="0">
            </div>

            <!-- Informasi Tambahan -->
            <div class="col-12 mt-4">
              <h6 class="border-bottom pb-2 mb-3 text-primary">Informasi Tambahan</h6>
            </div>
            
            <div class="col-md-4">
              <label class="form-label">Consp</label>
              <input type="text" name="consp" class="form-control">
            </div>
            
            <div class="col-md-4">
              <label class="form-label">Purchase Order</label>
              <input type="text" name="purchase_order" class="form-control">
            </div>
            
            <div class="col-md-4">
              <label class="form-label">SPPO</label>
              <input type="text" name="sppo" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="save" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('gudang').addEventListener('change', function () {
    let gudangId = this.value;
    let barangSelect = document.getElementById('kategori_barang');

    barangSelect.innerHTML = '<option value="">Loading...</option>';

    if (gudangId) {
        fetch('?action=get_barang&id_gudang=' + gudangId)
            .then(res => res.json())
            .then(data => {
                barangSelect.innerHTML = '<option value="">Pilih Barang</option>';
                data.forEach(item => {
                   barangSelect.innerHTML += `<option value="${item.id_inventory}" data-stok="${item.stok_akhir}" data-satuan="${item.satuan}">${item.nama_barang}</option>`;

                });
            })
            .catch(err => {
                console.error(err);
                barangSelect.innerHTML = '<option value="">Gagal memuat</option>';
            });
    } else {
        barangSelect.innerHTML = '<option value="">Pilih Barang</option>';
    }
});

// Auto isi stok & satuan ketika pilih barang
document.getElementById('kategori_barang').addEventListener('change', function() {
    let selected = this.options[this.selectedIndex];
    let stok = selected.getAttribute('data-stok') || '';
    let satuan = selected.getAttribute('data-satuan') || '';

    document.getElementById('stok_material').value = stok;
    document.getElementById('satuan').value = satuan;
});
</script>


<?php include 'includes/footer.php'; ?>

