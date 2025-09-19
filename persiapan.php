<?php
// persiapan.php
include "includes/header.php";
include "config/db.php";

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses simpan persiapan
$message = '';
$messageType = '';

if (isset($_POST['save'])) {
    // Validasi input required
    $required_fields = ['tanggal', 'customer', 'item', 'artikel', 'qty'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $message = 'Error: Field berikut wajib diisi: ' . implode(', ', $missing_fields);
        $messageType = 'danger';
    } else {
        // Ambil data dengan aman
        $tanggal = $_POST['tanggal'];
        $customer = $_POST['customer'];
        $item = $_POST['item'];
        $artikel = $_POST['artikel'];
        $qty = intval($_POST['qty']);
        $size = $_POST['size'] ?? '';
        $kirim = $_POST['kirim'] ?? null;
        $approval = $_POST['approval'] ?? '';
        $sp_srx = $_POST['sp_srx'] ?? '';

        // Upload gambar dengan validasi
        function uploadFile($field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
                // Validasi file
                $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
                $fileType = $_FILES[$field]['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    return ['error' => 'Tipe file tidak valid untuk ' . $field];
                }
                
                // Validasi ukuran (max 5MB)
                if ($_FILES[$field]['size'] > 5000000) {
                    return ['error' => 'File terlalu besar untuk ' . $field];
                }
                
                $targetDir = "uploads/persiapan/";
                if (!is_dir($targetDir)) {
                    if (!mkdir($targetDir, 0777, true)) {
                        return ['error' => 'Gagal membuat direktori uploads'];
                    }
                }
                
                $filename = time() . "_" . basename($_FILES[$field]['name']);
                $targetFile = $targetDir . $filename;
                
                if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetFile)) {
                    return ['filename' => $filename];
                } else {
                    return ['error' => 'Gagal upload file ' . $field];
                }
            }
            return ['filename' => null];
        }

        // Upload semua file
        $uploadResults = [
            'sample_product' => uploadFile("sample_product"),
            'design' => uploadFile("design"),
            'st_chart' => uploadFile("st_chart"),
            'material_sm' => uploadFile("material_sm"),
            'pola_sample' => uploadFile("pola_sample"),
            'buat_sample' => uploadFile("buat_sample")
        ];

        // Cek error upload
        $uploadErrors = [];
        foreach ($uploadResults as $field => $result) {
            if (isset($result['error'])) {
                $uploadErrors[] = $result['error'];
            }
        }

        if (!empty($uploadErrors)) {
            $message = 'Error upload: ' . implode(', ', $uploadErrors);
            $messageType = 'danger';
        } else {
            // Gunakan prepared statement untuk keamanan
            $sql = "INSERT INTO sps 
                    (tanggal, sps_no, customer, item, artikel, qty, size, 
                     sample_product, design, st_chart, material_sm, pola_sample, buat_sample, 
                     kirim, approval, sp_srx)
                    VALUES 
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sssssisissssssss", 
                    $tanggal, $persiapan_no, $customer, $item, $artikel, $qty, $size,
                    $uploadResults['sample_product']['filename'],
                    $uploadResults['design']['filename'],
                    $uploadResults['st_chart']['filename'],
                    $uploadResults['material_sm']['filename'],
                    $uploadResults['pola_sample']['filename'],
                    $uploadResults['buat_sample']['filename'],
                    $kirim, $approval, $sp_srx
                );
                
                if ($stmt->execute()) {
                    $message = 'Data persiapan berhasil disimpan!';
                    $messageType = 'success';
                    
                    // Redirect untuk mencegah duplicate submission
                    header("Location: persiapan.php?success=1");
                    exit();
                } else {
                    $message = 'Error: Gagal menyimpan data - ' . $stmt->error;
                    $messageType = 'danger';
                }
                $stmt->close();
            } else {
                $message = 'Error: Gagal prepare statement - ' . $conn->error;
                $messageType = 'danger';
            }
        }
    }
}

// Cek parameter success dari redirect
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = 'Data persiapan berhasil disimpan!';
    $messageType = 'success';
}

