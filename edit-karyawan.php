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
        LEFT JOIN jabatan j ON k.id_jabatan = j.id_jabatan 
        LEFT JOIN departemen d ON k.id_departemen = d.id_departemen 
        WHERE k.id_karyawan = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$karyawan = mysqli_fetch_assoc($result);

if (!$karyawan) {
    header("Location: data-karyawan.php");
    exit();
}

// Get type-specific data
if ($karyawan['type_karyawan'] == 'tetap') {
    $sql = "SELECT * FROM karyawan_tetap WHERE id_karyawan = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $karyawan_tetap = mysqli_fetch_assoc($result);
    $karyawan_harian_borongan = null;
} elseif ($karyawan['type_karyawan'] == 'harian' || $karyawan['type_karyawan'] == 'borongan') {
    // echo "karyawan harian";
    $sql = "SELECT * FROM karyawan_harian_borongan WHERE id_karyawan = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $karyawan_harian_borongan = mysqli_fetch_assoc($result);
    $karyawan_tetap = null;
}

// // Get dropdown data
$jabatan_list = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY nama_jabatan");
$departemen_list = mysqli_query($conn, "SELECT * FROM departemen ORDER BY nama_departemen");

// // Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $id_jabatan = intval($_POST['id_jabatan']);
    $id_departemen = intval($_POST['id_departemen']);
    $tanggal_masuk = mysqli_real_escape_string($conn, $_POST['tanggal_masuk']);
    $type_karyawan = mysqli_real_escape_string($conn, $_POST['type_karyawan']);
    echo $type_karyawan;
    
