<?php
include 'config/db.php';

// Tambah Data
if (isset($_POST['save'])) {
    $id_persiapan = $_POST['id_persiapan'];
    $id_inventory = $_POST['id_inventory'] ?? null;
    $no_urut = $_POST['no_urut'];
    $bahan = $_POST['bahan'];
    $qty = $_POST['qty'];
    $barang_jadi = $_POST['barang_jadi'];
    $stok_order = $_POST['stok_order'] ?? 0;
    $efisiensi_consp = $_POST['efisiensi_consp'] ?? 0;
    $efisiensi_rap = $_POST['efisiensi_rap'] ?? 0;
    $stok_material = $_POST['stok_material'] ?? 0;
    $po = $_POST['po'] ?? 0;
    $harga_per_meter = $_POST['harga_per_meter'] ?? 0;
    $biaya_tenaga_kerja_per_qty = $_POST['biaya_tenaga_kerja_per_qty'] ?? 0;
    $listrik = $_POST['listrik'] ?? 0;
    $air = $_POST['air'] ?? 0;
    $overhead = $_POST['overhead'] ?? 0;
    $profit = $_POST['profit'] ?? 0;

    // Hitung nilai-nilai yang perlu dihitung
    $rap_x_harga_per_m = $efisiensi_rap * $harga_per_meter;
    $total_harga_bahan = $qty * $rap_x_harga_per_m;
    $total_biaya_tenaga_kerja = $qty * $biaya_tenaga_kerja_per_qty;
    $total_biaya = $total_harga_bahan + $total_biaya_tenaga_kerja + $listrik + $air + $overhead;
    $hpp = $qty > 0 ? $total_biaya / $qty : 0;
    $harga_jual = $hpp * (1 + ($profit / 100));

    $sql = "INSERT INTO hpp (id_persiapan, id_inventory, no_urut, bahan, qty, barang_jadi, stok_order, efisiensi_consp, efisiensi_rap, stok_material, po, harga_per_meter, rap_x_harga_per_m, total_harga_bahan, biaya_tenaga_kerja_per_qty, total_biaya_tenaga_kerja, listrik, air, overhead, total_biaya, hpp, profit, harga_jual) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Perbaikan: Sesuaikan tipe data dan jumlah parameter
        $stmt->bind_param("iissiiidddiiddddddddddd", 
            $id_persiapan, 
            $id_inventory, 
            $no_urut, 
            $bahan, 
            $qty, 
            $barang_jadi, 
            $stok_order, 
            $efisiensi_consp, 
            $efisiensi_rap, 
            $stok_material, 
            $po, 
            $harga_per_meter, 
            $rap_x_harga_per_m, 
            $total_harga_bahan, 
            $biaya_tenaga_kerja_per_qty, 
            $total_biaya_tenaga_kerja, 
            $listrik, 
            $air, 
            $overhead, 
            $total_biaya, 
            $hpp, 
            $profit, 
            $harga_jual
        );
        
        if ($stmt->execute()) {
            $success_msg = "Data HPP berhasil ditambahkan";
        } else {
            $error_msg = "Gagal menambahkan data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Error dalam persiapan query: " . $conn->error;
    }
}

