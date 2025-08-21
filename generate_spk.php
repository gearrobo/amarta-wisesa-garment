<?php
include 'config/db.php'; // koneksi database

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    // Cek apakah SPK sudah pernah dicetak (berdasarkan sp_srx yang sudah terisi)
    $check_sql = "SELECT sp_srx FROM sps WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $sps_data = $check_result->fetch_assoc();
    
    // Jika sudah ada nomor SPK (sp_srx sudah terisi), skip insert data
    if (!empty($sps_data['sp_srx'])) {
        echo "⚠️ SPK sudah pernah dicetak sebelumnya dengan nomor: " . $sps_data['sp_srx'] . "<br>";
        echo "Data tidak akan diinsert ulang untuk menghindari duplikasi.";
    } else {
        // Ambil data SPS
        $sql = "SELECT * FROM sps WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            // Cek apakah data persiapan sudah ada untuk id_sps ini
            $check_persiapan = "SELECT id FROM persiapan WHERE id_sps = ? LIMIT 1";
            $check_persiapan_stmt = $conn->prepare($check_persiapan);
            $check_persiapan_stmt->bind_param("i", $row['id']);
            $check_persiapan_stmt->execute();
            $check_persiapan_result = $check_persiapan_stmt->get_result();
            
            if ($check_persiapan_result->num_rows > 0) {
                echo "⚠️ Data persiapan untuk SPS ini sudah ada, tidak akan diinsert ulang.<br>";
            } else {
                // Data persiapan (contoh dummy)
                $kode_barang = '123';
                $nama_barang = 'Kain Biru';
                $jumlah = 500;
                $satuan = 'meter';
                $harga = 10;
                $total = $jumlah * $harga;
                $status = 'pending';
                $tanggal_persiapan = $row['kirim']; // pastikan format YYYY-MM-DD

                $insert = "INSERT INTO persiapan 
                  (id_sps, kode_barang, nama_barang, jumlah, satuan, harga, total, tanggal_persiapan, status) 
                  VALUES (?,?,?,?,?,?,?,?,?)";

                $stmt2 = $conn->prepare($insert);
                if ($stmt2) {
                    $stmt2->bind_param(
                        "issisddss",
                        $row['id'],        // id_sps (FK ke sps.id)
                        $kode_barang,      // string
                        $nama_barang,      // string
                        $jumlah,           // int
                        $satuan,           // string
                        $harga,            // double
                        $total,            // double
                        $tanggal_persiapan,// string (date)
                        $status            // string
                    );

                    if ($stmt2->execute()) {
                        echo "✅ Data berhasil disimpan ke persiapan!<br>";
                    } else {
                        echo "❌ Error insert: " . $stmt2->error . "<br>";
                    }

                    $stmt2->close();
                } else {
                    echo "❌ Error prepare insert: " . $conn->error;
                }
            }
            $check_persiapan_stmt->close();
        }
    }
    $check_stmt->close();
}
$id = intval($_GET['id']);

// Ambil data SPS
$result = $conn->query("SELECT * FROM sps WHERE id = $id");
$sps = $result->fetch_assoc();

if (!$sps) {
    die("Data tidak ditemukan!");
}

// Generate nomor SPK
$spk_no = "SPK-" . date('Ymd') . "-" . str_pad($id, 4, '0', STR_PAD_LEFT);

// Update SP SRX dengan nomor SPK
$stmt = $conn->prepare("UPDATE sps SET sp_srx = ? WHERE id = ?");
$stmt->bind_param("si", $spk_no, $id);
$stmt->execute();
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
            <td><?= $spk_no ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td><?= date('d/m/Y') ?></td>
        </tr>
        <tr>
            <td>No. SPS</td>
            <td>:</td>
            <td><?= $sps['sps_no'] ?></td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>:</td>
            <td><?= $sps['customer'] ?></td>
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
                <td><?= $sps['item'] ?></td>
                <td><?= $sps['artikel'] ?></td>
                <td><?= $sps['qty'] ?></td>
                <td><?= $sps['size'] ?></td>
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

    <script>
        window.print();
    </script>
</body>
</html>
