<?php
include 'includes/header.php';
include 'config/db.php';

// Ambil data dari persiapan + join ke sps untuk customer & item
$sql = "SELECT 
            p.id,
            s.customer AS nama_customer,
            s.item,
            p.jumlah,
            p.sp_srx AS no_spk
        FROM persiapan p
        JOIN sps s ON p.id_sps = s.id";
$result = $conn->query($sql);
?>

<div class="main-content">

    <div>
        <h1 class="h3 mb-4">Produksi</h1>
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Produksi</li>
            </ol>
        </nav>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-responsive">
            <table id="produksiTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nama Customer</th>
                        <th>Item</th>
                        <th>Jumlah</th>
                        <th>No SPK</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_customer']) ?></td>
                                <td><?= htmlspecialchars($row['item']) ?></td>
                                <td><?= htmlspecialchars($row['jumlah']) ?></td>
                                <td><?= htmlspecialchars($row['no_spk']) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                    <a href="detail-produksi.php?id=<?= $row['id'] ?>" class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-warning" onclick="editPersiapan(<?= $row['id'] ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deletePersiapan(<?= $row['id'] ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <a href="generate_summary.php?id=<?= $row['id'] ?>" class="btn btn-secondary" title="Print" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php include'includes/footer.php'; ?>