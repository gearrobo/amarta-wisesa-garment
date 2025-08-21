<?php
// edit-persiapan.php
include "includes/header.php";
include "config/db.php";

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data persiapan berdasarkan ID
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
    die("Data persiapan tidak ditemukan!");
}

$message = '';
$messageType = '';

// Proses update data
if (isset($_POST['update'])) {
    // Ambil data dari form
    $nama_barang = $_POST['nama_barang'] ?? '';
    $jumlah = intval($_POST['jumlah'] ?? 0);
    $satuan = $_POST['satuan'] ?? '';
    $harga = floatval($_POST['harga'] ?? 0);
    $status = $_POST['status'] ?? '';

    // Fungsi upload file
    function uploadFile($field, $allowedTypes, $maxSize = 5000000) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $fileType = $_FILES[$field]['type'];
            $fileExtension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
            
            // Validasi tipe file berdasarkan ekstensi dan MIME type
            $valid = false;
            foreach ($allowedTypes as $type) {
                if (strpos($type, '/') !== false) {
                    // MIME type
                    if ($fileType === $type) {
                        $valid = true;
                        break;
                    }
                } else {
                    // Ekstensi file
                    if ($fileExtension === strtolower($type)) {
                        $valid = true;
                        break;
                    }
                }
            }
            
            if (!$valid) {
                return ['error' => 'Tipe file tidak valid untuk ' . $field];
            }
            
            if ($_FILES[$field]['size'] > $maxSize) {
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

    // Upload file pola (.hpg/.mrk)
    $polaUpload = uploadFile('pola', ['hpg', 'mrk', 'application/octet-stream'], 10000000);
    
    // Upload file marker (.hpg/.mrk)
    $markerUpload = uploadFile('marker', ['hpg', 'mrk', 'application/octet-stream'], 10000000);
    
    // Upload file SPK (.pdf/.png)
    $spkUpload = uploadFile('spk', ['pdf', 'png', 'image/png', 'application/pdf'], 10000000);

    // Cek error upload
    $uploadErrors = [];
    $uploads = ['pola' => $polaUpload, 'marker' => $markerUpload, 'spk' => $spkUpload];
    
    foreach ($uploads as $field => $result) {
        if (isset($result['error'])) {
            $uploadErrors[] = $result['error'];
        }
    }

    if (!empty($uploadErrors)) {
        $message = 'Error upload: ' . implode(', ', $uploadErrors);
        $messageType = 'danger';
    } else {
        // Build update query
        $updateFields = [
            'nama_barang' => $nama_barang,
            'jumlah' => $jumlah,
            'satuan' => $satuan,
            'harga' => $harga,
            'status' => $status
        ];

        // Tambahkan field upload jika ada file baru
        if ($polaUpload['filename']) {
            $updateFields['pola'] = $polaUpload['filename'];
        }
        if ($markerUpload['filename']) {
            $updateFields['marker'] = $markerUpload['filename'];
        }
        if ($spkUpload['filename']) {
            $updateFields['spk'] = $spkUpload['filename'];
        }

        // Build query
        $setParts = [];
        $params = [];
        $types = '';
        
        foreach ($updateFields as $field => $value) {
            $setParts[] = "$field = ?";
            $params[] = $value;
            $types .= 's';
        }
        
        $params[] = $id;
        $types .= 'i';

        $sql = "UPDATE persiapan SET " . implode(', ', $setParts) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $message = 'Data persiapan berhasil diupdate!';
                $messageType = 'success';
                
                // Redirect untuk refresh data
                header("Location: edit-persiapan.php?id=$id&success=1");
                exit();
            } else {
                $message = 'Error: Gagal update data - ' . $stmt->error;
                $messageType = 'danger';
            }
            $stmt->close();
        } else {
            $message = 'Error: Gagal prepare statement - ' . $conn->error;
            $messageType = 'danger';
        }
    }
}

// Refresh data setelah update
if (isset($_GET['success'])) {
    $sql = "SELECT p.*, s.sps_no 
            FROM persiapan p 
            LEFT JOIN sps s ON p.id_sps = s.id 
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $persiapan = $result->fetch_assoc();
}
?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Persiapan</h1>
                <a href="persiapan.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="persiapan.php">Persiapan</a></li>
                    <li class="breadcrumb-item active">Edit Persiapan</li>
                </ol>
            </nav>

            <!-- Alerts -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Form Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Data Persiapan</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">No SPS</label>
                                    <input type="text" class="form-control" value="<?= $persiapan['sps_no'] ?: '-' ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control" value="<?= $persiapan['nama_barang'] ?? '' ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah" class="form-control" value="<?= $persiapan['jumlah'] ?? 0 ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Satuan</label>
                                    <input type="text" name="satuan" class="form-control" value="<?= $persiapan['satuan'] ?? '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Harga</label>
                                    <input type="number" name="harga" class="form-control" step="0.01" value="<?= $persiapan['harga'] ?? 0 ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Draft" <?= ($persiapan['status'] ?? '') == 'Draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="Proses" <?= ($persiapan['status'] ?? '') == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                        <option value="Selesai" <?= ($persiapan['status'] ?? '') == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                        <option value="Dibatalkan" <?= ($persiapan['status'] ?? '') == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- File Uploads -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Upload Pola (.hpg/.mrk)</label>
                                    <input type="file" name="pola" class="form-control" accept=".hpg,.mrk">
                                    <?php if (!empty($persiapan['pola'])): ?>
                                        <small class="text-muted">
                                            File saat ini: <a href="uploads/persiapan/<?= $persiapan['pola'] ?>" target="_blank"><?= $persiapan['pola'] ?></a>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Upload Marker (.hpg/.mrk)</label>
                                    <input type="file" name="marker" class="form-control" accept=".hpg,.mrk">
                                    <?php if (!empty($persiapan['marker'])): ?>
                                        <small class="text-muted">
                                            File saat ini: <a href="uploads/persiapan/<?= $persiapan['marker'] ?>" target="_blank"><?= $persiapan['marker'] ?></a>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Upload SPK (.pdf/.png)</label>
                                    <input type="file" name="spk" class="form-control" accept=".pdf,.png">
                                    <?php if (!empty($persiapan['spk'])): ?>
                                        <small class="text-muted">
                                            File saat ini: <a href="uploads/persiapan/<?= $persiapan['spk'] ?>" target="_blank"><?= $persiapan['spk'] ?></a>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="update" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Data
                            </button>
                            <a href="persiapan.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validasi file upload
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const fileName = file.name.toLowerCase();
                    const fileSize = file.size;
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    
                    let allowedExtensions = [];
                    if (this.name === 'pola' || this.name === 'marker') {
                        allowedExtensions = ['.hpg', '.mrk'];
                    } else if (this.name === 'spk') {
                        allowedExtensions = ['.pdf', '.png'];
                    }
                    
                    const fileExtension = '.' + fileName.split('.').pop();
                    
                    if (!allowedExtensions.includes(fileExtension)) {
                        alert('File tidak valid! Ekstensi yang diizinkan: ' + allowedExtensions.join(', '));
                        this.value = '';
                        return;
                    }
                    
                    if (fileSize > maxSize) {
                        alert('File terlalu besar! Maksimal 10MB');
                        this.value = '';
                        return;
                    }
                }
            });
        });
    </script>

<?php include 'includes/footer.php'; ?>
