<?php
// include 'includes/header.php';
include 'config/db.php';

// Ambil id persiapan dari URL
$id_persiapan = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ---------------- GET DETAIL PERSIAPAN ----------------
$sql = "SELECT p.*, c.nama AS customer_nama, c.alamat AS customer_alamat, c.no_telp AS customer_no_telp
        FROM persiapan p
        LEFT JOIN customer c ON p.id_customer = c.id
        WHERE p.id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("SQL Error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id_persiapan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data_persiapan = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data_persiapan) {
    die("Data persiapan tidak ditemukan.");
}


// ---------------- GET TIMELINE ----------------
$sql_timeline = "SELECT * FROM timeline WHERE id_persiapan = ? ORDER BY tanggal";
$stmt_timeline = mysqli_prepare($conn, $sql_timeline);
mysqli_stmt_bind_param($stmt_timeline, "i", $id_persiapan);
mysqli_stmt_execute($stmt_timeline);
$result_timeline = mysqli_stmt_get_result($stmt_timeline);
$timeline = mysqli_fetch_all($result_timeline, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_timeline);

// ---------------- GET DATA GUDANG ----------------
$sql_gudang = "SELECT 
        hpp.bahan,
        hpp.qty,
        hpp.harga_per_meter,
        hpp.total_harga_bahan,
        ig.stok_akhir,
        ig.satuan,
        g.nama AS nama_gudang
    FROM hpp
    LEFT JOIN inventory_gudang ig ON hpp.id_inventory = ig.id_inventory
    LEFT JOIN gudang g ON ig.id_gudang = g.id
    WHERE hpp.id_persiapan = ?";
$stmt_gudang = mysqli_prepare($conn, $sql_gudang);
mysqli_stmt_bind_param($stmt_gudang, "i", $id_persiapan);
mysqli_stmt_execute($stmt_gudang);
$result_gudang = mysqli_stmt_get_result($stmt_gudang);
$gudang_items = mysqli_fetch_all($result_gudang, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_gudang);

?>

<div class="container mt-4">
    <h2>Detail Persiapan</h2>

    <!-- Informasi Persiapan -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title mb-0">Informasi Persiapan</h5>
        </div>
        <div class="card-body">
            <p><strong>Nomor Persiapan:</strong> <?= htmlspecialchars($persiapan['nomor']); ?></p>
            <p><strong>Tanggal:</strong> <?= htmlspecialchars($persiapan['tanggal']); ?></p>
            <p><strong>Customer:</strong> <?= htmlspecialchars($persiapan['customer']); ?></p>
            <p><strong>Item:</strong> <?= htmlspecialchars($persiapan['item']); ?></p>
            <p><strong>Qty Order:</strong> <?= htmlspecialchars($persiapan['qty']); ?></p>
            <p><strong>Keterangan:</strong> <?= htmlspecialchars($persiapan['keterangan']); ?></p>
        </div>
    </div>

    <!-- Detail Produksi & Analisis Biaya -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title mb-0">Detail Produksi & Analisis Biaya</h5>
        </div>
        <div class="card-body">
            <p><strong>Total Biaya Produksi:</strong> Rp <?= number_format($persiapan['total_biaya'], 0, ',', '.'); ?></p>
            <p><strong>Harga Jual:</strong> Rp <?= number_format($persiapan['harga_jual'], 0, ',', '.'); ?></p>
            <p><strong>Estimasi Laba:</strong> Rp <?= number_format($persiapan['estimasi_laba'], 0, ',', '.'); ?></p>
        </div>
    </div>

    <!-- Data Gudang / Pemakaian Bahan -->
    <?php if (!empty($gudang_items)): ?>
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title mb-0">Data Gudang / Pemakaian Bahan</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Gudang</th>
                        <th>Bahan</th>
                        <th>Qty Digunakan</th>
                        <th>Stok Akhir</th>
                        <th>Harga per Meter</th>
                        <th>Total Harga Bahan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gudang_items as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= htmlspecialchars($item['nama_gudang'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($item['bahan']); ?></td>
                        <td><?= number_format($item['qty']); ?> <?= $item['satuan']; ?></td>
                        <td><?= number_format($item['stok_akhir']); ?> <?= $item['satuan']; ?></td>
                        <td>Rp <?= number_format($item['harga_per_meter'], 0, ',', '.'); ?></td>
                        <td>Rp <?= number_format($item['total_harga_bahan'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Files Section -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title mb-0">Files</h5>
        </div>
        <div class="card-body">
            <?php if (empty($files)): ?>
                <p>Tidak ada file diunggah.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($files as $file): ?>
                        <li><a href="uploads/<?= htmlspecialchars($file['filename']); ?>" target="_blank"><?= htmlspecialchars($file['filename']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Timeline Section -->
    <div class="card mt-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Timeline Produksi</h5>
        </div>
        <div class="card-body">
            <?php if (empty($timeline)): ?>
                <p>Belum ada timeline.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($timeline as $event): ?>
                        <li><?= htmlspecialchars($event['tanggal']); ?> - <?= htmlspecialchars($event['deskripsi']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
