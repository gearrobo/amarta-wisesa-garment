<?php
include 'config/db.php'; // koneksi database

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan!");
}

$id = intval($_GET['id']);

// Ambil data dari tabel persiapan
$sql = "SELECT * FROM persiapan WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$persiapan = mysqli_fetch_assoc($result);

if (!$persiapan) {
    die("Data persiapan tidak ditemukan!");
}

// Ambil data SPS terkait berdasarkan id_sps
$sql_sps = "SELECT * FROM sps WHERE id = ?";
$stmt_sps = mysqli_prepare($conn, $sql_sps);
if (!$stmt_sps) {
    die("Prepare failed for SPS query: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt_sps, "i", $persiapan['id_sps']);
mysqli_stmt_execute($stmt_sps);
$result_sps = mysqli_stmt_get_result($stmt_sps);
$sps = mysqli_fetch_assoc($result_sps);

if (!$sps) {
    die("Data SPS tidak ditemukan!");
}

// Ambil data persiapan detail untuk tabel database preparation
$sql_detail = "SELECT * FROM persiapan WHERE id = ? ORDER BY id ASC";
$stmt_detail = mysqli_prepare($conn, $sql_detail);
if (!$stmt_detail) {
    die("Prepare failed for detail query: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt_detail, "i", $id);
mysqli_stmt_execute($stmt_detail);
$result_detail = mysqli_stmt_get_result($stmt_detail);
$persiapan_details = [];
while ($row = mysqli_fetch_assoc($result_detail)) {
    $persiapan_details[] = $row;
}

// Cek apakah SPP sudah pernah dicetak (berdasarkan spp_no yang sudah terisi)
$check_sql = "SELECT spp_no FROM persiapan WHERE id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
if (!$check_stmt) {
    die("Prepare failed for check query: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($check_stmt, "i", $id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$persiapan_data = mysqli_fetch_assoc($check_result);

// Jika sudah ada nomor SPP (spp_no sudah terisi), gunakan nomor yang sudah ada
if (!empty($persiapan_data['spp_no'])) {
    $spp_no = $persiapan_data['spp_no'];
} else {
    // Generate nomor SPP baru
    $year = date('Y');
    $month = date('m');
    
    // Cari nomor SPP terakhir
    $last_spp_sql = "SELECT MAX(CAST(SUBSTRING(spp_no, 4, 4) AS UNSIGNED)) as last_num 
                     FROM persiapan 
                     WHERE spp_no LIKE 'SPP%' AND YEAR(created_at) = ?";
    $last_spp_stmt = mysqli_prepare($conn, $last_spp_sql);
    if (!$last_spp_stmt) {
        die("Prepare failed for last SPP query: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($last_spp_stmt, "i", $year);
    mysqli_stmt_execute($last_spp_stmt);
    $last_spp_result = mysqli_stmt_get_result($last_spp_stmt);
    $last_spp = mysqli_fetch_assoc($last_spp_result);
    
    $next_num = ($last_spp['last_num'] ?? 0) + 1;
    $spp_no = 'SPP' . str_pad($next_num, 4, '0', STR_PAD_LEFT);

    // Update nomor SPP ke database
    $update_sql = "UPDATE persiapan SET spp_no = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    if (!$update_stmt) {
        die("Prepare failed for update query: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($update_stmt, "si", $spp_no, $id);
    mysqli_stmt_execute($update_stmt);
}

// Ambil data yang sudah diupdate
$final_sql = "SELECT p.*, s.sps_no, s.customer, s.item, s.artikel, s.qty, s.size, s.kirim 
              FROM persiapan p 
              JOIN sps s ON p.id_sps = s.id 
              WHERE p.id = ?";
$final_stmt = mysqli_prepare($conn, $final_sql);
if (!$final_stmt) {
    die("Prepare failed for final query: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($final_stmt, "i", $id);
mysqli_stmt_execute($final_stmt);
$final_result = mysqli_stmt_get_result($final_stmt);
$final_data = mysqli_fetch_assoc($final_result);

if (!$final_data) {
    die("Data tidak ditemukan setelah update!");
}

// Gunakan data yang sudah diupdate
$spp_no = $final_data['spp_no'];
$sps = $final_data;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SPP - Surat Perintah Potong</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-name { font-size: 18px; font-weight: bold; }
        .spp-title { font-size: 20px; font-weight: bold; margin: 20px 0; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .signature { margin-top: 50px; }
        .signature div { margin-bottom: 40px; }
        .detail-table { margin-top: 30px; }
        .detail-table th { background-color: #f8f9fa; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">CV. AMARTA WISESA</div>
        <div>Jl. Brigjend. Katamso No.48-50, Kauman, Kec. Klojen, Kota Malang, Jawa Timur 65119</div>
        <div>Telp: (62) 8125202033</div>
    </div>

    <div class="spp-title">SURAT PERINTAH POTONG (SPP)</div>

    <table class="info-table">
        <tr>
            <td width="150">No. SPP</td>
            <td width="10">:</td>
            <td><?= htmlspecialchars($spp_no) ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td><?= date('d/m/Y') ?></td>
        </tr>
        <tr>
            <td>No. SPS</td>
            <td>:</td>
            <td><?= htmlspecialchars($sps['sps_no']) ?></td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>:</td>
            <td><?= htmlspecialchars($sps['customer']) ?></td>
        </tr>
        <tr>
            <td>Item</td>
            <td>:</td>
            <td><?= htmlspecialchars($sps['item']) ?></td>
        </tr>
        <tr>
            <td>Artikel</td>
            <td>:</td>
            <td><?= htmlspecialchars($sps['artikel']) ?></td>
        </tr>
        <tr>
            <td>Size</td>
            <td>:</td>
            <td><?= htmlspecialchars($sps['size']) ?></td>
        </tr>
        <tr>
            <td>Tanggal Kirim</td>
            <td>:</td>
            <td><?= date('d/m/Y', strtotime($sps['kirim'])) ?></td>
        </tr>
    </table>

    <?php if (!empty($persiapan_details)): ?>
    <div class="detail-table">
        <h5>Data Persiapan Database:</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($persiapan_details as $index => $detail): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($detail['kode_barang'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($detail['nama_barang'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($detail['jumlah'] ?? '0') ?></td>
                    <td><?= htmlspecialchars($detail['satuan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($detail['keterangan'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="alert alert-info mt-3">
        <strong>Info:</strong> Tidak ada data persiapan yang ditemukan untuk SPP ini.
    </div>
    <?php endif; ?>

    <div class="signature">
        <div class="row">
            <div class="col-md-4 text-center">
                <div>Dibuat oleh,</div>
                <div style="margin-top: 60px;">(_________________)</div>
            </div>
            <div class="col-md-4 text-center">
                <div>Disetujui oleh,</div>
                <div style="margin-top: 60px;">(_________________)</div>
            </div>
            <div class="col-md-4 text-center">
                <div>Diketahui oleh,</div>
                <div style="margin-top: 60px;">(_________________)</div>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px;">
        <small>Catatan: SPP ini harus dilaksanakan sesuai dengan spesifikasi yang telah ditentukan.</small>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="btn btn-primary">Cetak SPP</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </div>

    <script>
        // Auto print saat halaman dimuat
        window.addEventListener('load', function() {
            setTimeout(function() {
                if (window.location.search.indexOf('print=1') > -1) {
                    window.print();
                }
            }, 1000);
        });
    </script>
</body>
</html>
