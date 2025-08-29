<?php
include 'config/db.php'; // koneksi database

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan!");
}

$id = intval($_GET['id']);

// Ambil data HPP berdasarkan ID
$sql = "SELECT h.*, s.sps_no, s.customer, p.nama_barang, p.id_sps
        FROM hpp h
        LEFT JOIN persiapan p ON h.id_persiapan = p.id
        LEFT JOIN sps s ON p.id_sps = s.id
        WHERE h.id = ?";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$hpp = mysqli_fetch_assoc($result);

if (!$hpp) {
    die("Data HPP tidak ditemukan!");
}

// Generate Nomor PO (kalau belum ada)
if (!empty($hpp['po'])) {
    $po_no = $hpp['po'];
} else {
    $year = date('Y');
    $month = date('m');

    // Cari nomor PO terakhir tahun ini
    $last_po_sql = "SELECT MAX(CAST(SUBSTRING(po, 6, 4) AS UNSIGNED)) as last_num 
                    FROM hpp 
                    WHERE po LIKE 'POBB%' AND YEAR(created_at) = ?";
    $last_po_stmt = mysqli_prepare($conn, $last_po_sql);
    mysqli_stmt_bind_param($last_po_stmt, "i", $year);
    mysqli_stmt_execute($last_po_stmt);
    $last_po_result = mysqli_stmt_get_result($last_po_stmt);
    $last_po = mysqli_fetch_assoc($last_po_result);

    $next_num = ($last_po['last_num'] ?? 0) + 1;
    $po_no = 'POBB' . str_pad($next_num, 4, '0', STR_PAD_LEFT) . '/' . $month . '/' . $year;

    // Update nomor PO ke tabel hpp
    $update_sql = "UPDATE hpp SET po = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "si", $po_no, $id);
    mysqli_stmt_execute($update_stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PO Bahan Baku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-name { font-size: 18px; font-weight: bold; }
        .po-title { font-size: 20px; font-weight: bold; margin: 20px 0; }
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

    <div class="po-title">SURAT PERINTAH PO BAHAN BAKU</div>

    <table class="info-table">
        <tr>
            <td width="150">No. PO</td>
            <td width="10">:</td>
            <td><?= htmlspecialchars($po_no ?? '') ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td><?= date('d/m/Y') ?></td>
        </tr>
        <tr>
            <td>No. SPS</td>
            <td>:</td>
            <td><?= htmlspecialchars($hpp['sps_no'] ?? '') ?></td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>:</td>
            <td><?= htmlspecialchars($hpp['customer'] ?? '') ?></td>
        </tr>
        <tr>
            <td>Gudang</td>
            <td>:</td>
            <td><?= htmlspecialchars($hpp['nama_gudang'] ?? '') ?></td>
        </tr>
    </table>

    <div class="detail-table">
    <h5>Detail Bahan Baku:</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="50">No</th>
                <th>Nama Bahan</th>
                <th>Stok PO Order</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td><?= htmlspecialchars($hpp['bahan'] ?? '-') ?></td>
                <td><?= htmlspecialchars($hpp['po'] ?? '-') ?></td>
            </tr>
        </tbody>
    </table>
</div>


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
        <small>Catatan: PO Bahan Baku ini harus dilaksanakan sesuai dengan spesifikasi yang telah ditentukan.</small>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="btn btn-primary">Cetak PO</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </div>
</body>
</html>
