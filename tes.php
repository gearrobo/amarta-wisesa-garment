<?php
include 'config/db.php';

// Get Persiapan ID from URL parameter
$id_persiapan = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_persiapan == 0) {
    die("ID Persiapan tidak valid. Gunakan URL: hpp.php?id=2");
}

// Get Persiapan details
$persiapan_query = "SELECT p.*, s.sps_no, s.customer, s.item, s.artikel 
                    FROM persiapan p 
                    LEFT JOIN sps s ON p.id_sps = s.id 
                    WHERE p.id = ?";
$persiapan_stmt = $conn->prepare($persiapan_query);
$persiapan_stmt->bind_param("i", $id_persiapan);
$persiapan_stmt->execute();
$persiapan_result = $persiapan_stmt->get_result();
$persiapan_data = $persiapan_result->fetch_assoc();
$persiapan_stmt->close();

if (!$persiapan_data) {
    die("Data Persiapan tidak ditemukan untuk ID: " . $id_persiapan);
}

// Get SPS details
$sps_id = $persiapan_data['id_sps'];
$sps_query = "SELECT * FROM sps WHERE id = ?";
$sps_stmt = $conn->prepare($sps_query);
$sps_stmt->bind_param("i", $sps_id);
$sps_stmt->execute();
$sps_result = $sps_stmt->get_result();
$sps_data = $sps_result->fetch_assoc();
$sps_stmt->close();

// Get HPP ID for editing
$hpp_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$hpp_data = null;

// If we're editing, fetch the HPP data
if ($hpp_id > 0) {
    $hpp_query = "SELECT hpp.*, ig.nama_barang as nama_bahan, g.nama as nama_gudang
                  FROM hpp
                  LEFT JOIN inventory_gudang ig ON hpp.id_inventory = ig.id
                  LEFT JOIN gudang g ON ig.id_gudang = g.id
                  WHERE hpp.id = ? AND hpp.id_persiapan = ?";
    $hpp_stmt = $conn->prepare($hpp_query);
    $hpp_stmt->bind_param("ii", $hpp_id, $id_persiapan);
    $hpp_stmt->execute();
    $hpp_result = $hpp_stmt->get_result();
    $hpp_data = $hpp_result->fetch_assoc();
    $hpp_stmt->close();
}

