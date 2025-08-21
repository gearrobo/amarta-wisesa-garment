<?php
include 'config/db.php'; // koneksi database

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan!");
}

$id = intval($_GET['id']);

// Ambil data dari tabel persiapan
$sql = "SELECT * FROM persiapan WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$persiapan = $result->fetch_assoc();

if (!$persiapan) {
    die("Data persiapan tidak ditemukan!");
}

// Ambil data SPS terkait berdasarkan id_sps
$sql_sps = "SELECT * FROM sps WHERE id = ?";
$stmt_sps = $conn->prepare($sql_sps);
$stmt_sps->bind_param("i", $persiapan['id_sps']);
$stmt_sps->execute();
$result_sps = $stmt_sps->get_result();
$sps = $result_sps->fetch_assoc();

if (!$sps) {
    die("Data SPS tidak ditemukan!");
}

// Cek apakah SPK sudah pernah dicetak (berdasarkan sp_srx yang sudah terisi)
$check_sql = "SELECT sp_srx FROM persiapan WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$persiapan_data = $check_result->fetch_assoc();

// Jika sudah ada nomor SPK (sp_srx sudah terisi), gunakan nomor yang sudah ada
if (!empty($persiapan_data['sp_srx'])) {
    $spk_no = $persiapan_data['sp_srx'];
} else {
    // Generate nomor SPK baru
    $spk_no = "SPK-" . date('Ymd') . "-" . str_pad($id, 4, '0', STR_PAD_LEFT);
    
    // Update SP SRX dengan nomor SPK di tabel persiapan
    $update_stmt = $conn->prepare("UPDATE persiapan SET sp_srx = ? WHERE id = ?");
    $update_stmt->bind_param("si", $spk_no, $id);
    $update_stmt->execute();
    $update_stmt->close();
}

$stmt->close();
$check_stmt->close();
$stmt_sps->close();
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