// Ambil data persiapan dari tabel persiapan dengan join ke tabel sps untuk mendapatkan No SPS
$result = $conn->query("SELECT p.*, s.sps_no 
                        FROM persiapan p 
                        LEFT JOIN sps s ON p.id_sps = s.id 
                        ORDER BY p.id DESC");

// Hitung jumlah total persiapan
$total_result = $conn->query("SELECT COUNT(*) as total_count FROM persiapan");
$total_count = $total_result ? $total_result->fetch_assoc()['total_count'] : 0;

// Hitung jumlah persiapan yang sudah memiliki harga
$harga_result = $conn->query("SELECT COUNT(*) as harga_count FROM persiapan WHERE harga IS NOT NULL AND harga > 0");
$harga_count = $harga_result ? $harga_result->fetch_assoc()['harga_count'] : 0;

// Hitung jumlah persiapan yang belum memiliki harga
$belum_harga_result = $conn->query("SELECT COUNT(*) as belum_harga_count FROM persiapan WHERE harga IS NULL OR harga = 0");
$belum_harga_count = $belum_harga_result ? $belum_harga_result->fetch_assoc()['belum_harga_count'] : 0;
?>

    <!-- Main Content -->
    <main class="main-content">
       
            <div>
                <h1 class="h3 mb-4">Persiapan Produksi</h1>
                
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Persiapan Produksi</li>
                    </ol>
                </nav>
            </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?php echo $total_count; ?></h3>
                    <p>Total Persiapan</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?php echo $harga_count; ?></h3>
                    <p>Sudah Ada Harga</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?php echo $belum_harga_count; ?></h3>
                    <p>Belum Ada Harga</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?php echo $total_count; ?></h3>
                    <p>Total Item</p>
                </div>
            </div>
        </div>    

        <!-- Alerts -->
        <?php
        if (isset($_GET['updated'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Data berhasil diupdate!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
        if (isset($_GET['deleted'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Data berhasil dihapus!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
        if (isset($_GET['approved'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Data berhasil diapprove!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
        ?>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-responsive">
                <table id="persiapanTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No SPS</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['sps_no'] ?: '-' ?></td>
                            <td><?= $row['nama_barang'] ?: '-' ?></td>
                            <td><?= number_format($row['jumlah'] ?? 0) ?></td>
                            <td><?= $row['satuan'] ?: '-' ?></td>
                            <td>Rp <?= number_format($row['harga'] ?? 0, 0, ',', '.') ?></td>
                            <td>
                                <?php
                                $status = $row['status'] ?? '';
                                $statusClass = '';
                                switch(strtolower($status)) {
                                    case 'pending':
                                        $statusClass = 'badge bg-warning text-dark';
                                        break;
                                    case 'proses':
                                        $statusClass = 'badge bg-primary';
                                        break;
                                    case 'selesai':
                                        $statusClass = 'badge bg-success';
                                        break;
                                    case 'batal':
                                        $statusClass = 'badge bg-danger';
                                        break;
                                    default:
                                        $statusClass = 'badge bg-light text-dark';
                                        break;
                                }
                                ?>
                                <span class="<?= $statusClass ?>"><?= $status ?: '-' ?></span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <?php if (strtolower($status) === 'selesai'): ?>
                                        <a href="detail-persiapan.php?id=<?= $row['id'] ?>" class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" title="Detail Persiapan Enable saat Status Selesai" disabled>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-warning" onclick="editPersiapan(<?= $row['id'] ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <!-- <button class="btn btn-danger" onclick="deletePersiapan(<?= $row['id'] ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button> -->
                                    <?php if (strtolower($status) === 'selesai'): ?>
                                        <a href="generate_spp.php?id=<?= $row['id'] ?>" class="btn btn-secondary" title="Print" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" title="SPP hanya bisa dicetak saat status Selesai" disabled>
                                            <i class="fas fa-print"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

<!-- Modal Form Input -->
<div class="modal fade" id="persiapanModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">Buat Persiapan Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">

        <div class="col-md-4">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        </div>
        <div class="col-md-4">
            <label>Customer</label>
            <input type="text" name="customer" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Item</label>
            <input type="text" name="item" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Artikel</label>
            <input type="text" name="artikel" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Qty</label>
            <input type="number" name="qty" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Size</label>
            <input type="text" name="size" class="form-control">
        </div>
        <div class="col-md-4">
            <label>Kirim</label>
            <input type="date" name="kirim" class="form-control">
        </div>
        <div class="col-md-4">
            <label>Approval</label>
            <input type="text" name="approval" class="form-control">
        </div>
        <div class="col-md-4">
            <label>SP SRX</label>
            <input type="text" name="sp_srx" class="form-control">
        </div>

        <div class="col-md-6">
            <label>Sample Product (.png)</label>
            <input type="file" name="sample_product" accept="image/png" class="form-control">
        </div>
        <div class="col-md-6">
            <label>Design (.png)</label>
            <input type="file" name="design" accept="image/png" class="form-control">
        </div>
        <div class="col-md-6">
            <label>ST Chart (.png)</label>
            <input type="file" name="st_chart" accept="image/png" class="form-control">
        </div>
        <div class="col-md-6">
            <label>Material SM (.png)</label>
            <input type="file" name="material_sm" accept="image/png" class="form-control">
        </div>
        <div class="col-md-6">
            <label>Pola Sample (.png)</label>
            <input type="file" name="pola_sample" accept="image/png" class="form-control">
        </div>
        <div class="col-md-6">
            <label>Buat Sample (.png)</label>
            <input type="file" name="buat_sample" accept="image/png" class="form-control">
        </div>

      </div>
      <div class="modal-footer">
        <button type="submit" name="save" class="btn btn-success">Simpan</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
<script>
    $(document).ready(function() {
            $('#persiapanTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                order: [[0, 'asc']]
            });
        });
// JavaScript functions for action buttons
function detailPersiapan(id) {
    window.location.href = 'detail-persiapan.php?id=' + id;
}

function editPersiapan(id) {
    window.location.href = 'edit-persiapan.php?id=' + id;
}

function deletePersiapan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = 'delete_persiapan.php?id=' + id;
    }
}

function approvePersiapan(id) {
    if (confirm('Apakah Anda yakin ingin approve data ini?')) {
        window.location.href = 'approve_persiapan.php?id=' + id;
    }
}

function generateSPK(id) {
    window.open('generate_spk.php?id=' + id, '_blank');
}
</script>

<?php include 'includes/footer.php'; ?>
