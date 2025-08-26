<?php
include 'includes/header.php';
include 'config/db.php';

// --- Handle Simpan Produksi ---
if (isset($_POST['save'])) {
    $id_sps      = intval($_POST['id_sps']);
    $id_persiapan = intval($_POST['id_persiapan']);
    $kerjaan     = $_POST['kerjaan'];
    $target      = intval($_POST['target']);
    $hasil       = intval($_POST['hasil']);
    $pekerja     = $_POST['pekerja'];
    $status      = $_POST['status'];
    $qc          = $_POST['qc'];

    if ($_POST['id'] == "") {
        // Insert baru
        $stmt = $conn->prepare("INSERT INTO produksi 
            (id_sps, id_persiapan, kerjaan, target, hasil, pekerja, status, qc) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisiiiss", $id_sps, $id_persiapan, $kerjaan, $target, $hasil, $pekerja, $status, $qc);
        $stmt->execute();
    } else {
        // Update
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE produksi 
            SET id_sps=?, id_persiapan=?, kerjaan=?, target=?, hasil=?, pekerja=?, status=?, qc=? 
            WHERE id=?");
        $stmt->bind_param("iisiiissi", $id_sps, $id_persiapan, $kerjaan, $target, $hasil, $pekerja, $status, $qc, $id);
        $stmt->execute();
    }
    header("Location: produksi.php");
    exit();
}

// --- Handle Hapus ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM produksi WHERE id=$id");
    header("Location: produksi.php");
    exit();
}

// --- Ambil Data Produksi ---
$sql = "SELECT p.*, s.sps_no, s.customer, ps.spp_no, ps.nama_barang 
        FROM produksi p
        LEFT JOIN sps s ON p.id_sps = s.id
        LEFT JOIN persiapan ps ON p.id_persiapan = ps.id
        ORDER BY p.id DESC";
$produksi = $conn->query($sql);

// Query untuk mengambil data karyawan
$query_karyawan = "SELECT nama_lengkap, type_karyawan FROM karyawan WHERE type_karyawan IN ('harian', 'borongan') ORDER BY nama_lengkap ASC";
$resultKaryawan = $conn->query($query_karyawan);

// --- Ambil SPS untuk dropdown awal ---
// $sps = $conn->query("SELECT id, sps_no, customer, item FROM sps WHERE sp_srx IS NOT NULL ORDER BY sps_no ASC");

$check_persiapan_sql = "SELECT * FROM persiapan WHERE sp_srx IS NOT NULL";
$result = $conn->query($check_persiapan_sql);

if ($result && $result->num_rows > 0) {
    // echo "✅ Ada " . $result->num_rows . " data persiapan yang memiliki SPK";
    
    // Tampilkan data
    while ($row = $result->fetch_assoc()) {
        $id_persiapan_baru = $row['id'];
        $stmt = $conn->prepare("SELECT id, sps_no, customer, item FROM sps WHERE id = ? ORDER BY sps_no ASC");
        $stmt->bind_param("i", $id_persiapan_baru);
        $stmt->execute();
        $sps = $stmt->get_result();
    }
} else {
    echo "❌ Tidak ada data persiapan yang memiliki SPK";
}
?>

<div class="main-content">
    <div>
        <h1 class="h3 mb-4">Produksi</h1>
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Produksi</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#produksiModal" onclick="addProduksi()">Tambah Produksi</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>No SPS</th>
                <th>Customer</th>
                <th>SPP No / Barang</th>
                <th>Pekerjaan</th>
                <th>Target</th>
                <th>Hasil</th>
                <th>Pekerja</th>
                <th>Status</th>
                <th>QC</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; while($row = $produksi->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['sps_no'] ?></td>
                <td><?= $row['customer'] ?></td>
                <td><?= $row['spp_no'].' | '.$row['nama_barang'] ?></td>
                <td><?= $row['kerjaan'] ?></td>
                <td><?= $row['target'] ?></td>
                <td><?= $row['hasil'] ?></td>
                <td><?= $row['pekerja'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['qc'] ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" 
                        onclick="editProduksi(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Hapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah/Edit Produksi -->
<div class="modal fade" id="produksiModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Form Produksi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="id">

            <!-- Dropdown SPS -->
            <div class="mb-3">
                <label>No SPS</label>
                <select name="id_sps" id="id_sps" class="form-control" required>
                    <option value="">-- Pilih SPS --</option>
                    <?php while($row = $sps->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>">
                            <?= $row['sps_no'] ?> | <?= $row['customer'] ?> | <?= $row['item'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Dropdown Persiapan -->

            <div class="mb-3">
                <label>Pekerjaan</label>
                <select name="pekerja" id="kerjaan" class="form-select">
                    <option value="">-- Pilih Pekerja --</option>
                    <?php
                    if ($resultKaryawan && $resultKaryawan->num_rows > 0) {
                        while ($row = $resultKaryawan->fetch_assoc()) {
                            $value = $row['nama_lengkap'] . " | " . $row['type_karyawan'];
                            echo "<option value='" . htmlspecialchars($value) . "'>";
                            echo htmlspecialchars($row['nama_lengkap']) . " - " . htmlspecialchars($row['type_karyawan']);
                            echo "</option>";
                        }
                    } else {
                        echo "<option value=''>Tidak ada data karyawan</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Target</label>
                <input type="number" name="target" id="target" class="form-control">
            </div>
            <div class="mb-3">
                <label>Hasil</label>
                <input type="number" name="hasil" id="hasil" class="form-control">
            </div>
            <div class="mb-3">
                <label>Pekerja</label>
                <input type="text" name="pekerja" id="pekerja" class="form-control">
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="proses">Proses</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
            <div class="mb-3">
                <label>QC</label>
                <input type="text" name="qc" id="qc" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="save" class="btn btn-success">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- jQuery & Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function addProduksi(){
    $("#id").val("");
    $("#id_sps").val("");
    $("#id_persiapan").html('<option value="">-- Pilih Persiapan --</option>');
    $("#kerjaan, #target, #hasil, #pekerja, #qc").val("");
    $("#status").val("pending");
}

// Edit: isi form dengan data row
function editProduksi(data){
    $("#id").val(data.id);
    $("#id_sps").val(data.id_sps).trigger("change");
    setTimeout(function(){ $("#id_persiapan").val(data.id_persiapan); }, 500);
    $("#kerjaan").val(data.kerjaan);
    $("#target").val(data.target);
    $("#hasil").val(data.hasil);
    $("#pekerja").val(data.pekerja);
    $("#status").val(data.status);
    $("#qc").val(data.qc);
    $("#produksiModal").modal("show");
}

// AJAX untuk ambil Persiapan berdasarkan SPS
$("#id_sps").change(function(){
    var id_sps = $(this).val();
    $.ajax({
        url: "produksi.php",
        type: "POST",
        data: {get_persiapan: 1, id_sps: id_sps},
        success: function(data){
            $("#id_persiapan").html(data);
        }
    });
});
</script>

<?php
// --- AJAX Handler untuk Persiapan ---
if (isset($_POST['get_persiapan'])) {
    $id_sps = intval($_POST['id_sps']);
    $result = $conn->query("SELECT id, spp_no, nama_barang FROM persiapan WHERE id_sps=$id_sps");
    echo '<option value="">-- Pilih Persiapan --</option>';
    while($r = $result->fetch_assoc()){
        echo '<option value="'.$r['id'].'">'.$r['spp_no'].' | '.$r['nama_barang'].'</option>';
    }
    exit();
}
?>