//     // Update main karyawan data
        $sql = "UPDATE karyawan SET 
        nik = ?, nama_lengkap = ?, jenis_kelamin = ?, tempat_lahir = ?, 
        tanggal_lahir = ?, alamat = ?, no_telepon = ?, email = ?, 
        id_jabatan = ?, id_departemen = ?, tanggal_masuk = ?, type_karyawan = ?
        WHERE id_karyawan = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssissii", 
            $nik, $nama_lengkap, $jenis_kelamin, $tempat_lahir, $tanggal_lahir,
            $alamat, $no_telepon, $email, $id_jabatan, $id_departemen, 
            $tanggal_masuk, $type_karyawan, $id_karyawan
        );
    
    if (mysqli_stmt_execute($stmt)) {
        // Update type-specific data
        if ($type_karyawan == 'tetap') {
            $npwp = mysqli_real_escape_string($conn, $_POST['npwp'] ?? '');
            $bpjs_ketenagakerjaan = mysqli_real_escape_string($conn, $_POST['bpjs_ketenagakerjaan'] ?? '');
            $bpjs_kesehatan = mysqli_real_escape_string($conn, $_POST['bpjs_kesehatan'] ?? '');
            $gaji_pokok = floatval($_POST['gaji_pokok'] ?? 0);
            $tunjangan_jabatan = floatval($_POST['tunjangan_jabatan'] ?? 0);
            $tunjangan_transport = floatval($_POST['tunjangan_transport'] ?? 0);
            $tunjangan_makan = floatval($_POST['tunjangan_makan'] ?? 0);
            $rekening_bank = mysqli_real_escape_string($conn, $_POST['rekening_bank'] ?? '');
            $nama_bank = mysqli_real_escape_string($conn, $_POST['nama_bank'] ?? '');
            
            if (isset($karyawan_tetap)) {
                // Update existing
                $sql = "UPDATE karyawan_tetap SET 
                        npwp = ?, bpjs_ketenagakerjaan = ?, bpjs_kesehatan = ?, 
                        gaji_pokok = ?, tunjangan_jabatan = ?, tunjangan_transport = ?, 
                        tunjangan_makan = ?, rekening_bank = ?, nama_bank = ?
                        WHERE id_karyawan = ?";
                
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssddddssi", 
                    $npwp, $bpjs_ketenagakerjaan, $bpjs_kesehatan, $gaji_pokok, 
                    $tunjangan_jabatan, $tunjangan_transport, $tunjangan_makan, 
                    $rekening_bank, $nama_bank, $id_karyawan
                );
            } else {
                // Insert new
                $sql = "INSERT INTO karyawan_tetap (id_karyawan, npwp, bpjs_ketenagakerjaan, bpjs_kesehatan, 
                        gaji_pokok, tunjangan_jabatan, tunjangan_transport, tunjangan_makan, 
                        rekening_bank, nama_bank) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "issddddssi", 
                    $id_karyawan, $npwp, $bpjs_ketenagakerjaan, $bpjs_kesehatan, $gaji_pokok, 
                    $tunjangan_jabatan, $tunjangan_transport, $tunjangan_makan, $rekening_bank, $nama_bank
                );
            }
            mysqli_stmt_execute($stmt);
            
            // Delete non-tetap data if exists
            mysqli_query($conn, "DELETE FROM karyawan_harian_borongan WHERE id_karyawan = $id_karyawan");
            
        } elseif ($type_karyawan == 'harian' || $type_karyawan == 'borongan'){ 
            $upah_per_hari = floatval($_POST['upah_per_hari'] ?? 0);
            $upah_per_jam = floatval($_POST['upah_per_jam'] ?? 0);
            $upah_borongan = floatval($_POST['upah_borongan'] ?? 0);
            $metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran'] ?? '');
            $rekening_bank = mysqli_real_escape_string($conn, $_POST['rekening_bank'] ?? '');
            $nama_bank = mysqli_real_escape_string($conn, $_POST['nama_bank'] ?? '');
            
            if (isset($karyawan_harian_borongan)) {
                // Update existing
                $sql = "UPDATE karyawan_harian_borongan SET 
                        upah_per_hari = ?, upah_per_jam = ?, upah_borongan = ?, 
                        metode_pembayaran = ?, rekening_bank = ?, nama_bank = ?
                        WHERE id_karyawan = ?";
                
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "dddssi", 
                    $upah_per_hari, $upah_per_jam, $upah_borongan, 
                    $metode_pembayaran, $rekening_bank, $nama_bank, $id_karyawan
                );
            } else {
                // Insert new
                $sql = "INSERT INTO karyawan_harian_borongan (id_karyawan, upah_per_hari, upah_per_jam, upah_borongan, 
                        metode_pembayaran, rekening_bank, nama_bank) VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ddddssi", 
                    $id_karyawan, $upah_per_hari, $upah_per_jam, $upah_borongan, 
                    $metode_pembayaran, $rekening_bank, $nama_bank
                );
            }
            mysqli_stmt_execute($stmt);
            
            // Delete tetap data if exists
            mysqli_query($conn, "DELETE FROM karyawan_tetap WHERE id_karyawan = $id_karyawan");
        }
        
        $success = "Data karyawan berhasil diperbarui!";
        
        // Refresh data
        $sql = "SELECT k.*, j.nama_jabatan, d.nama_departemen 
                FROM karyawan k 
                LEFT JOIN jabatan j ON k.id_jabatan = j.id_jabatan 
                LEFT JOIN departemen d ON k.id_departemen = d.id_departemen 
                WHERE k.id_karyawan = ?";
                
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $karyawan = mysqli_fetch_assoc($result);
        
        // Refresh type-specific data
        if ($type_karyawan == 'tetap') {
            $sql = "SELECT * FROM karyawan_tetap WHERE id_karyawan = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $karyawan_tetap = mysqli_fetch_assoc($result);
        } elseif ($type_karyawan == 'harian' || $type_karyawan == 'borongan') {
            $sql = "SELECT * FROM karyawan_harian_borongan WHERE id_karyawan = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $karyawan_harian_borongan = mysqli_fetch_assoc($result);
        }
        
    } else {
        $error = "Gagal memperbarui data: " . mysqli_error($conn);
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
                                        <option value="L" <?= $karyawan['jenis_kelamin'] == 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="P" <?= $karyawan['jenis_kelamin'] == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
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
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($karyawan['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($karyawan['alamat']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_telepon" class="form-label">No Telepon <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="no_telepon" name="no_telepon" 
                                           value="<?= htmlspecialchars($karyawan['no_telepon']); ?>" required>
                                </div>
                            </div>
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
                                        <?php while($jabatan = mysqli_fetch_assoc($jabatan_list)): ?>
                                            <option value="<?= $jabatan['id_jabatan']; ?>" 
                                                    <?= $karyawan['id_jabatan'] == $jabatan['id_jabatan'] ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($jabatan['nama_jabatan']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_departemen" class="form-label">Departemen <span class="text-danger">*</span></label>
                                    <select class="form-select" id="id_departemen" name="id_departemen" required>
                                        <option value="">Pilih Departemen</option>
                                        <?php mysqli_data_seek($departemen_list, 0); ?>
                                        <?php while($departemen = mysqli_fetch_assoc($departemen_list)): ?>
                                            <option value="<?= $departemen['id_departemen']; ?>" 
                                                    <?= $karyawan['id_departemen'] == $departemen['id_departemen'] ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($departemen['nama_departemen']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type_karyawan" class="form-label">Type Karyawan <span class="text-danger">*</span></label>
                            <select class="form-select" id="type_karyawan" name="type_karyawan" required>
                                <option value="tetap" <?= $karyawan['type_karyawan'] == 'tetap' ? 'selected' : ''; ?>>Tetap</option>
                                <option value="harian" <?= $karyawan['type_karyawan'] == 'harian' ? 'selected' : ''; ?>>Harian</option>
                                <option value="borongan" <?= $karyawan['type_karyawan'] == 'borongan' ? 'selected' : ''; ?>>Borongan</option>
                            </select>
                        </div>
                        
                        <!-- Type-specific fields -->
                        <div id="tetap_fields" style="display: <?= $karyawan['type_karyawan'] == 'tetap' ? 'block' : 'none'; ?>;">
                            <h6 class="text-primary mb-3">Data Karyawan Tetap</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="npwp" class="form-label">NPWP</label>
                                        <input type="text" class="form-control" id="npwp" name="npwp" 
                                               value="<?= htmlspecialchars($karyawan_tetap['npwp'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bpjs_ketenagakerjaan" class="form-label">BPJS Ketenagakerjaan</label>
                                        <input type="text" class="form-control" id="bpjs_ketenagakerjaan" name="bpjs_ketenagakerjaan" 
                                               value="<?= htmlspecialchars($karyawan_tetap['bpjs_ketenagakerjaan'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bpjs_kesehatan" class="form-label">BPJS Kesehatan</label>
                                        <input type="text" class="form-control" id="bpjs_kesehatan" name="bpjs_kesehatan" 
                                               value="<?= htmlspecialchars($karyawan_tetap['bpjs_kesehatan'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                                        <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" 
                                               value="<?= $karyawan_tetap['gaji_pokok'] ?? 0; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tunjangan_jabatan" class="form-label">Tunjangan Jabatan</label>
                                        <input type="number" class="form-control" id="tunjangan_jabatan" name="tunjangan_jabatan" 
                                               value="<?= $karyawan_tetap['tunjangan_jabatan'] ?? 0; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tunjangan_transport" class="form-label">Tunjangan Transport</label>
                                        <input type="number" class="form-control" id="tunjangan_transport" name="tunjangan_transport" 
                                               value="<?= $karyawan_tetap['tunjangan_transport'] ?? 0; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tunjangan_makan" class="form-label">Tunjangan Makan</label>
                                        <input type="number" class="form-control" id="tunjangan_makan" name="tunjangan_makan" 
                                               value="<?= $karyawan_tetap['tunjangan_makan'] ?? 0; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rekening_bank" class="form-label">Rekening Bank</label>
                                        <input type="text" class="form-control" id="rekening_bank" name="rekening_bank" 
                                               value="<?= htmlspecialchars($karyawan_tetap['rekening_bank'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nama_bank" class="form-label">Nama Bank</label>
                                <input type="text" class="form-control" id="nama_bank" name="nama_bank" 
                                       value="<?= htmlspecialchars($karyawan_tetap['nama_bank'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div id="non_tetap_fields" style="display: <?= $karyawan['type_karyawan'] == 'harian' || $karyawan['type_karyawan'] == 'borongan' ? 'block' : 'none'; ?>;">
                            <h6 class="text-primary mb-3">Data Karyawan Non Tetap</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="upah_per_hari" class="form-label">Upah per Hari</label>
                                        <input type="number" class="form-control" id="upah_per_hari" name="upah_per_hari" 
                                               value="<?= $karyawan_harian_borongan['upah_per_hari'] ?? 0; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="upah_per_jam" class="form-label">Upah per Jam</label>
                                        <input type="number" class="form-control" id="upah_per_jam" name="upah_per_jam" 
                                               value="<?= $karyawan_harian_borongan['upah_per_jam'] ?? 0; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="upah_borongan" class="form-label">Upah Borongan</label>
                                        <input type="number" class="form-control" id="upah_borongan" name="upah_borongan" 
                                               value="<?= $karyawan_harian_borongan['upah_borongan'] ?? 0; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                        <select class="form-select" id="metode_pembayaran" name="metode_pembayaran">
                                            <option value="transfer" <?= ($karyawan_harian_borongan['metode_pembayaran'] ?? '') == 'transfer' ? 'selected' : ''; ?>>Transfer</option>
                                            <option value="tunai" <?= ($karyawan_harian_borongan['metode_pembayaran'] ?? '') == 'tunai' ? 'selected' : ''; ?>>Tunai</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rekening_bank" class="form-label">Rekening Bank</label>
                                        <input type="text" class="form-control" id="rekening_bank_non" name="rekening_bank" 
                                               value="<?= htmlspecialchars($karyawan_harian_borongan['rekening_bank'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_bank" class="form-label">Nama Bank</label>
                                        <input type="text" class="form-control" id="nama_bank_non" name="nama_bank" 
                                               value="<?= htmlspecialchars($karyawan_harian_borongan['nama_bank'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
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

<script>
document.getElementById('type_karyawan').addEventListener('change', function() {
    const tetapFields = document.getElementById('tetap_fields');
    const nonTetapFields = document.getElementById('non_tetap_fields');
    
    if (this.value === 'tetap') {
        tetapFields.style.display = 'block';
        nonTetapFields.style.display = 'none';
    } else {
        tetapFields.style.display = 'none';
        nonTetapFields.style.display = 'block';
    }
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
