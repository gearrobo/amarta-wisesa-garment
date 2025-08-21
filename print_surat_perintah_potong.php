<?php
// print_surat_perintah_potong.php
include "config/db.php";
include "includes/header.php";

if (!isset($_GET['id'])) {
    die("ID persiapan tidak ditemukan");
}

$id = intval($_GET['id']);

// Ambil data persiapan
$sql = "SELECT p.*, s.sps_no 
        FROM persiapan p 
        LEFT JOIN sps s ON p.id_sps = s.id 
        WHERE p.id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$persiapan = $result->fetch_assoc();

if (!$persiapan) {
    die("Data persiapan tidak ditemukan");
}

// Format tanggal
$tanggal = date('d/m/Y', strtotime($persiapan['tanggal']));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Surat Perintah Potong</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
        }
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SURAT PERINTAH POTONG</h1>
        <p>No: <?= $persiapan['sps_no'] ?: 'SPP-' . str_pad($persiapan['id'], 4, '0', STR_PAD_LEFT) ?></p>
        <p>Tanggal: <?= $tanggal ?></p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Customer:</div>
            <div class="info-value"><?= $persiapan['customer'] ?: '-' ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Item:</div>
            <div class="info-value"><?= $persiapan['item'] ?: '-' ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Artikel:</div>
            <div class="info-value"><?= $persiapan['artikel'] ?: '-' ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Size:</div>
            <div class="info-value"><?= $persiapan['size'] ?: '-' ?></div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td><?= $persiapan['nama_barang'] ?: $persiapan['item'] ?></td>
                <td><?= number_format($persiapan['jumlah'] ?? 0) ?></td>
                <td><?= $persiapan['satuan'] ?: 'pcs' ?></td>
                <td><?= $persiapan['keterangan'] ?: '-' ?></td>
            </tr>
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>Dibuat oleh,</p>
            <div class="signature-line"></div>
            <p>Admin</p>
        </div>
        <div class="signature-box">
            <p>Disetujui oleh,</p>
            <div class="signature-line"></div>
            <p>Manager Produksi</p>
        </div>
        <div class="signature-box">
            <p>Diketahui oleh,</p>
            <div class="signature-line"></div>
            <p>Kepala Gudang</p>
        </div>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    <script>
        window.onload = function() {
            // Auto print jika diinginkan
            // window.print();
        };
    </script>
</body>
</html>
