<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// db.php
include "includes/header.php";
include "config/db.php";

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses simpan SPS
$message = '';
$messageType = '';

if (isset($_POST['save'])) {
    // Validasi input required
    $required_fields = ['tanggal', 'sp_no', 'sps_no', 'customer', 'item', 'artikel', 'gender', 'age', 'qty'];
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
        $sp_no = $_POST['sp_no'];
        $sps_no = $_POST['sps_no'];
        $customer = $_POST['customer'];
        $item = $_POST['item'];
        $artikel = $_POST['artikel'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $qty = intval($_POST['qty']);
        $size = $_POST['size'] ?? '';
        $kirim = $_POST['kirim'] ?? null;
        $approval = $_POST['approval'] ?? '';
        $sp_srx = $_POST['sp_srx'] ?? '';

        // Upload gambar dengan validasi
        function uploadFile($field)
        {
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

                $targetDir = "uploads/";
                if (!is_dir($targetDir)) {
                    if (!mkdir($targetDir, 0777, true)) {
                        return ['error' => 'Gagal membuat direktori uploads'];
                    }
                }

                $filename = time() . "_" . uniqid() . "_" . basename($_FILES[$field]['name']);
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
                    (tanggal, sp_no, sps_no, customer, item, artikel, gender, age, qty, size,
                     sample_product, design, st_chart, material_sm, pola_sample, buat_sample,
                     kirim, approval, sp_srx)
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if ($stmt) {
            $stmt->bind_param(
                "ssssssssissssssssss",
                $tanggal,
                $sp_no,
                $sps_no,
                $customer,
                $item,
                $artikel,
                $gender,
                $age,
                $qty,
                $size,
                $uploadResults['sample_product']['filename'],
                $uploadResults['design']['filename'],
                $uploadResults['st_chart']['filename'],
                $uploadResults['material_sm']['filename'],
                $uploadResults['pola_sample']['filename'],
                $uploadResults['buat_sample']['filename'],
                $kirim,
                $approval,
                $sp_srx
            );

                if ($stmt->execute()) {
                    $message = 'Data berhasil disimpan!';
                    $messageType = 'success';

                    // Redirect untuk mencegah duplicate submission
                    header("Location: sps-sample.php?success=1");
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
    $message = 'Data berhasil disimpan!';
    $messageType = 'success';
}

// Fungsi untuk memperbaiki nama file sample_product yang tidak lengkap
function fixSampleProductFilenames($conn)
{
    $dir = "uploads/";

    // Ambil semua record yang sample_product hanya berisi angka (timestamp)
    $sql = "SELECT id, sample_product FROM sps WHERE sample_product REGEXP '^[0-9]+$'";
    $result = $conn->query($sql);

    $fixed_count = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $sample_product = $row['sample_product'];

            // Cari file di folder uploads yang diawali dengan sample_product
            $files = glob($dir . $sample_product . "*");

            if (count($files) > 0) {
                // Ambil nama file lengkap (tanpa path)
                $filename = basename($files[0]);

                // Update database dengan nama file lengkap
                $update_sql = "UPDATE sps SET sample_product = ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("si", $filename, $id);
                if ($stmt->execute()) {
                    $fixed_count++;
                }
                $stmt->close();
            }
        }
    }

    return $fixed_count;
}

// Jalankan perbaikan otomatis saat halaman dimuat
$fixed_files = fixSampleProductFilenames($conn);
if ($fixed_files > 0) {
    // $message = "Berhasil memperbaiki $fixed_files file sample product!";
    $messageType = "success";
}

// Ambil data SPS
$result = $conn->query("SELECT * FROM sps ORDER BY id DESC");

// Hitung jumlah pending approval
$pending_result = $conn->query("SELECT COUNT(*) as pending_count FROM sps WHERE approval = '' OR approval IS NULL");
$pending_count = $pending_result->fetch_assoc()['pending_count'];

// Hitung jumlah approved
$approved_result = $conn->query("SELECT COUNT(*) as approved_count FROM sps WHERE approval != '' AND approval IS NOT NULL");
$approved_count = $approved_result->fetch_assoc()['approved_count'];

// Hitung jumlah dikirim (yang sudah di-approve dan dikirim)
$kirim_result = $conn->query("SELECT COUNT(*) as kirim_count FROM sps WHERE approval ='Approved' ");
$kirim_count = $kirim_result->fetch_assoc()['kirim_count'];

// Ambil distinct values untuk dropdown gender dan age dari database
$gender_result = $conn->query("SHOW COLUMNS FROM sps WHERE Field = 'gender'");
$gender_row = $gender_result->fetch_assoc();
$gender_enum = $gender_row['Type'];
preg_match("/^enum\((.*)\)$/", $gender_enum, $matches);
$gender_options = array_map(function ($val) {
    return trim($val, "'");
}, explode(',', $matches[1]));

$age_result = $conn->query("SHOW COLUMNS FROM sps WHERE Field = 'age'");
$age_row = $age_result->fetch_assoc();
$age_enum = $age_row['Type'];
preg_match("/^enum\((.*)\)$/", $age_enum, $matches);
$age_options = array_map(function ($val) {
    return trim($val, "'");
}, explode(',', $matches[1]));
?>

<!-- Main Content -->
<main class="main-content">

    <div>
        <h1 class="h3 mb-4">Surat Perintah Sample</h1>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Surat Perintah Sample</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <h3><?php echo $result->num_rows; ?></h3>
                <p>Total SPS</p>
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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#spsModal">
            <i class="fas fa-plus"></i> Buat SPS Baru
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
            <table id="sppTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>SPS No</th>
                        <th>SP No</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Artikel</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Qty</th>
                        <th>Size</th>
                        <th>Tanggal Kirim</th>
                        <th>Approval</th>
                        <th>SP SRX</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><strong><?= $row['sps_no'] ?></strong></td>
                            <td><?= $row['sp_no'] ?></td>
                            <td><?= $row['customer'] ?></td>
                            <td><?= $row['item'] ?></td>
                            <td><?= $row['artikel'] ?></td>
                            <td><?= $row['gender'] ?></td>
                            <td><?= $row['age'] ?></td>
                            <td><?= number_format($row['qty']) ?></td>
                            <td><?= $row['size'] ?></td>
                            <td><?= $row['kirim'] ? date('d/m/Y', strtotime($row['kirim'])) : '-' ?></td>
                            <td>
                                <?php if ($row['approval']): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> <?= $row['approval'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['sp_srx'] ?: '-' ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-warning" onclick="editSPS(<?= $row['id'] ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteSPS(<?= $row['id'] ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-success" onclick="approveSPS(<?= $row['id'] ?>)" title="Approve">
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
<div class="modal fade" id="spsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Buat SPS Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">

                    <div class="col-md-4">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>No SP</label>
                        <input type="text" name="sp_no" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>No SPS</label>
                        <input type="text" name="sps_no" class="form-control" required>
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
                        <label>Gender</label>
                        <select name="gender" class="form-control" required>
                            <option value="">Pilih Gender</option>
                            <?php foreach ($gender_options as $option): ?>
                                <option value="<?php echo $option; ?>"><?php echo ucfirst($option); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Age</label>
                        <select name="age" class="form-control" required>
                            <option value="">Pilih Age</option>
                            <?php foreach ($age_options as $option): ?>
                                <option value="<?php echo $option; ?>"><?php echo ucfirst($option); ?></option>
                            <?php endforeach; ?>
                        </select>
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
                        <input type="file" name="sample_product" accept="image/*" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Design (.png)</label>
                        <input type="file" name="design" accept="image/*" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>ST Chart (.png)</label>
                        <input type="file" name="st_chart" accept="image/*" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Material SM (.png)</label>
                        <input type="file" name="material_sm" accept="image/*" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Pola Sample (.png)</label>
                        <input type="file" name="pola_sample" accept="image/*" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Buat Sample (.png)</label>
                        <input type="file" name="buat_sample" accept="image/*" class="form-control">
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
        $('#sppTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            order: [
                [0, 'asc']
            ]
        });
    });

    // JavaScript functions for action buttons
    function editSPS(id) {
        window.location.href = 'edit_sps.php?id=' + id;
    }

    function deleteSPS(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            window.location.href = 'delete_sps.php?id=' + id;
        }
    }

    function approveSPS(id) {
        if (confirm('Apakah Anda yakin ingin approve data ini?')) {
            window.location.href = 'approve_sps.php?id=' + id;
        }
    }

    function generateSPK(id) {
        window.open('generate_spk.php?id=' + id, '_blank');
    }
</script>


<?php include 'includes/footer.php';
