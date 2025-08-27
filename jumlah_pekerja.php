<?php
include 'config/db.php'; // koneksi MySQL

// ---------------- CREATE / UPDATE ----------------
if (isset($_POST['save'])) {
    $id             = $_POST['id'] ?? 0;
    $bahan_baku     = $_POST['bahan_baku'];
    $jumlah_order   = (int)$_POST['jumlah_order'];
    $jenis_pekerjaan= $_POST['jenis_pekerjaan'];
    $target_waktu   = (int)$_POST['target_waktu'];
    $harga_perunit  = (float)$_POST['harga_perunit'];
    $now            = date("Y-m-d H:i:s");

    // hitung otomatis
    $man_hours          = 7 * 60 * 60; // 7 jam = 25200 detik
    $jumlah_waktu_kerja = $jumlah_order * $target_waktu;
    $jumlah_pekerja     = ceil($jumlah_waktu_kerja / $man_hours);

    if ($id > 0) {
        // UPDATE
        $sql = "UPDATE jumlah_pekerja 
                SET bahan_baku=?, jumlah_order=?, jenis_pekerjaan=?, target_waktu=?, 
                    jumlah_waktu_kerja=?, harga_perunit=?, man_hours=?, jumlah_pekerja=?, updated_at=? 
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sisiidissi",
            $bahan_baku,
            $jumlah_order,
            $jenis_pekerjaan,
            $target_waktu,
            $jumlah_waktu_kerja,
            $harga_perunit,
            $man_hours,
            $jumlah_pekerja,
            $now,
            $id
        );
        $stmt->execute();
    } else {
        // INSERT
        $sql = "INSERT INTO jumlah_pekerja 
                (bahan_baku, jumlah_order, jenis_pekerjaan, target_waktu, 
                 jumlah_waktu_kerja, harga_perunit, man_hours, jumlah_pekerja, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sisiidisss",
            $bahan_baku,
            $jumlah_order,
            $jenis_pekerjaan,
            $target_waktu,
            $jumlah_waktu_kerja,
            $harga_perunit,
            $man_hours,
            $jumlah_pekerja,
            $now,
            $now
        );
        $stmt->execute();
    }

    header("Location: jumlah_pekerja.php");
    exit;
}

// ---------------- DELETE ----------------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM jumlah_pekerja WHERE id=$id");
    header("Location: jumlah_pekerja.php");
    exit;
}

