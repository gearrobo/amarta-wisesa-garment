<?php
include 'includes/header.php';
include 'config/db.php';

// Simpan data HPP
if (isset($_POST['save'])) {
    $id_persiapan = $_POST['id_persiapan'];
    $no_urut = $_POST['no_urut'];
    $bahan = $_POST['bahan'];
    $qty = $_POST['qty'];
    $barang_jadi = $_POST['barang_jadi'];
    $stok_order = $_POST['stok_order'];
    $efisiensi_consp = $_POST['efisiensi_consp'];
    $efisiensi_rap = $_POST['efisiensi_rap'];
    $stok_material = $_POST['stok_material'];
    $po = $_POST['po'];
    $harga_per_meter = $_POST['harga_per_meter'];
    $rap_x_harga_per_m = $_POST['rap_x_harga_per_m'];
    $total_harga_bahan = $_POST['total_harga_bahan'];
    $biaya_tenaga_kerja_per_qty = $_POST['biaya_tenaga_kerja_per_qty'];
    $total_biaya_tenaga_kerja = $_POST['total_biaya_tenaga_kerja'];
    $listrik = $_POST['listrik'];
    $air = $_POST['air'];
    $overhead = $_POST['overhead'];
    $total_biaya = $_POST['total_biaya'];
    $hpp = $_POST['hpp'];
    $profit = $_POST['profit'];
    $harga_jual = $_POST['harga_jual'];

    $sql = "INSERT INTO hpp (
        id_persiapan, no_urut, bahan, qty, barang_jadi, stok_order, 
        efisiensi_consp, efisiensi_rap, stok_material, po, harga_per_meter, 
        rap_x_harga_per_m, total_harga_bahan, biaya_tenaga_kerja_per_qty, 
        total_biaya_tenaga_kerja, listrik, air, overhead, total_biaya, 
        hpp, profit, harga_jual
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iisiiiiiddddddddddd",
        $id_persiapan, $no_urut, $bahan, $qty, $barang_jadi, $stok_order, 
        $efisiensi_consp, $efisiensi_rap, $stok_material, $po, $harga_per_meter, 
        $rap_x_harga_per_m, $total_harga_bahan, $biaya_tenaga_kerja_per_qty, 
        $total_biaya_tenaga_kerja, $listrik, $air, $overhead, $total_biaya, 
        $hpp, $profit, $harga_jual
    );

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Data HPP berhasil disimpan.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}

// Tampilkan data HPP + relasi persiapan
$result = $conn->query("
    SELECT hpp.*, persiapan.spp_no, persiapan.nama_barang
    FROM hpp 
    JOIN persiapan ON hpp.id_persiapan = persiapan.id
    ORDER BY hpp.id DESC
");
?>

<div class="main-content">
    <h3>Manajemen HPP</h3>

    <!-- Form Tambah HPP -->
    <form method="POST">
        <div class="row">
            <div class="col-md-3">
                <label>ID Persiapan</label>
                <input type="number" name="id_persiapan" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>No Urut</label>
                <input type="number" name="no_urut" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Bahan</label>
                <input type="text" name="bahan" class="form-control">
            </div>
            <div class="col-md-2">
                <label>Qty</label>
                <input type="number" name="qty" class="form-control">
            </div>
            <div class="col-md-2">
                <label>Barang Jadi</label>
                <input type="number" name="barang_jadi" class="form-control">
            </div>
        </div>

        <!-- Tambahkan input lainnya sesuai kebutuhan -->

        <button type="submit" name="save" class="btn btn-primary mt-3">Simpan</button>
    </form>

    <hr>

    <!-- Tabel Data HPP -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>No Urut</th>
                <th>Bahan</th>
                <th>Qty</th>
                <th>Barang Jadi</th>
                <th>Total Biaya</th>
                <th>HPP</th>
                <th>Profit</th>
                <th>Harga Jual</th>
                <th>Persiapan</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['no_urut'] ?></td>
                    <td><?= $row['bahan'] ?></td>
                    <td><?= $row['qty'] ?></td>
                    <td><?= $row['barang_jadi'] ?></td>
                    <td><?= number_format($row['total_biaya'], 2) ?></td>
                    <td><?= number_format($row['hpp'], 2) ?></td>
                    <td><?= number_format($row['profit'], 2) ?>%</td>
                    <td><?= number_format($row['harga_jual'], 2) ?></td>
                    <td><?= $row['spp_no'] ?> - <?= $row['nama_barang'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
