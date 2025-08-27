<?php
include 'includes/header.php';
include 'config/db.php';

// --- Handle Simpan Produksi ---
if (isset($_POST['save'])) {
    $id_sps      = intval($_POST['id_sps']);
    $kerjaan     = $_POST['kerjaan'];
    $target      = intval($_POST['target']);
    $hasil       = intval($_POST['hasil']);
    $pekerja     = $_POST['pekerja'];
    $status      = $_POST['status'];
    $qc          = $_POST['qc'];

    if ($_POST['id'] == "") {
        // Insert baru
        $stmt = $conn->prepare("INSERT INTO produksi 
            (id_sps, kerjaan, target, hasil, pekerja, status, qc) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isiisss", 
    $id_sps, 
    $kerjaan, 
    $target, 
    $hasil, 
    $pekerja, 
    $status, 
    $qc
);
        $stmt->execute();
    } else {
        // Update
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE produksi 
            SET id_sps=?, kerjaan=?, target=?, hasil=?, pekerja=?, status=?, qc=? 
            WHERE id=?");
        $stmt->bind_param("isiisssi", 
    $id_sps, 
    $kerjaan, 
    $target, 
    $hasil, 
    $pekerja, 
    $status, 
    $qc, 
    $id
);
        if (!$stmt->execute()) {
    die("Gagal simpan produksi: " . $stmt->error);
}
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

// --- Handle Approve/Selesai ---
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $conn->prepare("UPDATE produksi SET status='selesai' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: produksi.php");
    exit();
}

// --- Ambil Data Produksi ---
$sql = "SELECT p.id, p.id_sps, p.kerjaan, p.target, p.hasil, p.pekerja, p.status, p.qc, 
               s.sps_no, s.customer, ps.spp_no, ps.nama_barang 
        FROM produksi p
        LEFT JOIN sps s ON p.id_sps = s.id
        LEFT JOIN persiapan ps ON ps.id_sps = p.id_sps"; 

$produksi = $conn->query($sql);

// Validasi jumlah kolom untuk DataTables
if ($produksi) {
    $expected_columns = 12; // Jumlah kolom di thead
    $first_row = $produksi->fetch_assoc();
    if ($first_row && count($first_row) !== $expected_columns) {
        echo "<div class='alert alert-warning'>Peringatan: Jumlah kolom dari query (" . count($first_row) . ") tidak sesuai dengan yang diharapkan oleh tabel (" . $expected_columns . "). Silakan periksa query SQL.</div>";
    }
    // Kembalikan pointer ke awal
    $produksi->data_seek(0);
}

// Query dengan JOIN ke tabel jabatan untuk memastikan yang jabatan QC
$query_karyawan_qc = "SELECT nama_lengkap FROM karyawan WHERE id_jabatan = 3 ";
$resultKaryawan_qc = $conn->query($query_karyawan_qc);


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
        $id_persiapan_baru = $row['id_sps'];
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
                <li class="breadcrumb-item"><a href="persiapan.php">Persiapan</a></li>
                <li class="breadcrumb-item active">Data Persiapan</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#produksiModal" onclick="addProduksi()">Tambah Produksi</button>

    <table id="produksiTable" class="table table-bordered">
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
                    <div class="btn-group btn-group-sm" role="group">
                    <?php if($row['status'] != 'selesai'): ?>
                        <a href="?approve=<?= $row['id'] ?>" class="btn btn-success btn-sm"
                            onclick="return confirm('Apakah anda yakin ingin menyelesaikan proses ini?')"
                            title="Approve - Tandai Selesai">
                            <i class="fas fa-check-circle"></i>
                        </a>
                    <?php endif; ?>
                    <button class="btn btn-warning btn-sm" 
                        onclick="editProduksi(<?= htmlspecialchars(json_encode($row)) ?>)"><i class="fas fa-edit"></i></button>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
                    </div>
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

            <div class="mb-3">
                <label>Pekerjaan</label>
                <input type="text" name="kerjaan" id="kerjaan" class="form-control" required>
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
                <select name="pekerja" id="pekerja" class="form-select">
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
                <label>Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="proses">Proses</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
            <div class="mb-3">
                <label>QC</label>
                <select name="qc" id="qc" class="form-select" required>
                    <option value="">-- Pilih QC --</option>
                    <?php
                    if ($resultKaryawan_qc && $resultKaryawan_qc->num_rows > 0) {
                        while ($row = $resultKaryawan_qc->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['nama_lengkap']) . "'>";
                            echo htmlspecialchars($row['nama_lengkap']);
                            echo "</option>";
                        }
                    } else {
                        echo "<option value=''>Tidak ada QC tersedia</option>";
                    }
                    ?>
                </select>
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
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
<script>
$(document).ready(function() {
    $('#produksiTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        order: [[0, 'asc']]
    });
});

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
