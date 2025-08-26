<?php
// Include header
include 'includes/header.php';
include 'config/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: data-karyawan.php");
    exit();
}

$id_karyawan = intval($_GET['id']); // Sanitize ID
$error = '';
$success = '';

// Get employee details
$sql = "SELECT k.*, j.nama_jabatan, d.nama_departemen 
        FROM karyawan k 
        LEFT JOIN jabatan j ON k.id_jabatan = j.id
        LEFT JOIN departemen d ON k.id_departemen = d.id
        WHERE k.id = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$karyawan = mysqli_fetch_assoc($result);

if (!$karyawan) {
    header("Location: data-karyawan.php");
    exit();
}

// Get jabatan list
$sql = "SELECT * FROM jabatan ORDER BY nama_jabatan";
$result = mysqli_query($conn, $sql);
$jabatanList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get departemen list
$sql = "SELECT * FROM departemen ORDER BY nama_departemen";
$result = mysqli_query($conn, $sql);
$departemenList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required fields
    $required_fields = ['nik', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
                       'alamat', 'no_telepon', 'email', 'type_karyawan', 'tanggal_masuk', 
                       'id_jabatan', 'id_departemen'];
    
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $error = "Mohon lengkapi semua field yang wajib diisi! Field yang kosong: " . implode(', ', $missing_fields);
    } else {
        // Sanitize input
        $nik = mysqli_real_escape_string($conn, $_POST['nik']);
        $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
        $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $type_karyawan = mysqli_real_escape_string($conn, $_POST['type_karyawan']);
        $tanggal_masuk = $_POST['tanggal_masuk'];
        $id_jabatan = intval($_POST['id_jabatan']);
        $id_departemen = intval($_POST['id_departemen']);

        // Update employee data
        $sql = "UPDATE karyawan SET 
                nik = ?, nama_lengkap = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, 
                alamat = ?, no_telepon = ?, email = ?, type_karyawan = ?, tanggal_masuk = ?, 
                id_jabatan = ?, id_departemen = ?
                WHERE id_karyawan = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssssiii", 
            $nik, $nama_lengkap, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, 
            $alamat, $no_telepon, $email, $type_karyawan, $tanggal_masuk, 
            $id_jabatan, $id_departemen, $id_karyawan
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Data karyawan berhasil diperbarui!";
            
            // Refresh data
            $sql = "SELECT k.*, j.nama_jabatan, d.nama_departemen 
                    FROM karyawan k 
                    LEFT JOIN jabatan j ON k.id_jabatan = j.id
                    LEFT JOIN departemen d ON k.id_departemen = d.id
                    WHERE k.id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $karyawan = mysqli_fetch_assoc($result);
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($conn);
        }
    }
}
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Edit Data Karyawan</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="data-karyawan.php">Data Karyawan</a></li>
                    <li class="breadcrumb-item active">Edit Karyawan</li>
                </ol>
            </nav>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Form Edit Karyawan</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nik" name="nik" 
                                           value="<?= htmlspecialchars($karyawan['nik']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                           value="<?= htmlspecialchars($karyawan['nama_lengkap']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="L" <?= $karyawan['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="P" <?= $karyawan['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" 
                                           value="<?= htmlspecialchars($karyawan['tempat_lahir']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" 
                                           value="<?= $karyawan['tanggal_lahir']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_telepon" class="form-label">No Telepon <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon" 
                                           value="<?= htmlspecialchars($karyawan['no_telepon']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($karyawan['email']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_karyawan" class="form-label">Type Karyawan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type_karyawan" name="type_karyawan" required>
                                        <option value="tetap" <?= $karyawan['type_karyawan'] == 'tetap' ? 'selected' : ''; ?>>Tetap</option>
                                        <option value="harian" <?= $karyawan['type_karyawan'] == 'harian' ? 'selected' : ''; ?>>Harian</option>
                                        <option value="borongan" <?= $karyawan['type_karyawan'] == 'borongan' ? 'selected' : ''; ?>>Borongan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_masuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" 
                                           value="<?= $karyawan['tanggal_masuk']; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="id_jabatan" name="id_jabatan" required>
                                        <option value="">Pilih Jabatan</option>
                                        <?php foreach ($jabatanList as $jabatan): ?>
                                            <option value="<?= $jabatan['id_jabatan']; ?>" 
                                                <?= $karyawan['id_jabatan'] == $jabatan['id_jabatan'] ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($jabatan['nama_jabatan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_departemen" class="form-label">Departemen <span class="text-danger">*</span></label>
                                    <select class="form-select" id="id_departemen" name="id_departemen" required>
                                        <option value="">Pilih Departemen</option>
                                        <?php foreach ($departemenList as $departemen): ?>
                                            <option value="<?= $departemen['id_departemen']; ?>" 
                                                <?= $karyawan['id_departemen'] == $departemen['id_departemen'] ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($departemen['nama_departemen']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($karyawan['alamat']); ?></textarea>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Data
                            </button>
                            <a href="detail-karyawan.php?id=<?= $id_karyawan; ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>