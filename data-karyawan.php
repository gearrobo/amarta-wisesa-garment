<?php
// Start session
session_start();

// Include database configuration
include 'config/db.php';

// Include header
include 'includes/header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah_karyawan'])) {
        // Start transaction
        mysqli_autocommit($conn, FALSE);
        
        try {
            // Insert basic employee data
            $sql = "INSERT INTO karyawan (nik, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, no_telepon, email, type_karyawan, tanggal_masuk, id_jabatan, id_departemen) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt, "ssssssssssii", 
                $_POST['nik'],
                $_POST['nama_lengkap'],
                $_POST['jenis_kelamin'],
                $_POST['tempat_lahir'],
                $_POST['tanggal_lahir'],
                $_POST['alamat'],
                $_POST['no_telepon'],
                $_POST['email'],
                $_POST['type_karyawan'],
                $_POST['tanggal_masuk'],
                $_POST['id_jabatan'],
                $_POST['id_departemen']
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
            }
            
            $id_karyawan = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            // Insert type-specific data
            if ($_POST['type_karyawan'] == 'tetap') {
                $sql = "INSERT INTO karyawan_tetap (id_karyawan, npwp, bpjs_ketenagakerjaan, bpjs_kesehatan, gaji_pokok, tunjangan_jabatan, tunjangan_transport, tunjangan_makan, rekening_bank, nama_bank) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed for karyawan_tetap: " . mysqli_error($conn));
                }
                
                mysqli_stmt_bind_param($stmt, "isssiiiiis", 
                    $id_karyawan,
                    $_POST['npwp'],
                    $_POST['bpjs_ketenagakerjaan'],
                    $_POST['bpjs_kesehatan'],
                    $_POST['gaji_pokok'],
                    $_POST['tunjangan_jabatan'],
                    $_POST['tunjangan_transport'],
                    $_POST['tunjangan_makan'],
                    $_POST['rekening_bank'],
                    $_POST['nama_bank']
                );
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Execute failed for karyawan_tetap: " . mysqli_stmt_error($stmt));
                }
                mysqli_stmt_close($stmt);
                
            } else {
                $sql = "INSERT INTO karyawan_harian_borongan (id_karyawan, upah_per_hari, upah_per_jam, upah_borongan, metode_pembayaran, rekening_bank, nama_bank) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed for karyawan_harian_borongan: " . mysqli_error($conn));
                }
                
                mysqli_stmt_bind_param($stmt, "iiiiiss", 
                    $id_karyawan,
                    $_POST['upah_per_hari'],
                    $_POST['upah_per_jam'],
                    $_POST['upah_borongan'],
                    $_POST['metode_pembayaran'],
                    $_POST['rekening_bank'],
                    $_POST['nama_bank']
                );
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Execute failed for karyawan_non_tetap: " . mysqli_stmt_error($stmt));
                }
                mysqli_stmt_close($stmt);
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            $_SESSION['success'] = "Data karyawan berhasil ditambahkan!";
            header("Location: data-karyawan.php");
            exit();
            
        } catch(Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Gagal menambahkan data karyawan: " . $e->getMessage();
        }
    }
}

// Get all employees
$sql = "SELECT k.*, j.nama_jabatan, d.nama_departemen 
        FROM karyawan k 
        LEFT JOIN jabatan j ON k.id_jabatan = j.id_jabatan 
        LEFT JOIN departemen d ON k.id_departemen = d.id_departemen 
        ORDER BY k.nama_lengkap";