// Update Data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $id_persiapan = $_POST['id_persiapan'];
    $id_inventory = $_POST['id_inventory'] ?? null;
    $no_urut = $_POST['no_urut'];
    $bahan = $_POST['bahan'];
    $qty = $_POST['qty'];
    $barang_jadi = $_POST['barang_jadi'];
    $stok_order = $_POST['stok_order'] ?? 0;
    $efisiensi_consp = $_POST['efisiensi_consp'] ?? 0;
    $efisiensi_rap = $_POST['efisiensi_rap'] ?? 0;
    $stok_material = $_POST['stok_material'] ?? 0;
    $po = $_POST['po'] ?? 0;
    $harga_per_meter = $_POST['harga_per_meter'] ?? 0;
    $biaya_tenaga_kerja_per_qty = $_POST['biaya_tenaga_kerja_per_qty'] ?? 0;
    $listrik = $_POST['listrik'] ?? 0;
    $air = $_POST['air'] ?? 0;
    $overhead = $_POST['overhead'] ?? 0;
    $profit = $_POST['profit'] ?? 0;

    // Hitung nilai-nilai yang perlu dihitung
    $rap_x_harga_per_m = $efisiensi_rap * $harga_per_meter;
    $total_harga_bahan = $qty * $rap_x_harga_per_m;
    $total_biaya_tenaga_kerja = $qty * $biaya_tenaga_kerja_per_qty;
    $total_biaya = $total_harga_bahan + $total_biaya_tenaga_kerja + $listrik + $air + $overhead;
    $hpp = $qty > 0 ? $total_biaya / $qty : 0;
    $harga_jual = $hpp * (1 + ($profit / 100));

    $sql = "UPDATE hpp SET id_persiapan=?, id_inventory=?, no_urut=?, bahan=?, qty=?, barang_jadi=?, stok_order=?, efisiensi_consp=?, efisiensi_rap=?, stok_material=?, po=?, harga_per_meter=?, rap_x_harga_per_m=?, total_harga_bahan=?, biaya_tenaga_kerja_per_qty=?, total_biaya_tenaga_kerja=?, listrik=?, air=?, overhead=?, total_biaya=?, hpp=?, profit=?, harga_jual=? WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Perbaikan: Sesuaikan tipe data dan jumlah parameter
        $stmt->bind_param("iissiiidddiidddddddddddi",
            $id_persiapan, 
            $id_inventory, 
            $no_urut, 
            $bahan, 
            $qty, 
            $barang_jadi, 
            $stok_order, 
            $efisiensi_consp, 
            $efisiensi_rap, 
            $stok_material, 
            $po, 
            $harga_per_meter, 
            $rap_x_harga_per_m, 
            $total_harga_bahan, 
            $biaya_tenaga_kerja_per_qty, 
            $total_biaya_tenaga_kerja, 
            $listrik, 
            $air, 
            $overhead, 
            $total_biaya, 
            $hpp, 
            $profit, 
            $harga_jual,
            $id
        );
        
        if ($stmt->execute()) {
            $success_msg = "Data HPP berhasil diperbarui";
        } else {
            $error_msg = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Error dalam persiapan query: " . $conn->error;
    }
}

// Hapus Data
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM hpp WHERE id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success_msg = "Data HPP berhasil dihapus";
        } else {
            $error_msg = "Gagal menghapus data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Error dalam persiapan query: " . $conn->error;
    }
}

// Ambil Data HPP dengan JOIN ke tabel persiapan, inventory_gudang, gudang, dan sps
$query = "
    SELECT hpp.*, p.spp_no, p.nama_barang as nama_produk, 
           ig.nama_barang as nama_bahan, g.nama as nama_gudang,
           s.sps_no, s.customer, s.item, s.artikel
    FROM hpp
    LEFT JOIN persiapan p ON hpp.id_persiapan = p.id
    LEFT JOIN sps s ON p.id_sps = s.id
    LEFT JOIN inventory_gudang ig ON hpp.id_inventory = ig.id
    LEFT JOIN gudang g ON ig.id_gudang = g.id
    ORDER BY hpp.id DESC
";

$result = $conn->query($query);
if (!$result) {
    die("Error dalam query: " . $conn->error);
}

// Ambil data persiapan untuk dropdown dengan informasi SPS
$persiapan_query = "
    SELECT p.id, p.spp_no, p.nama_barang, s.sps_no, s.customer
    FROM persiapan p
    LEFT JOIN sps s ON p.id_sps = s.id
    WHERE p.status = 'proses' OR p.status = 'selesai'
    ORDER BY s.sps_no
";
$persiapan_result = $conn->query($persiapan_query);

