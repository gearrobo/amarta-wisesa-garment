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

// Cek apakah SPK sudah pernah dicetak (berdasarkan sp_srx yang sudah terisi)
$check_sql = "SELECT sp_srx FROM persiapan WHERE id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
if (!$check_stmt) {
    die("Prepare failed for check query: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($check_stmt, "i", $id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$persiapan_data = mysqli_fetch_assoc($check_result);

// Jika sudah ada nomor SPK (sp_srx sudah terisi), gunakan nomor yang sudah ada
if (!empty($persiapan_data['sp_srx'])) {
    $spk_no = $persiapan_data['sp_srx'];
} else {
    // Generate nomor SPK baru
    $year = date('Y');
    $month = date('m');
    
    // Cari nomor SPK terakhir
    $last_spk_sql = "SELECT MAX(CAST(SUBSTRING(sp_srx, 4, 4) AS UNSIGNED)) as last_num 
                     FROM persiapan 
                     WHERE sp_srx LIKE 'SPK%' AND YEAR(created_at) = ?";
    $last_spk_stmt = mysqli_prepare($conn, $last_spk_sql);
    if (!$last_spk_stmt) {
        die("Prepare failed for last SPK query: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($last_spk_stmt, "i", $year);
    mysqli_stmt_execute($last_spk_stmt);
    $last_spk_result = mysqli_stmt_get_result($last_spk_stmt);
    $last_spk = mysqli_fetch_assoc($last_spk_result);
    
    $next_num = ($last_spk['last_num'] ?? 0) + 1;
    $spk_no = 'SPK' . str_pad($next_num, 4, '0', STR_PAD_LEFT);

    // Update nomor SPK ke database
    $update_sql = "UPDATE persiapan SET sp_srx = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    if (!$update_stmt) {
        die("Prepare failed for update query: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($update_stmt, "si", $spk_no, $id);
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
$spk_no = $final_data['sp_srx'];
$sps = $final_data;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perintah Kerja (SPK)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-name { font-size: 18px; font-weight: bold; }
        .spk-title { font-size: 20px; font-weight: bold; margin: 20px 0; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .signature { margin-top: 50px; }
        .signature div { margin-bottom: 40px; }
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

    <div class="spk-title">SURAT PERINTAH KERJA (SPK)</div>

    <table class="info-table">
        <tr>
            <td width="150">No. SPK</td>
            <td width="10">:</td>
            <td><?= htmlspecialchars($spk_no) ?></td>
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
    </table>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Item</th>
                <th>Artikel</th>
                <th>Qty</th>
                <th>Size</th>
                <th>Tanggal Kirim</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= htmlspecialchars($sps['item']) ?></td>
                <td><?= htmlspecialchars($sps['artikel']) ?></td>
                <td><?= htmlspecialchars($sps['qty']) ?></td>
                <td><?= htmlspecialchars($sps['size']) ?></td>
                <td><?= date('d/m/Y', strtotime($sps['kirim'])) ?></td>
            </tr>
        </tbody>
    </table>

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
        <small>Catatan: SPK ini harus dilaksanakan sesuai dengan spesifikasi yang telah ditentukan.</small>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="btn btn-primary">Cetak SPK</button>
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