// Update Data HPP
if (isset($_POST['update'])) {
    $hpp_id = $_POST['hpp_id'];
    $id_inventory = $_POST['id_inventory'] ?? null;
    $no_urut = $_POST['no_urut'];
    $bahan = $_POST['bahan'];
    $qty = $_POST['qty'];
    $barang_jadi = $_POST['barang_jadi'];
    $stok_order = $_POST['stok_order'] ?? 0;
    $efisiensi_consp = $_POST['efisiensi_consp'] ?? 0;
    $efisiensi_rap = $_POST['efisiensi_rap'] ?? 0;
    $stok_material = $_POST['stok_material'] ?? 0;
    $po = $_POST['po'] ?? 0;
    $harga_per_meter = $_POST['harga_per_meter'] ?? 0;
    $biaya_tenaga_kerja_per_qty = $_POST['biaya_tenaga_kerja_per_qty'] ?? 0;
    $listrik = $_POST['listrik'] ?? 0;
    $air = $_POST['air'] ?? 0;
    $overhead = $_POST['overhead'] ?? 0;
    $profit = $_POST['profit'] ?? 0;

    // Hitung nilai-nilai yang perlu dihitung
    $rap_x_harga_per_m = $efisiensi_rap * $harga_per_meter;
    $total_harga_bahan = $qty * $rap_x_harga_per_m;
    $total_biaya_tenaga_kerja = $qty * $biaya_tenaga_kerja_per_qty;
    $total_biaya = $total_harga_bahan + $total_biaya_tenaga_kerja + $listrik + $air + $overhead;
    $hpp = $qty > 0 ? $total_biaya / $qty : 0;
    $harga_jual = $hpp * (1 + ($profit / 100));

    $sql = "UPDATE hpp SET 
                id_inventory = ?, 
                no_urut = ?, 
                bahan = ?, 
                qty = ?, 
                barang_jadi = ?, 
                stok_order = ?, 
                efisiensi_consp = ?, 
                efisiensi_rap = ?, 
                stok_material = ?, 
                po = ?, 
                harga_per_meter = ?, 
                rap_x_harga_per_m = ?, 
                total_harga_bahan = ?, 
                biaya_tenaga_kerja_per_qty = ?, 
                total_biaya_tenaga_kerja = ?, 
                listrik = ?, 
                air = ?, 
                overhead = ?, 
                total_biaya = ?, 
                hpp = ?, 
                profit = ?, 
                harga_jual = ?
            WHERE id = ? AND id_persiapan = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("issiiidddiiddddddddddii", 
            $id_inventory, 
            $no_urut, 
            $bahan, 
            $qty, 
            $barang_jadi, 
            $stok_order, 
            $efisiensi_consp, 
            $efisiensi_rap, 
            $stok_material, 
            $po, 
            $harga_per_meter, 
            $rap_x_harga_per_m, 
            $total_harga_bahan, 
            $biaya_tenaga_kerja_per_qty, 
            $total_biaya_tenaga_kerja, 
            $listrik, 
            $air, 
            $overhead, 
            $total_biaya, 
            $hpp, 
            $profit, 
            $harga_jual,
            $hpp_id,
            $id_persiapan
        );
        
        if ($stmt->execute()) {
            $success_msg = "Data HPP berhasil diperbarui";
            // Refresh data
            $hpp_query = "SELECT hpp.*, ig.nama_barang as nama_bahan, g.nama as nama_gudang
                          FROM hpp
                          LEFT JOIN inventory_gudang ig ON hpp.id_inventory = ig.id
                          LEFT JOIN gudang g ON ig.id_gudang = g.id
                          WHERE hpp.id = ? AND hpp.id_persiapan = ?";
            $hpp_stmt = $conn->prepare($hpp_query);
            $hpp_stmt->bind_param("ii", $hpp_id, $id_persiapan);
            $hpp_stmt->execute();
            $hpp_result = $hpp_stmt->get_result();
            $hpp_data = $hpp_result->fetch_assoc();
            $hpp_stmt->close();
        } else {
            $error_msg = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Error dalam persiapan query: " . $conn->error;
    }
}

// Tambah Data HPP
if (isset($_POST['save'])) {
    $id_inventory = $_POST['id_inventory'] ?? null;
    $no_urut = $_POST['no_urut'];
    $bahan = $_POST['bahan'];
    $qty = $_POST['qty'];
    $barang_jadi = $_POST['barang_jadi'];
    $stok_order = $_POST['stok_order'] ?? 0;
    $efisiensi_consp = $_POST['efisiensi_consp'] ?? 0;
    $efisiensi_rap = $_POST['efisiensi_rap'] ?? 0;
    $stok_material = $_POST['stok_material'] ?? 0;
    $po = $_POST['po'] ?? 0;
    $harga_per_meter = $_POST['harga_per_meter'] ?? 0;
    $biaya_tenaga_kerja_per_qty = $_POST['biaya_tenaga_kerja_per_qty'] ?? 0;
    $listrik = $_POST['listrik'] ?? 0;
    $air = $_POST['air'] ?? 0;
    $overhead = $_POST['overhead'] ?? 0;
    $profit = $_POST['profit'] ?? 0;

    // Hitung nilai-nilai yang perlu dihitung
    $rap_x_harga_per_m = $efisiensi_rap * $harga_per_meter;
    $total_harga_bahan = $qty * $rap_x_harga_per_m;
    $total_biaya_tenaga_kerja = $qty * $biaya_tenaga_kerja_per_qty;
    $total_biaya = $total_harga_bahan + $total_biaya_tenaga_kerja + $listrik + $air + $overhead;
    $hpp = $qty > 0 ? $total_biaya / $qty : 0;
    $harga_jual = $hpp * (1 + ($profit / 100));

    $sql = "INSERT INTO hpp (id_persiapan, id_inventory, no_urut, bahan, qty, barang_jadi, stok_order, efisiensi_consp, efisiensi_rap, stok_material, po, harga_per_meter, rap_x_harga_per_m, total_harga_bahan, biaya_tenaga_kerja_per_qty, total_biaya_tenaga_kerja, listrik, air, overhead, total_biaya, hpp, profit, harga_jual) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iissiiidddiiddddddddddd", 
            $id_persiapan, 
            $id_inventory, 
            $no_urut, 
            $bahan, 
            $qty, 
            $barang_jadi, 
            $stok_order, 
            $efisiensi_consp, 
            $efisiensi_rap, 
            $stok_material, 
            $po, 
            $harga_per_meter, 
            $rap_x_harga_per_m, 
            $total_harga_bahan, 
            $biaya_tenaga_kerja_per_qty, 
            $total_biaya_tenaga_kerja, 
            $listrik, 
            $air, 
            $overhead, 
            $total_biaya, 
            $hpp, 
            $profit, 
            $harga_jual
        );
        
        if ($stmt->execute()) {
            $success_msg = "Data HPP berhasil ditambahkan untuk SPP " . $persiapan_data['spp_no'];
        } else {
            $error_msg = "Gagal menambahkan data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Error dalam persiapan query: " . $conn->error;
    }
}

