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
    $required_fields = ['tanggal', 'persiapan_no', 'customer', 'item', 'artikel', 'qty'];
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
        $persiapan_no = $_POST['persiapan_no'];
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
            $sql = "INSERT INTO persiapan 
                    (tanggal, persiapan_no, customer, item, artikel, qty, size, 
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

// Ambil data persiapan
// $result = $conn->query("SELECT * FROM persiapan ORDER BY id DESC");

// // Hitung jumlah pending approval
// $pending_result = $conn->query("SELECT COUNT(*) as pending_count FROM persiapan WHERE approval = '' OR approval IS NULL");
// $pending_count = $pending_result->fetch_assoc()['pending_count'];

// // Hitung jumlah approved
// $approved_result = $conn->query("SELECT COUNT(*) as approved_count FROM persiapan WHERE approval != '' AND approval IS NOT NULL");
// $approved_count = $approved_result->fetch_assoc()['approved_count'];

// // Hitung jumlah dikirim
// $kirim_result = $conn->query("SELECT COUNT(*) as kirim_count FROM persiapan WHERE approval ='Approved'");
// $kirim_count = $kirim_result->fetch_assoc()['kirim_count'];
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
                    <h3><?php echo $result->num_rows; ?></h3>
                    <p>Total Persiapan</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?php echo $pending_count; ?></h3>
                    <p>Menunggu Approval</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?php echo $approved_count; ?></h3>
                    <p>Disetujui</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?php echo $kirim_count; ?></h3>
                    <p>Dikirim</p>
                </div>
            </div>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#persiapanModal">
                <i class="fas fa-plus"></i> Buat Persiapan Baru
            </button>
        </div><br>

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
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>No Persiapan</th>
                            <th>Customer</th>
                            <th>Item</th>
                            <th>Artikel</th>
                            <th>Qty</th>
                            <th>Size</th>
                            <th>Kirim</th>
                            <th>Approval</th>
                            <th>SP SRX</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><strong><?= $row['persiapan_no'] ?></strong></td>
                            <td><?= $row['customer'] ?></td>
                            <td><?= $row['item'] ?></td>
                            <td><?= $row['artikel'] ?></td>
                            <td><?= number_format($row['qty']) ?></td>
                            <td><?= $row['size'] ?></td>
                            <td><?= $row['kirim'] ? date('d/m/Y', strtotime($row['kirim'])) : '-' ?></td>
                            <td>
                                <?php if($row['approval']): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> <?= $row['approval'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['sp_srx'] ?: '-' ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-warning" onclick="editPersiapan(<?= $row['id'] ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deletePersiapan(<?= $row['id'] ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-success" onclick="approvePersiapan(<?= $row['id'] ?>)" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-info" onclick="generateSPK(<?= $row['id'] ?>)" title="Generate SPK">
                                        <i class="fas fa-print"></i>
                                    </button>
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
        <div class="col-md-4">
            <label>No Persiapan</label>
            <input type="text" name="persiapan_no" class="form-control" required>
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
<script>
// JavaScript functions for action buttons
function editPersiapan(id) {
    window.location.href = 'edit_persiapan.php?id=' + id;
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