$result = mysqli_query($conn, $sql);
$karyawanList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get jabatan list
$sql = "SELECT * FROM jabatan ORDER BY nama_jabatan";
$result = mysqli_query($conn, $sql);
$jabatanList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get departemen list
$sql = "SELECT * FROM departemen ORDER BY nama_departemen";
$result = mysqli_query($conn, $sql);
$departemenList = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Data Karyawan</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="master-data.php">Master Data</a></li>
                    <li class="breadcrumb-item active">Data Karyawan</li>
                </ol>
            </nav>

            <!-- Alert Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKaryawanModal">
                    <i class="fas fa-plus"></i> Tambah Karyawan
                </button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>

            <!-- Employee Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Karyawan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tabelKaryawan">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jabatan</th>
                                    <th>Departemen</th>
                                    <th>Type</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($karyawanList as $karyawan): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($karyawan['nik']); ?></td>
                                    <td><?= htmlspecialchars($karyawan['nama_lengkap']); ?></td>
                                    <td><?= htmlspecialchars($karyawan['nama_jabatan']); ?></td>
                                    <td><?= htmlspecialchars($karyawan['nama_departemen']); ?></td>
                                    <td>
                                        <span class="badge bg-<?= $karyawan['type_karyawan'] == 'tetap' ? 'primary' : 'secondary'; ?>">
                                            <?= ucfirst($karyawan['type_karyawan']); ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($karyawan['tanggal_masuk'])); ?></td>
                                    <td>
                                        <a href="detail-karyawan.php?id=<?= $karyawan['id_karyawan']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit-karyawan.php?id=<?= $karyawan['id_karyawan']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusKaryawan(<?= $karyawan['id_karyawan']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tambah Karyawan Modal -->
<div class="modal fade" id="tambahKaryawanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NIK</label>
                                <input type="text" name="nik" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">Pilih</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No Telepon</label>
                                <input type="text" name="no_telepon" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Type Karyawan</label>
                                <select name="type_karyawan" class="form-select" required>
                                    <option value="">Pilih</option>
                                    <option value="tetap">Tetap</option>
                                    <option value="harian">Harian</option>
                                    <option value="borongan">Borongan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jabatan</label>
                                <select name="id_jabatan" class="form-select" required>
                                    <option value="">Pilih Jabatan</option>
                                    <?php foreach ($jabatanList as $jabatan): ?>
                                    <option value="<?= $jabatan['id_jabatan']; ?>"><?= $jabatan['nama_jabatan']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Departemen</label>
                                <select name="id_departemen" class="form-select" required>
                                    <option value="">Pilih Departemen</option>
                                    <?php foreach ($departemenList as $departemen): ?>
                                    <option value="<?= $departemen['id_departemen']; ?>"><?= $departemen['nama_departemen']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Type-specific fields will be shown dynamically via JavaScript -->
                    <div id="typeSpecificFields"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_karyawan" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tabelKaryawan').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
    
    // Handle type karyawan change
    $('select[name="type_karyawan"]').change(function() {
        var type = $(this).val();
        var fieldsHtml = '';
        
        if (type == 'tetap') {
            fieldsHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">NPWP</label>
                            <input type="text" name="npwp" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">BPJS Ketenagakerjaan</label>
                            <input type="text" name="bpjs_ketenagakerjaan" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">BPJS Kesehatan</label>
                            <input type="text" name="bpjs_kesehatan" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Gaji Pokok</label>
                            <input type="number" name="gaji_pokok" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tunjangan Jabatan</label>
                            <input type="number" name="tunjangan_jabatan" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tunjangan Transport</label>
                            <input type="number" name="tunjangan_transport" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tunjangan Makan</label>
                            <input type="number" name="tunjangan_makan" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Rekening Bank</label>
                            <input type="text" name="rekening_bank" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Bank</label>
                    <input type="text" name="nama_bank" class="form-control">
                </div>
            `;
        } else {
            fieldsHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Upah per Hari</label>
                            <input type="number" name="upah_per_hari" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Upah per Jam</label>
                            <input type="number" name="upah_per_jam" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Upah Borongan</label>
                            <input type="number" name="upah_borongan" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Rekening Bank</label>
                            <input type="text" name="rekening_bank" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Bank</label>
                            <input type="text" name="nama_bank" class="form-control">
                        </div>
                    </div>
                </div>
            `;
        }
        
        $('#typeSpecificFields').html(fieldsHtml);
    });
});

function hapusKaryawan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data karyawan ini?')) {
        // Implement delete functionality
        window.location.href = 'hapus-karyawan.php?id=' + id;
    }
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