// Hapus Data
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Verifikasi bahwa HPP yang akan dihapus memang milik persiapan ini
    $verify_query = "SELECT id FROM hpp WHERE id = ? AND id_persiapan = ?";
    $verify_stmt = $conn->prepare($verify_query);
    $verify_stmt->bind_param("ii", $id, $id_persiapan);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows === 0) {
        $error_msg = "Error: Data HPP tidak valid untuk persiapan ini";
    } else {
        $sql = "DELETE FROM hpp WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $success_msg = "Data HPP berhasil dihapus";
            } else {
                $error_msg = "Gagal menghapus data: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Error dalam persiapan query: " . $conn->error;
        }
    }
    $verify_stmt->close();
}

// Ambil Data HPP hanya untuk persiapan ini
$query = "
    SELECT hpp.*, 
           ig.nama_barang as nama_bahan, g.nama as nama_gudang
    FROM hpp
    LEFT JOIN inventory_gudang ig ON hpp.id_inventory = ig.id
    LEFT JOIN gudang g ON ig.id_gudang = g.id
    WHERE hpp.id_persiapan = ?
    ORDER BY hpp.id DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_persiapan);
$stmt->execute();
$result = $stmt->get_result();

// Ambil data inventory untuk dropdown
$inventory_query = "
    SELECT ig.id, ig.nama_barang, g.nama as nama_gudang, ig.stok_akhir, i.harga_per_unit
    FROM inventory_gudang ig
    LEFT JOIN gudang g ON ig.id_gudang = g.id
    LEFT JOIN inventory i ON ig.id_inventory = i.id
    WHERE ig.stok_akhir > 0
    ORDER BY g.nama, ig.nama_barang