// Ambil data inventory untuk dropdown
$inventory_query = "
    SELECT ig.id, ig.nama_barang, g.nama as nama_gudang, ig.stok_akhir, i.harga_per_unit
    FROM inventory_gudang ig
    LEFT JOIN gudang g ON ig.id_gudang = g.id
    LEFT JOIN inventory i ON ig.id_inventory = i.id
    WHERE ig.stok_akhir > 0
    ORDER BY g.nama, ig.nama_barang
";

$inventory_result = $conn->query($inventory_query);
?>

<!-- Kode HTML tetap sama seperti sebelumnya -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD HPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th, .table td {
            vertical-align: middle;
        }
        .form-label {
            font-weight: 500;
        }
        .inventory-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="p-4">

<div class="container">
    <h3 class="mb-3">Data HPP (Harga Pokok Produksi)</h3>

    <!-- Notifikasi -->
    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success_msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error_msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Data HPP</button>

    <!-- Tabel -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
              <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>No SPS</th>
                    <th>Customer</th>
                    <th>Item</th>
                    <th>No SPP</th>
                    <th>Produk</th>
                    <th>Gudang</th>
                    <th>Bahan</th>
                    <th>QTY</th>
                    <th>Barang Jadi</th>
                    <th>Stok Material</th>
                    <th>HPP/Unit</th>
                    <th>Harga Jual</th>
                    <th>Profit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $no = 1;
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()): 
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['sps_no'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['customer'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['item'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['spp_no'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama_produk'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama_gudang'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['bahan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['qty']) ?></td>
                    <td><?= htmlspecialchars($row['barang_jadi']) ?></td>
                    <td><?= htmlspecialchars($row['stok_material'] ?? 0) ?></td>
                    <td>Rp <?= number_format($row['hpp'] ?? 0, 2, ',', '.') ?></td>
                    <td>Rp <?= number_format($row['harga_jual'] ?? 0, 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['profit'] ?? 0) ?>%</td>
                    <td>
                        <button class="btn btn-sm btn-warning" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin hapus data HPP ini?')">Hapus</a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="post">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit HPP</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    
                                    <div class="row">
                                         <div class="col-md-6 mb-2">
                                            <label class="form-label">Persiapan (SPP)</label>
                                            <select name="id_persiapan" class="form-control" required>
                                                <option value="">Pilih Persiapan</option>
                                                <?php
                                                $persiapan_result2 = $conn->query($persiapan_query);
                                                while($opt = $persiapan_result2->fetch_assoc()):
                                                    $selected = ($opt['id'] == $row['id_persiapan']) ? 'selected' : '';
                                                ?>
                                                    <option value="<?= $opt['id'] ?>" <?= $selected ?>>
                                                        <?= $opt['sps_no'] ?> - <?= $opt['spp_no'] ?> - <?= $opt['nama_barang'] ?> (<?= $opt['customer'] ?>)
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Inventory</label>
                                            <select name="id_inventory" class="form-control" id="editInventory<?= $row['id'] ?>" onchange="updateInventoryInfo('edit', <?= $row['id'] ?>)">
                                                <option value="">Pilih Inventory</option>
                                                <?php
                                                $inventory_result2 = $conn->query($inventory_query);
                                                while($opt = $inventory_result2->fetch_assoc()):
                                                    $selected = ($opt['id'] == $row['id_inventory']) ? 'selected' : '';
                                                ?>
                                                    <option value="<?= $opt['id'] ?>" 
                                                            data-stok="<?= $opt['stok_akhir'] ?>" 
                                                            data-harga="<?= $opt['harga_per_unit'] ?>" 
                                                            data-gudang="<?= $opt['nama_gudang'] ?>"
                                                            <?= $selected ?>>
                                                        <?= $opt['nama_gudang'] ?> - <?= $opt['nama_barang'] ?> (Stok: <?= $opt['stok_akhir'] ?>)
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="editInventoryInfo<?= $row['id'] ?>" class="inventory-info" style="display: none;">
                                        <strong>Informasi Inventory:</strong>
                                        <div>Gudang: <span id="editGudangInfo<?= $row['id'] ?>"></span></div>
                                        <div>Stok Tersedia: <span id="editStokInfo<?= $row['id'] ?>"></span></div>
                                        <div>Harga per Unit: Rp <span id="editHargaInfo<?= $row['id'] ?>"></span></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">No Urut</label>
                                            <input type="text" name="no_urut" class="form-control" value="<?= htmlspecialchars($row['no_urut'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-8 mb-2">
                                            <label class="form-label">Bahan</label>
                                            <input type="text" name="bahan" class="form-control" value="<?= htmlspecialchars($row['bahan'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">QTY</label>
                                            <input type="number" name="qty" class="form-control" value="<?= htmlspecialchars($row['qty']) ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Barang Jadi</label>
                                            <input type="number" name="barang_jadi" class="form-control" value="<?= htmlspecialchars($row['barang_jadi']) ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Stok Order</label>
                                            <input type="number" name="stok_order" class="form-control" value="<?= htmlspecialchars($row['stok_order'] ?? 0) ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Efisiensi Consp</label>
                                            <input type="number" step="0.01" name="efisiensi_consp" class="form-control" value="<?= htmlspecialchars($row['efisiensi_consp'] ?? 0) ?>">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Efisiensi RAP</label>
                                            <input type="number" step="0.01" name="efisiensi_rap" class="form-control" value="<?= htmlspecialchars($row['efisiensi_rap'] ?? 0) ?>">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Stok Material</label>
                                            <input type="number" name="stok_material" class="form-control" value="<?= htmlspecialchars($row['stok_material'] ?? 0) ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">PO</label>
                                            <input type="number" name="po" class="form-control" value="<?= htmlspecialchars($row['po'] ?? 0) ?>">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Harga per Meter</label>
                                            <input type="number" step="0.01" name="harga_per_meter" class="form-control" value="<?= htmlspecialchars($row['harga_per_meter'] ?? 0) ?>">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Biaya Tenaga Kerja per Qty</label>
                                            <input type="number" step="0.01" name="biaya_tenaga_kerja_per_qty" class="form-control" value="<?= htmlspecialchars($row['biaya_tenaga_kerja_per_qty'] ?? 0) ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Listrik</label>
                                            <input type="number" step="0.01" name="listrik" class="form-control" value="<?= htmlspecialchars($row['listrik'] ?? 0) ?>">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Air</label>
                                            <input type="number" step="0.01" name="air" class="form-control" value="<?= htmlspecialchars($row['air'] ?? 0) ?>">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Overhead</label>
                                            <input type="number" step="0.01" name="overhead" class="form-control" value="<?= htmlspecialchars($row['overhead'] ?? 0) ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Profit (%)</label>
                                                                                        <input type="number" step="0.01" name="profit" class="form-control" value="<?= htmlspecialchars($row['profit'] ?? 0) ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="12" class="text-center">Tidak ada data HPP</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data HPP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Persiapan (SPP)</label>
                            <select name="id_persiapan" class="form-control" required>
                                <option value="">Pilih Persiapan</option>
                                <?php
                                while($opt = $persiapan_result->fetch_assoc()):
                                ?>
                                    <option value="<?= $opt['id'] ?>">
                                        <?= $opt['sps_no'] ?> - <?= $opt['spp_no'] ?> - <?= $opt['nama_barang'] ?> (<?= $opt['customer'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Inventory</label>
                            <select name="id_inventory" class="form-control" id="addInventory" onchange="updateInventoryInfo('add')">
                                <option value="">Pilih Inventory</option>
                                <?php
                                $inventory_result->data_seek(0); // Reset pointer
                                while($opt = $inventory_result->fetch_assoc()):
                                ?>
                                    <option value="<?= $opt['id'] ?>" 
                                            data-stok="<?= $opt['stok_akhir'] ?>" 
                                            data-harga="<?= $opt['harga_per_unit'] ?>" 
                                            data-gudang="<?= $opt['nama_gudang'] ?>">
                                        <?= $opt['nama_gudang'] ?> - <?= $opt['nama_barang'] ?> (Stok: <?= $opt['stok_akhir'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div id="addInventoryInfo" class="inventory-info" style="display: none;">
                        <strong>Informasi Inventory:</strong>
                        <div>Gudang: <span id="addGudangInfo"></span></div>
                        <div>Stok Tersedia: <span id="addStokInfo"></span></div>
                        <div>Harga per Unit: Rp <span id="addHargaInfo"></span></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">No Urut</label>
                            <input type="text" name="no_urut" class="form-control">
                        </div>
                        <div class="col-md-8 mb-2">
                            <label class="form-label">Bahan</label>
                            <input type="text" name="bahan" class="form-control" id="addBahan" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">QTY</label>
                            <input type="number" name="qty" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Barang Jadi</label>
                            <input type="number" name="barang_jadi" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Stok Order</label>
                            <input type="number" name="stok_order" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Efisiensi Consp</label>
                            <input type="number" step="0.01" name="efisiensi_consp" class="form-control" value="1.00">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Efisiensi RAP</label>
                            <input type="number" step="0.01" name="efisiensi_rap" class="form-control" value="1.00">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Stok Material</label>
                            <input type="number" name="stok_material" class="form-control" id="addStokMaterial" value="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">PO</label>
                            <input type="number" name="po" class="form-control" value="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Harga per Meter</label>
                            <input type="number" step="0.01" name="harga_per_meter" class="form-control" id="addHargaPerMeter" value="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Biaya Tenaga Kerja per Qty</label>
                            <input type="number" step="0.01" name="biaya_tenaga_kerja_per_qty" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Listrik</label>
                            <input type="number" step="0.01" name="listrik" class="form-control" value="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Air</label>
                            <input type="number" step="0.01" name="air" class="form-control" value="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Overhead</label>
                            <input type="number" step="0.01" name="overhead" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Profit (%)</label>
                            <input type="number" step="0.01" name="profit" class="form-control" value="30">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="save" class="btn btn-primary">Simpan Data</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateInventoryInfo(type, id = null) {
    const selectElement = type === 'add' ? document.getElementById('addInventory') : document.getElementById('editInventory' + id);
    const infoElement = type === 'add' ? document.getElementById('addInventoryInfo') : document.getElementById('editInventoryInfo' + id);
    const gudangElement = type === 'add' ? document.getElementById('addGudangInfo') : document.getElementById('editGudangInfo' + id);
    const stokElement = type === 'add' ? document.getElementById('addStokInfo') : document.getElementById('editStokInfo' + id);
    const hargaElement = type === 'add' ? document.getElementById('addHargaInfo') : document.getElementById('editHargaInfo' + id);
    
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectedOption.value !== '') {
        const stok = selectedOption.getAttribute('data-stok');
        const harga = selectedOption.getAttribute('data-harga');
        const gudang = selectedOption.getAttribute('data-gudang');
        const bahan = selectedOption.text.split(' - ')[1].split(' (Stok:')[0];
        
        gudangElement.textContent = gudang;
        stokElement.textContent = stok;
        hargaElement.textContent = parseFloat(harga).toLocaleString('id-ID', {minimumFractionDigits: 2});
        
        infoElement.style.display = 'block';
        
        // Auto-fill fields
        if (type === 'add') {
            document.getElementById('addBahan').value = bahan;
            document.getElementById('addStokMaterial').value = stok;
            document.getElementById('addHargaPerMeter').value = harga;
        }
    } else {
        infoElement.style.display = 'none';
    }
}

// Initialize inventory info for edit modals when they open
document.addEventListener('DOMContentLoaded', function() {
    const editModals = document.querySelectorAll('[id^="editModal"]');
    editModals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            const id = this.id.replace('editModal', '');
            updateInventoryInfo('edit', id);
        });
    });
});
</script>
</body>
</html>