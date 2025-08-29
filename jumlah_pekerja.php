<?php
include 'config/db.php';
include 'includes/header.php';

// ------------------- Simpan Data -------------------
if (isset($_POST['save'])) {
    $id_sps         = intval($_POST['id_sps']);
    $bahan_baku     = $_POST['bahan_baku'];
    $jumlah_order   = intval($_POST['jumlah_order']);
    $jenis_pekerjaan= $_POST['jenis_pekerjaan'];
    $target_waktu   = intval($_POST['target_waktu']);
    $harga_perunit  = intval($_POST['harga_perunit']);

    // hitungan otomatis
    $man_hours = 7 * 60 * 60;
    $jumlah_waktu_kerja = $jumlah_order * $target_waktu;
    $jumlah_pekerja = ceil($jumlah_waktu_kerja / $man_hours);

    $sql = "INSERT INTO jumlah_pekerja 
            (id_sps, bahan_baku, jumlah_order, jenis_pekerjaan, target_waktu, 
             jumlah_waktu_kerja, harga_perunit, man_hours, jumlah_pekerja, created_at, updated_at) 
            VALUES 
            ($id_sps, '$bahan_baku', $jumlah_order, '$jenis_pekerjaan', $target_waktu, 
             $jumlah_waktu_kerja, $harga_perunit, $man_hours, $jumlah_pekerja, NOW(), NOW())";

    if (!$conn->query($sql)) {
        echo "Error Insert: " . $conn->error;
    }
}

// ------------------- Update Data -------------------
if (isset($_POST['update'])) {
    $id             = intval($_POST['id']);
    $id_sps         = intval($_POST['id_sps']);
    $bahan_baku     = $_POST['bahan_baku'];
    $jumlah_order   = intval($_POST['jumlah_order']);
    $jenis_pekerjaan= $_POST['jenis_pekerjaan'];
    $target_waktu   = intval($_POST['target_waktu']);
    $harga_perunit  = intval($_POST['harga_perunit']);

    $man_hours = 7 * 60 * 60;
    $jumlah_waktu_kerja = $jumlah_order * $target_waktu;
    $jumlah_pekerja = ceil($jumlah_waktu_kerja / $man_hours);

    $sql = "UPDATE jumlah_pekerja SET 
                id_sps=$id_sps,
                bahan_baku='$bahan_baku',
                jumlah_order=$jumlah_order,
                jenis_pekerjaan='$jenis_pekerjaan',
                target_waktu=$target_waktu,
                jumlah_waktu_kerja=$jumlah_waktu_kerja,
                harga_perunit=$harga_perunit,
                man_hours=$man_hours,
                jumlah_pekerja=$jumlah_pekerja,
                updated_at=NOW()
            WHERE id=$id";

    if (!$conn->query($sql)) {
        echo "Error Update: " . $conn->error;
    }
}

// ------------------- Hapus Data -------------------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM jumlah_pekerja WHERE id=$id");
}

// ------------------- Ambil Data -------------------
$id_sps = intval($_GET['id_sps'] ?? 0);

$sql = "SELECT jp.*, s.sps_no, s.customer 
        FROM jumlah_pekerja jp 
        LEFT JOIN sps s ON jp.id_sps = s.id";
if ($id_sps > 0) {
    $sql .= " WHERE jp.id_sps=$id_sps";
}
$result = $conn->query($sql);


// cari id persiapan berdasarkan id_sps
$persiapan_id = null;
if ($id_sps > 0) {
    $sql = "SELECT id FROM persiapan WHERE id_sps = $id_sps LIMIT 1";
    $result_pr = $conn->query($sql);
    if ($row = $result_pr->fetch_assoc()) {
        $persiapan_id = $row['id'];
    }
}

?>
<div class="main-content">
     <div>
        <h1 class="h3 mb-4">Detail Persiapan</h1>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="persiapan.php">Persiapan</a></li>
                <?php if ($persiapan_id): ?>
                    <li class="breadcrumb-item"><a href="detail-persiapan.php?id=<?= $persiapan_id ?>">Data Persiapan</a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active">Hitung Jumlah Pekerja</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Data</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>SPS</th>
                <th>Bahan Baku</th>
                <th>Jumlah Order</th>
                <th>Jenis Pekerjaan</th>
                <th>Target Waktu (s)</th>
                <th>Jumlah Waktu Kerja</th>
                <th>Harga/Unit</th>
                <th>Man Hours</th>
                <th>Jumlah Pekerja</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php $no=1; while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['sps_no'] ?> - <?= $row['customer'] ?></td>
                <td><?= $row['bahan_baku'] ?></td>
                <td><?= $row['jumlah_order'] ?></td>
                <td><?= $row['jenis_pekerjaan'] ?></td>
                <td><?= $row['target_waktu'] ?></td>
                <td><?= $row['jumlah_waktu_kerja'] ?></td>
                <td><?= $row['harga_perunit'] ?></td>
                <td><?= $row['man_hours'] ?></td>
                <td><?= $row['jumlah_pekerja'] ?></td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                      <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>"><i class="fas fa-edit"></i></button>
                      <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="editModal<?= $row['id'] ?>">
                <div class="modal-dialog">
                    <form method="post" class="modal-content">
                        <div class="modal-header"><h5>Edit Data</h5></div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">

                            <div class="mb-2">
                                <label>SPS</label>
                                <select name="id_sps" class="form-control">
                                    <?php
                                    $sps = $conn->query("SELECT * FROM sps");
                                    while ($s = $sps->fetch_assoc()) {
                                        $sel = ($s['id'] == $row['id_sps']) ? 'selected' : '';
                                        echo "<option value='{$s['id']}' $sel>{$s['sps_no']} - {$s['customer']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-2"><label>Bahan Baku</label><input type="text" name="bahan_baku" class="form-control" value="<?= $row['bahan_baku'] ?>"></div>
                            <div class="mb-2"><label>Jumlah Order</label><input type="number" name="jumlah_order" class="form-control" value="<?= $row['jumlah_order'] ?>"></div>
                            <div class="mb-2"><label>Jenis Pekerjaan</label><input type="text" name="jenis_pekerjaan" class="form-control" value="<?= $row['jenis_pekerjaan'] ?>"></div>
                            <div class="mb-2"><label>Target Waktu (s)</label><input type="number" name="target_waktu" class="form-control" value="<?= $row['target_waktu'] ?>"></div>
                            <div class="mb-2"><label>Harga per Unit</label><input type="number" name="harga_perunit" class="form-control" value="<?= $row['harga_perunit'] ?>"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header"><h5>Tambah Data</h5></div>
            <div class="modal-body">
                <div class="mb-2">
                    <label>SPS</label>
                    <select name="id_sps" class="form-control">
                        <?php
                        $sps = $conn->query("SELECT * FROM sps WHERE id = $id_sps");
                        while ($s = $sps->fetch_assoc()) {
                            echo "<option value='{$s['id']}'>{$s['sps_no']} - {$s['customer']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-2"><label>Bahan Baku</label><input type="text" name="bahan_baku" class="form-control"></div>
                <div class="mb-2"><label>Jumlah Order</label><input type="number" name="jumlah_order" class="form-control"></div>
                <div class="mb-2"><label>Jenis Pekerjaan</label><input type="text" name="jenis_pekerjaan" class="form-control"></div>
                <div class="mb-2"><label>Target Waktu (s)</label><input type="number" name="target_waktu" class="form-control"></div>
                <div class="mb-2"><label>Harga per Unit</label><input type="number" name="harga_perunit" class="form-control"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="save" class="btn btn-success">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>