";
$inventory_result = $conn->query($inventory_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HPP untuk SPP <?= htmlspecialchars($persiapan_data['spp_no']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--dark) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card-custom {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border: none;
        }
        
        .card-custom:hover {
            transform: translateY(-5px);
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 600;
        }
        
        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning) 0%, #e67e22 100%);
            border: none;
            border-radius: 6px;
            padding: 5px 15px;
            font-weight: 600;
            color: white;
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, var(--accent) 0%, #c0392b 100%);
            border: none;
            border-radius: 6px;
            padding: 5px 15px;
            font-weight: 600;
        }
        
        .table-custom {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        
        .table-custom th {
            background-color: var(--primary);
            color: white;
            padding: 12px 15px;
            position: sticky;
            top: 0;
        }
        
        .table-custom td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .table-custom tr:nth-child(even) {
            background-color: #f5f7f9;
        }
        
        .table-custom tr:hover {
            background-color: #e8f4fc;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-inactive {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .search-container {
            position: relative;
            margin-bottom: 20px;
        }
        
        .search-input {
            border-radius: 50px;
            padding-left: 45px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 10px;
            color: #7f8c8d;
        }
        
        .summary-card {
            text-align: center;
            padding: 20px;
        }
        
        .summary-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--secondary);
            margin: 10px 0;
        }
        
        .summary-title {
            font-size: 1rem;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .modal-header-custom {
            background: linear-gradient(135deg, var(--secondary) 0%, #2980b9 100%);
            color: white;
        }
        
        .project-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .inventory-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
            
            .summary-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body class="p-4">

<div class="container">
    <!-- Project Header -->
    <div class="project-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">HPP untuk SPP: <?= htmlspecialchars($persiapan_data['spp_no']) ?></h2>
                <p class="mb-0">SPS: <?= htmlspecialchars($sps_data['sps_no']) ?> | Customer: <?= htmlspecialchars($sps_data['customer']) ?> | Item: <?= htmlspecialchars($sps_data['item']) ?></p>
                <p class="mb-0">Artikel: <?= htmlspecialchars($sps_data['artikel']) ?> | Produk: <?= htmlspecialchars($persiapan_data['nama_barang']) ?></p>
            </div>
            <div>
                <a href="persiapan.php" class="btn btn-light">Kembali ke Semua HPP</a>
            </div>
        </div>
    </div>

    <!-- Notifikasi -->
    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success_msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error_msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card card-custom summary-card">
                <div class="summary-title">Total Data HPP</div>
                <div class="summary-number"><?= $result->num_rows ?></div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom summary-card">
                <div class="summary-title">Rata-rata HPP</div>
                <div class="summary-number">
                    <?php
                    $total_hpp = 0;
                    $count = 0;
                    if ($result->num_rows > 0) {
                        $result->data_seek(0);
                        while($row = $result->fetch_assoc()) {
                            $total_hpp += $row['hpp'];
                            $count++;
                        }
                        $result->data_seek(0); // Reset pointer
                        echo 'Rp ' . number_format($count > 0 ? $total_hpp  : 0, 0, ',', '.');
                    } else {
                        echo 'Rp 0';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom summary-card">
                <div class="summary-title">Total QTY</div>
                <div class="summary-number">
                    <?php
                    $total_qty = 0;
                    if ($result->num_rows > 0) {
                        $result->data_seek(0);
                        while($row = $result->fetch_assoc()) {
                            $total_qty += $row['qty'];
                        }
                        $result->data_seek(0); // Reset pointer
                        echo number_format($total_qty, 0, ',', '.');
                    } else {
                        echo '0';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom summary-card">
                <div class="summary-title">Total Barang Jadi</div>
                <div class="summary-number">
                    <?php
                    $total_barang_jadi = 0;
                    if ($result->num_rows > 0) {
                        $result->data_seek(0);
                        while($row = $result->fetch_assoc()) {
                            $total_barang_jadi += $row['barang_jadi'];
                        }
                        $result->data_seek(0); // Reset pointer
                        echo number_format($total_barang_jadi, 0, ',', '.');
                    } else {
                        echo '0';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Tambah -->
    <button class="btn btn-primary-custom mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus-circle me-2"></i>Tambah Data HPP
    </button>

    <!-- Tabel -->
    <div class="card card-custom">
        <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Data HPP untuk SPP <?= htmlspecialchars($persiapan_data['spp_no']) ?></h5>
            <input type="text" class="form-control search-input" placeholder="Cari data HPP..." style="max-width: 300px;">
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gudang</th>
                            <th>Bahan</th>
                            <th>QTY</th>
                            <th>Barang Jadi</th>
                            <th>Stok Material</th>
                            <th>HPP/Unit</th>
                            <th>Harga Jual</th>
                            <th>Profit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $no = 1;
                    if ($result->num_rows > 0):
                        while($row = $result->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_gudang'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['bahan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['qty']) ?></td>
                            <td><?= htmlspecialchars($row['barang_jadi']) ?></td>
                            <td><?= htmlspecialchars($row['stok_material'] ?? 0) ?></td>
                            <td>Rp <?= number_format($row['hpp'] ?? 0, 2, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['harga_jual'] ?? 0, 2, ',', '.') ?></td>
                            <td><span class="status-badge status-active"><?= htmlspecialchars($row['profit'] ?? 0) ?>%</span></td>
                            <td>
                                <a href="?id=<?= $id_persiapan ?>&edit=<?= $row['id'] ?>" class="btn btn-warning-custom btn-sm mb-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?id=<?= $id_persiapan ?>&delete=<?= $row['id'] ?>" class="btn btn-danger-custom btn-sm"
                                    onclick="return confirm('Yakin hapus data HPP ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data HPP untuk SPP ini</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title">Tambah Data HPP untuk SPP <?= htmlspecialchars($persiapan_data['spp_no']) ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_persiapan" value="<?= $id_persiapan ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Inventory</label>
                            <select name="id_inventory" class="form-control" id="addInventory" onchange="updateInventoryInfo('add')">
                                <option value="">Pilih Inventory</option>
                                <?php
                                $inventory_result->data_seek(0);
                                while($opt = $inventory_result->fetch_assoc()):
                                ?>
                                    <option value="<?= $opt['id'] ?>" 
                                            data-stok="<?= $opt['stok_akhir'] ?>" 
                                            data-harga="<?= $opt['harga_per_unit'] ?>" 
                                            data-gudang="<?= $opt['nama_gudang'] ?>"
                                            data-bahan="<?= $opt['nama_barang'] ?>">
                                        <?= $opt['nama_gudang'] ?> - <?= $opt['nama_barang'] ?> (Stok: <?= $opt['stok_akhir'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div id="addInventoryInfo" class="inventory-info" style="display: none;">
                        <strong>Informasi Inventory:</strong>
                        <div>Gudang: <span id="addGudangInfo"></span></div>
                        <div>Stok Tersedia: <span id="addStokInfo"></span></div>
                        <div>Harga per Unit: Rp <span id="addHargaInfo"></span></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">No Urut</label>
                            <input type="text" name="no_urut" class="form-control" required>
                        </div>
                        <div class="col-md-8 mb-2">
                            <label class="form-label">Bahan</label>
                            <input type="text" name="bahan" class="form-control" id="addBahan" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">QTY</label>
                            <input type="number" name="qty" class="form-control" required min="1">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Barang Jadi</label>
                            <input type="number" name="barang_jadi" class="form-control" required min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Stok Order</label>
                            <input type="number" name="stok_order" class="form-control" value="0" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Efisiensi Consp</label>
                            <input type="number" step="0.01" name="efisiensi_consp" class="form-control" value="1.00" min="0.1" max="2.0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Efisiensi RAP</label>
                            <input type="number" step="0.01" name="efisiensi_rap" class="form-control" value="1.00" min="0.1" max="2.0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Stok Material</label>
                            <input type="number" name="stok_material" class="form-control" id="addStokMaterial" value="0" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">PO</label>
                            <input type="number" name="po" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Harga per Meter</label>
                            <input type="number" step="0.01" name="harga_per_meter" class="form-control" id="addHargaPerMeter" value="0" min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Biaya Tenaga Kerja per Qty</label>
                            <input type="number" step="0.01" name="biaya_tenaga_kerja_per_qty" class="form-control" value="0" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Listrik</label>
                            <input type="number" step="0.01" name="listrik" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Air</label>
                            <input type="number" step="0.01" name="air" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Overhead</label>
                            <input type="number" step="0.01" name="overhead" class="form-control" value="0" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Profit (%)</label>
                            <input type="number" step="0.01" name="profit" class="form-control" value="30" min="0" max="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="save" class="btn btn-primary-custom">Simpan Data</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<?php if ($hpp_id > 0 && $hpp_data): ?>
<div class="modal fade show" id="editModal" tabindex="-1" style="display: block; padding-right: 17px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title">Edit Data HPP</h5>
                    <a href="?id=<?= $id_persiapan ?>" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="hpp_id" value="<?= $hpp_data['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Inventory</label>
                            <select name="id_inventory" class="form-control" id="editInventory" onchange="updateInventoryInfo('edit')">
                                <option value="">Pilih Inventory</option>
                                <?php
                                $inventory_result->data_seek(0);
                                while($opt = $inventory_result->fetch_assoc()):
                                    $selected = ($opt['id'] == $hpp_data['id_inventory']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $opt['id'] ?>" 
                                            data-stok="<?= $opt['stok_akhir'] ?>" 
                                            data-harga="<?= $opt['harga_per_unit'] ?>" 
                                            data-gudang="<?= $opt['nama_gudang'] ?>"
                                            data-bahan="<?= $opt['nama_barang'] ?>"
                                            <?= $selected ?>>
                                        <?= $opt['nama_gudang'] ?> - <?= $opt['nama_barang'] ?> (Stok: <?= $opt['stok_akhir'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div id="editInventoryInfo" class="inventory-info">
                        <strong>Informasi Inventory:</strong>
                        <div>Gudang: <span id="editGudangInfo"><?= $hpp_data['nama_gudang'] ?? '-' ?></span></div>
                        <div>Stok Tersedia: <span id="editStokInfo"><?= $hpp_data['stok_material'] ?? 0 ?></span></div>
                        <div>Harga per Unit: Rp <span id="editHargaInfo"><?= number_format($hpp_data['harga_per_meter'] ?? 0, 2, ',', '.') ?></span></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">No Urut</label>
                            <input type="text" name="no_urut" class="form-control" value="<?= $hpp_data['no_urut'] ?>" required>
                        </div>
                        <div class="col-md-8 mb-2">
                            <label class="form-label">Bahan</label>
                            <input type="text" name="bahan" class="form-control" id="editBahan" value="<?= $hpp_data['bahan'] ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">QTY</label>
                            <input type="number" name="qty" class="form-control" value="<?= $hpp_data['qty'] ?>" required min="1">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Barang Jadi</label>
                            <input type="number" name="barang_jadi" class="form-control" value="<?= $hpp_data['barang_jadi'] ?>" required min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Stok Order</label>
                            <input type="number" name="stok_order" class="form-control" value="<?= $hpp_data['stok_order'] ?? 0 ?>" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Efisiensi Consp</label>
                            <input type="number" step="0.01" name="efisiensi_consp" class="form-control" value="<?= $hpp_data['efisiensi_consp'] ?? 1.00 ?>" min="0.1" max="2.0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Efisiensi RAP</label>
                            <input type="number" step="0.01" name="efisiensi_rap" class="form-control" value="<?= $hpp_data['efisiensi_rap'] ?? 1.00 ?>" min="0.1" max="2.0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Stok Material</label>
                            <input type="number" name="stok_material" class="form-control" id="editStokMaterial" value="<?= $hpp_data['stok_material'] ?? 0 ?>" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">PO</label>
                            <input type="number" name="po" class="form-control" value="<?= $hpp_data['po'] ?? 0 ?>" min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Harga per Meter</label>
                            <input type="number" step="0.01" name="harga_per_meter" class="form-control" id="editHargaPerMeter" value="<?= $hpp_data['harga_per_meter'] ?? 0 ?>" min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Biaya Tenaga Kerja per Qty</label>
                            <input type="number" step="0.01" name="biaya_tenaga_kerja_per_qty" class="form-control" value="<?= $hpp_data['biaya_tenaga_kerja_per_qty'] ?? 0 ?>" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Listrik</label>
                            <input type="number" step="0.01" name="listrik" class="form-control" value="<?= $hpp_data['listrik'] ?? 0 ?>" min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Air</label>
                            <input type="number" step="0.01" name="air" class="form-control" value="<?= $hpp_data['air'] ?? 0 ?>" min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Overhead</label>
                            <input type="number" step="0.01" name="overhead" class="form-control" value="<?= $hpp_data['overhead'] ?? 0 ?>" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Profit (%)</label>
                            <input type="number" step="0.01" name="profit" class="form-control" value="<?= $hpp_data['profit'] ?? 30 ?>" min="0" max="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-primary-custom">Update Data</button>
                    <a href="?id=<?= $id_persiapan ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateInventoryInfo(type) {
    const selectElement = document.getElementById(type + 'Inventory');
    const infoElement = document.getElementById(type + 'InventoryInfo');
    const gudangElement = document.getElementById(type + 'GudangInfo');
    const stokElement = document.getElementById(type + 'StokInfo');
    const hargaElement = document.getElementById(type + 'HargaInfo');
    
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectedOption.value !== '') {
        const stok = selectedOption.getAttribute('data-stok');
        const harga = selectedOption.getAttribute('data-harga');
        const gudang = selectedOption.getAttribute('data-gudang');
        const bahan = selectedOption.getAttribute('data-bahan');
        
        gudangElement.textContent = gudang;
        stokElement.textContent = stok;
        hargaElement.textContent = parseFloat(harga).toLocaleString('id-ID', {minimumFractionDigits: 2});
        
        infoElement.style.display = 'block';
        
        // Auto-fill fields
        document.getElementById(type + 'Bahan').value = bahan;
        document.getElementById(type + 'StokMaterial').value = stok;
        document.getElementById(type + 'HargaPerMeter').value = harga;
    } else {
        infoElement.style.display = 'none';
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    
    searchInput.addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('.table-custom tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Auto show edit modal if editing
    <?php if ($hpp_id > 0 && $hpp_data): ?>
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
    <?php endif; ?>
});
</script>
</body>
</html>