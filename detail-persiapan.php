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

$id_sps_produksi = $data_persiapan['id_sps'];
if (!$data_persiapan) {
    die("Data persiapan tidak ditemukan.");
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

// ---------------- GET PRODUKSI ----------------
// Query yang sesuai dengan struktur tabel produksi (tanpa id_persiapan)
$sql_produksi = "SELECT p.*, s.sps_no, s.customer 
        FROM produksi p
        LEFT JOIN sps s ON p.id_sps = s.id
        WHERE p.id_sps = ?
        ORDER BY p.id DESC";

$stmt_produksi = $conn->prepare($sql_produksi);

if ($stmt_produksi) {
    $stmt_produksi->bind_param("i", $id_sps_produksi);
    $stmt_produksi->execute();
    $produksi = $stmt_produksi->get_result();
} else {
    die("Error dalam query produksi: " . $conn->error);
}

// Handle actions for produksi
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $conn->prepare("UPDATE produksi SET status='selesai' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: detail-persiapan.php?id=" . $id_persiapan);
    exit();
}

if (isset($_GET['delete_produksi'])) {
    $id = intval($_GET['delete_produksi']);
    $conn->query("DELETE FROM produksi WHERE id=$id");
    header("Location: detail-persiapan.php?id=" . $id_persiapan);
    exit();
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
            <!-- Tombol Perhitungan HPP -->
            <div class="mb-3">
                <a href="data-hpp.php?id=<?= $id_persiapan; ?>" class="btn btn-primary">
                    <i class="fas fa-calculator me-2"></i>Perhitungan HPP
                </a>
            </div>
            
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
                        <?php if (!empty($gudang_items)): ?>
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
                                    <button class="btn btn-sm btn-secondary">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data gudang</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Langkah Kerja Section -->
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h5 class="card-title mb-0">Langkah Kerja</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="produksi.php?id_sps=<?= $id_sps_produksi; ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Langkah Kerja
                </a>
                <a href="jumlah_pekerja.php?id_sps=<?= $id_sps_produksi; ?>" class="btn btn-primary">
                    <i class="fas fa-calculator me-2"></i>Hitung Jumlah Pekerja
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
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
                        <?php if ($produksi && $produksi->num_rows > 0): ?>
                            <?php $no = 1; while($row = $produksi->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['sps_no']) ?></td>
                                <td><?= htmlspecialchars($row['customer']) ?></td>
                                <td><?= htmlspecialchars($data_persiapan['spp_no'] . ' | ' . $data_persiapan['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($row['kerjaan']) ?></td>
                                <td><?= htmlspecialchars($row['target']) ?></td>
                                <td><?= htmlspecialchars($row['hasil']) ?></td>
                                <td><?= htmlspecialchars($row['pekerja']) ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $row['status'] == 'selesai' ? 'bg-success' : 
                                           ($row['status'] == 'proses' ? 'bg-warning' : 'bg-secondary') ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['qc']) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <?php if($row['status'] != 'selesai'): ?>
                                            <a href="?approve=<?= $row['id'] ?>&id=<?= $id_persiapan ?>" class="btn btn-success btn-sm"
                                                onclick="return confirm('Apakah anda yakin ingin menyelesaikan proses ini?')"
                                                title="Approve - Tandai Selesai">
                                                <i class="fas fa-check-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="produksi.php?edit=<?= $row['id'] ?>&id_sps=<?= $id_sps_produksi ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete_produksi=<?= $row['id'] ?>&id=<?= $id_persiapan ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center">Tidak ada data produksi</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Timeline Section -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0">Timeline Produksi</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>Belum ada timeline untuk persiapan ini.
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>