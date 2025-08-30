<?php
include 'config/db.php';

$id = intval($_GET['id'] ?? 0);

// Ambil data karyawan_harian_borongan berdasarkan ID
$sql = "SELECT * FROM karyawan_harian_borongan WHERE id=$id LIMIT 1";
$res = $conn->query($sql);
$data = $res->fetch_assoc();

$id_karyawan = $data['id_karyawan'];

// Ambil daftar karyawan untuk dropdown
$listKaryawan = $conn->query("SELECT id, nama_lengkap FROM karyawan WHERE id = $id_karyawan LIMIT 1");
?>

<form method="POST" action="upah-pekerja.php?id_sps=<?= $data['id_sps'] ?>">
    <div class="modal-header">
        <h5 class="modal-title">Edit Data Pekerja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">
        <div class="mb-2">
            <label>Karyawan</label>
            <select name="id_karyawan" class="form-control">
                <?php while ($k = $listKaryawan->fetch_assoc()): ?>
                    <option value="<?= $k['id'] ?>" <?= $k['id']==$data['id_karyawan']?'selected':'' ?>>
                        <?= htmlspecialchars($k['nama_lengkap']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-2">
            <label>Upah per Hari</label>
            <input type="number" name="upah_per_hari" class="form-control" value="<?= $data['upah_per_hari'] ?>" required>
        </div>
        <div class="mb-2">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="aktif" <?= $data['status']=='aktif'?'selected':'' ?>>Aktif</option>
                <option value="non-aktif" <?= $data['status']=='non-aktif'?'selected':'' ?>>Non Aktif</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" name="update" class="btn btn-success">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
    </div>
</form>
