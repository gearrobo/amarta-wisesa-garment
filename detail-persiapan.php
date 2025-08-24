<?php
include 'includes/header.php';
include 'config/db.php';

// Ambil id persiapan dari URL
$id_persiapan = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_persiapan == 0) {
    die("ID Persiapan tidak valid.");
}

// ---------------- GET DETAIL PERSIAPAN ----------------
$sql = "SELECT p.*, s.sps_no, s.customer, s.item, s.artikel 
        FROM persiapan p 
        LEFT JOIN sps s ON p.id_sps = s.id 
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

if ($stmt_timeline) {
    mysqli_stmt_bind_param($stmt_timeline, "i", $id_persiapan);
    mysqli_stmt_execute($stmt_timeline);
    $result_timeline = mysqli_stmt_get_result($stmt_timeline);
    $timeline = $result_timeline ? mysqli_fetch_all($result_timeline, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt_timeline);
} else {
    $timeline = [];
    error_log("Error preparing timeline query: " . mysqli_error($conn));
}

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
    LEFT JOIN inventory_gudang ig ON hpp.id_inventory = ig.id
    LEFT JOIN gudang g ON ig.id_gudang = g.id
    WHERE hpp.id_persiapan = ?";
    
$stmt_gudang = mysqli_prepare($conn, $sql_gudang);

if ($stmt_gudang) {
    mysqli_stmt_bind_param($stmt_gudang, "i", $id_persiapan);
    mysqli_stmt_execute($stmt_gudang);
    $result_gudang = mysqli_stmt_get_result($stmt_gudang);
    $gudang_items = $result_gudang ? mysqli_fetch_all($result_gudang, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt_gudang);
} else {
    $gudang_items = [];
    error_log("Error preparing gudang query: " . mysqli_error($conn));
}

// ---------------- GET FILES ----------------
$sql_files = "SELECT * FROM files WHERE id_persiapan = ?";
$stmt_files = mysqli_prepare($conn, $sql_files);

if ($stmt_files) {
    mysqli_stmt_bind_param($stmt_files, "i", $id_persiapan);
    mysqli_stmt_execute($stmt_files);
    $result_files = mysqli_stmt_get_result($stmt_files);
    $files = $result_files ? mysqli_fetch_all($result_files, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt_files);
} else {
    $files = [];
    error_log("Error preparing files query: " . mysqli_error($conn));
}

?>


<div class="main-content">
    <div class="">
        <h2>Detail Persiapan</h2>
        <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="persiapan.php">Persiapan</a></li>
                    <li class="breadcrumb-item active">Data Persiapan</li>
                </ol>
            </nav>
    </div>

    <!-- Informasi Persiapan -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Informasi Persiapan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nomor SPP:</strong> <?= htmlspecialchars($data_persiapan['spp_no'] ?? '-'); ?></p>
                    <p><strong>Nomor SPS:</strong> <?= htmlspecialchars($data_persiapan['sps_no'] ?? '-'); ?></p>
                    <p><strong>Customer:</strong> <?= htmlspecialchars($data_persiapan['customer'] ?? '-'); ?></p>
                    <p><strong>Item:</strong> <?= htmlspecialchars($data_persiapan['item'] ?? '-'); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Artikel:</strong> <?= htmlspecialchars($data_persiapan['artikel'] ?? '-'); ?></p>
                    <p><strong>Nama Barang:</strong> <?= htmlspecialchars($data_persiapan['nama_barang'] ?? '-'); ?></p>
                    <p><strong>Jumlah:</strong> <?= htmlspecialchars($data_persiapan['jumlah'] ?? '0'); ?> <?= htmlspecialchars($data_persiapan['satuan'] ?? ''); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge 
                            <?= ($data_persiapan['status'] ?? '') == 'selesai' ? 'bg-success' : 
                               (($data_persiapan['status'] ?? '') == 'proses' ? 'bg-warning' : 
                               (($data_persiapan['status'] ?? '') == 'batal' ? 'bg-danger' : 'bg-secondary')) ?>">
                            <?= ucfirst($data_persiapan['status'] ?? 'unknown'); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Gudang / Pemakaian Bahan -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0">Data Gudang / Pemakaian Bahan</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($gudang_items)): ?>
            <div class="table-responsive">
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gudang_items as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($item['nama_gudang'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($item['bahan'] ?? '-'); ?></td>
                            <td><?= number_format($item['qty'] ?? 0); ?> <?= htmlspecialchars($item['satuan'] ?? ''); ?></td>
                            <td><?= number_format($item['stok_akhir'] ?? 0); ?> <?= htmlspecialchars($item['satuan'] ?? ''); ?></td>
                            <td>Rp <?= number_format($item['harga_per_meter'] ?? 0, 0, ',', '.'); ?></td>
                            <td>Rp <?= number_format($item['total_harga_bahan'] ?? 0, 0, ',', '.'); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="data-hpp.php?id=<?= $id_persiapan; ?>" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>Tidak ada data gudang untuk persiapan ini.
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Files Section -->
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h5 class="card-title mb-0">Files</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($files)): ?>
                <div class="list-group">
                    <?php foreach ($files as $file): ?>
                        <a href="uploads/<?= htmlspecialchars($file['filename']); ?>" target="_blank" class="list-group-item list-group-item-action">
                            <i class="fas fa-file me-2"></i> <?= htmlspecialchars($file['filename']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>Tidak ada file untuk persiapan ini.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Timeline Section -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0">Timeline Produksi</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($timeline)): ?>
                <div class="timeline">
                    <?php foreach ($timeline as $event): ?>
                        <div class="timeline-item">
                            <div class="timeline-date">
                                <i class="fas fa-calendar me-1"></i> <?= date('d M Y', strtotime($event['tanggal'])); ?>
                            </div>
                            <div class="timeline-content">
                                <?= htmlspecialchars($event['deskripsi']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>Belum ada timeline untuk persiapan ini.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php';