// ---------------- READ ----------------
$result = $conn->query("SELECT * FROM jumlah_pekerja ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CRUD Jumlah Pekerja</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">
  <h2 class="mb-3">Data Jumlah Pekerja</h2>
  <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Data</button>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Bahan Baku</th>
        <th>Jumlah Order</th>
        <th>Jenis Pekerjaan</th>
        <th>Target Waktu</th>
        <th>Jumlah Waktu Kerja</th>
        <th>Jumlah Pekerja</th>
        <th>Harga PerUnit</th>
        <th>Man Hours</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1 ; 
      while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['bahan_baku'] ?></td>
        <td><?= $row['jumlah_order'] ?></td>
        <td><?= $row['jenis_pekerjaan'] ?></td>
        <td><?= $row['target_waktu'] ?></td>
        <td><?= $row['jumlah_waktu_kerja'] ?></td>
        <td><?= $row['jumlah_pekerja'] ?></td>
        <td><?= $row['harga_perunit'] ?></td>
        <td><?= $row['man_hours'] ?></td>
        <td>
          <button class="btn btn-sm btn-warning" 
                  data-bs-toggle="modal" 
                  data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
             onclick="return confirm('Yakin hapus data?')">Hapus</a>
        </td>
      </tr>

      <!-- Modal Edit -->
      <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
          <form method="post" class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Data</h5></div>
            <div class="modal-body">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <div class="mb-2">
                <label>Bahan Baku</label>
                <input type="text" name="bahan_baku" value="<?= $row['bahan_baku'] ?>" class="form-control" required>
              </div>
              <div class="mb-2">
                <label>Jumlah Order</label>
                <input type="number" name="jumlah_order" value="<?= $row['jumlah_order'] ?>" class="form-control" required>
              </div>
              <div class="mb-2"><label>Jumlah Waktu Kerja</label>
                <input type="text" class="form-control waktu-kerja" value="<?= $row['jumlah_waktu_kerja'] ?? '' ?>" readonly>
              </div>
              <div class="mb-2"><label>Jumlah Pekerja</label>
                <input type="text" class="form-control jumlah-pekerja" value="<?= $row['jumlah_pekerja'] ?? '' ?>" readonly>
              </div>
              <div class="mb-2">
                <label>Jenis Pekerjaan</label>
                <input type="text" name="jenis_pekerjaan" value="<?= $row['jenis_pekerjaan'] ?>" class="form-control" required>
              </div>
              <div class="mb-2">
                <label>Target Waktu</label>
                <input type="number" name="target_waktu" value="<?= $row['target_waktu'] ?>" class="form-control" required>
              </div>
              <div class="mb-2">
                <label>Harga PerUnit</label>
                <input type="text" name="harga_perunit" value="<?= $row['harga_perunit'] ?>" class="form-control" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="save" class="btn btn-success">Simpan</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
          </form>
        </div>
      </div>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Tambah Data</h5></div>
      <div class="modal-body">
        <div class="mb-2"><label>Bahan Baku</label>
          <input type="text" name="bahan_baku" class="form-control" required>
        </div>
        <div class="mb-2"><label>Jumlah Order</label>
          <input type="number" name="jumlah_order" class="form-control" required>
        </div>
        <div class="mb-2"><label>Jenis Pekerjaan</label>
          <input type="text" name="jenis_pekerjaan" class="form-control" required>
        </div>
        <div class="mb-2"><label>Target Waktu</label>
          <input type="number" name="target_waktu" class="form-control" required>
        </div>
        <div class="mb-2"><label>Harga PerUnit</label>
          <input type="text" name="harga_perunit" class="form-control" required>
        </div>

        <!-- readonly supaya user tahu nilai dihitung otomatis -->
        <div class="mb-2"><label>Jumlah Waktu Kerja</label>
          <input type="text" class="form-control waktu-kerja" value="<?= $row['jumlah_waktu_kerja'] ?? '' ?>" readonly>
        </div>
        <div class="mb-2"><label>Jumlah Pekerja</label>
          <input type="text" class="form-control jumlah-pekerja" value="<?= $row['jumlah_pekerja'] ?? '' ?>" readonly>
        </div>
        <div class="mb-2"><label>Man Hours (otomatis)</label>
          <input type="text" class="form-control" value="25200" readonly>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="save" class="btn btn-success">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function hitungOtomatis(modalId) {
  const modal = document.querySelector(modalId);
  const jumlahOrderInput = modal.querySelector('input[name="jumlah_order"]');
  const targetWaktuInput = modal.querySelector('input[name="target_waktu"]');
  const waktuKerjaField  = modal.querySelector('.waktu-kerja');
  const pekerjaField     = modal.querySelector('.jumlah-pekerja');
  const manHours         = 7 * 60 * 60; // 25200 detik

  function update() {
    const jumlahOrder = parseInt(jumlahOrderInput.value) || 0;
    const targetWaktu = parseInt(targetWaktuInput.value) || 0;
    const jumlahWaktuKerja = jumlahOrder * targetWaktu;
    const jumlahPekerja = jumlahWaktuKerja > 0 ? Math.ceil(jumlahWaktuKerja / manHours) : 0;

    waktuKerjaField.value = jumlahWaktuKerja;
    pekerjaField.value = jumlahPekerja;
  }

  jumlahOrderInput.addEventListener('input', update);
  targetWaktuInput.addEventListener('input', update);
}
document.addEventListener("DOMContentLoaded", function() {
  hitungOtomatis('#addModal');
  document.querySelectorAll('[id^="editModal"]').forEach(modal => {
    hitungOtomatis('#' + modal.id);
  });
});
</script>

</body>
</html>
