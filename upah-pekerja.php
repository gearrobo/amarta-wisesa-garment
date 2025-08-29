<?php
include 'includes/header.php';
include 'config/db.php';

// Ambil id_sps dari URL
$id_sps_produksi = intval($_GET['id_sps'] ?? 0);

// ================= HANDLE CRUD =================

// Tambah
if (isset($_POST['tambah'])) {
    $id_karyawan   = intval($_POST['id_karyawan']);
    $upah_per_hari = intval($_POST['upah_per_hari']);
    $status        = $conn->real_escape_string($_POST['status']);

    $sqlInsert = "INSERT INTO karyawan_harian_borongan (id_sps, id_karyawan, upah_per_hari, status, created_at) 
                  VALUES ($id_sps_produksi, $id_karyawan, $upah_per_hari, '$status', NOW())";
    $conn->query($sqlInsert);
}

// Update
if (isset($_POST['update'])) {
    $id            = intval($_POST['id']);
    $id_karyawan   = intval($_POST['id_karyawan']);
    $upah_per_hari = intval($_POST['upah_per_hari']);
    $status        = $conn->real_escape_string($_POST['status']);

    $sqlUpdate = "UPDATE karyawan_harian_borongan 
                  SET id_karyawan=$id_karyawan, upah_per_hari=$upah_per_hari, status='$status'
                  WHERE id=$id";
    $conn->query($sqlUpdate);
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM karyawan_harian_borongan WHERE id=$id");
    header("Location: upah-pekerja.php?id_sps=$id_sps_produksi");
    exit;
}

// ================= DATA SPS =================
$sqlSps = "SELECT * FROM sps WHERE id = $id_sps_produksi";
$resSps = $conn->query($sqlSps);
$sps = $resSps->fetch_assoc();

// ================= DATA PEKERJA =================
$sql = "
    SELECT 
        khb.id,
        k.nama_lengkap AS nama_karyawan,
        khb.id_karyawan,
        khb.upah_per_hari,
        khb.status
    FROM karyawan_harian_borongan khb
    LEFT JOIN karyawan k ON khb.id_karyawan = k.id
    WHERE khb.id_sps = $id_sps_produksi
    ORDER BY khb.created_at DESC
";
$res = $conn->query($sql);

// Ambil daftar karyawan untuk dropdown
$listKaryawan = $conn->query("SELECT id, nama_lengkap FROM karyawan WHERE id = $id_sps_produksi ORDER BY nama_lengkap ASC");
?>

<div class="main-content">
    <h2>Data Upah Pekerja</h2>

    <!-- Info SPS -->
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>SPS No:</strong> <?= htmlspecialchars($sps['sps_no'] ?? '-') ?></p>
            <p><strong>Customer:</strong> <?= htmlspecialchars($sps['customer'] ?? '-') ?></p>
            <p><strong>Item:</strong> <?= htmlspecialchars($sps['item'] ?? '-') ?></p>
        </div>
    </div>

    <!-- Form Tambah -->
    <div class="card mb-3">
        <div class="card-body">
            <h5>Tambah Data Pekerja</h5>
            <form method="POST">
                <div class="row mb-2">
                    <div class="col">
                        <label>Karyawan</label>
                        <select name="id_karyawan" class="form-control" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php while ($k = $listKaryawan->fetch_assoc()): ?>
                                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_lengkap']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col">
                        <label>Upah per Hari</label>
                        <input type="number" name="upah_per_hari" class="form-control" required>
                    </div>
                    <div class="col">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif">Aktif</option>
                            <option value="non-aktif">Non Aktif</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
            </form>
        </div>
    </div>

    <!-- Tabel Pekerja -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Upah per Hari</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_karyawan']) ?></td>
                    <td><?= number_format($row['upah_per_hari'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <!-- Tombol Edit -->
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                        <!-- Tombol Hapus -->
                        <a href="upah-pekerja.php?id_sps=<?= $id_sps_produksi ?>&hapus=<?= $row['id'] ?>" 
                           onclick="return confirm('Yakin hapus data ini?')" 
                           class="btn btn-sm btn-danger">Hapus</a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Data Pekerja</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <div class="mb-2">
                                        <label>Karyawan</label>
                                        <select name="id_karyawan" class="form-control">
                                            <?php
                                            $listKaryawan2 = $conn->query("SELECT id, nama_lengkap FROM karyawan ORDER BY nama_lengkap ASC");
                                            while ($k2 = $listKaryawan2->fetch_assoc()):
                                            ?>
                                                <option value="<?= $k2['id'] ?>" <?= ($k2['id'] == $row['id_karyawan']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($k2['nama_lengkap']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label>Upah per Hari</label>
                                        <input type="number" name="upah_per_hari" class="form-control" value="<?= $row['upah_per_hari'] ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="aktif" <?= $row['status']=='aktif'?'selected':'' ?>>Aktif</option>
                                            <option value="non-aktif" <?= $row['status']=='non-aktif'?'selected':'' ?>>Non Aktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="update" class="btn btn-success">Simpan</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
