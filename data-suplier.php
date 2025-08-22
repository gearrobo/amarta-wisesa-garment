<?php
include 'includes/header.php';
include 'config/db.php';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        switch ($action) {
            case 'add':
                $kode_suplier = $_POST['kode_suplier'];
                $nama_suplier = $_POST['nama_suplier'];
                $alamat = $_POST['alamat'];
                $telepon = $_POST['telepon'];
                $email = $_POST['email'];
                $kontak_person = $_POST['kontak_person'];
                $npwp = $_POST['npwp'];
                $keterangan = $_POST['keterangan'];
                $status = $_POST['status'];
                
                $stmt = $mysqli->prepare("INSERT INTO suplier (kode_suplier, nama_suplier, alamat, telepon, email, kontak_person, npwp, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssss", $kode_suplier, $nama_suplier, $alamat, $telepon, $email, $kontak_person, $npwp, $keterangan, $status);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Supplier berhasil ditambahkan'];
                } else {
                    $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal menambahkan supplier: ' . $stmt->error];
                }
                $stmt->close();
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $kode_suplier = $_POST['kode_suplier'];
                $nama_suplier = $_POST['nama_suplier'];
                $alamat = $_POST['alamat'];
                $telepon = $_POST['telepon'];
                $email = $_POST['email'];
                $kontak_person = $_POST['kontak_person'];
                $npwp = $_POST['npwp'];
                $keterangan = $_POST['keterangan'];
                $status = $_POST['status'];
                
                $stmt = $mysqli->prepare("UPDATE suplier SET kode_suplier=?, nama_suplier=?, alamat=?, telepon=?, email=?, kontak_person=?, npwp=?, keterangan=?, status=? WHERE id=?");
                $stmt->bind_param("sssssssssi", $kode_suplier, $nama_suplier, $alamat, $telepon, $email, $kontak_person, $npwp, $keterangan, $status, $id);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Supplier berhasil diupdate'];
                } else {
                    $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal update supplier: ' . $stmt->error];
                }
                $stmt->close();
                break;
                
            case 'delete':
                $id = $_POST['id'];
                $stmt = $mysqli->prepare("DELETE FROM suplier WHERE id=?");
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Supplier berhasil dihapus'];
                } else {
                    $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal menghapus supplier: ' . $stmt->error];
                }
                $stmt->close();
                break;
        }
        
        header("Location: data-suplier.php");
        exit();
    }
}

// Get all suppliers
$suppliers = [];
$result = $conn->query("SELECT * FROM suplier ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }
}

// Get supplier for edit
$edit_supplier = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM suplier WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_supplier = $result->fetch_assoc();
    $stmt->close();
}
?>



    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col">
                    <h2>Data Supplier</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Data Supplier</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message']['type'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']['text']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="row mb-3">
                <div class="col">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                        <i class="fas fa-plus me-2"></i>Tambah Supplier
                    </button>
                </div>
            </div>

            <!-- Suppliers Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Supplier</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="suppliersTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Supplier</th>
                                    <th>Nama Supplier</th>
                                    <th>Alamat</th>
                                    <th>Telepon</th>
                                    <th>Email</th>
                                    <th>Kontak Person</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($suppliers as $index => $supplier): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($supplier['kode_suplier']); ?></td>
                                    <td><?php echo htmlspecialchars($supplier['nama_suplier']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($supplier['alamat'], 0, 50)) . '...'; ?></td>
                                    <td><?php echo htmlspecialchars($supplier['telepon']); ?></td>
                                    <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                                    <td><?php echo htmlspecialchars($supplier['kontak_person']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $supplier['status'] == 'Aktif' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $supplier['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editSupplier(<?php echo $supplier['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteSupplier(<?php echo $supplier['id']; ?>)">
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

    <!-- Add/Edit Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $edit_supplier ? 'Edit Supplier' : 'Tambah Supplier'; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="data-suplier.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $edit_supplier ? 'edit' : 'add'; ?>">
                        <?php if ($edit_supplier): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_supplier['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kode_suplier" class="form-label">Kode Supplier *</label>
                                    <input type="text" class="form-control" id="kode_suplier" name="kode_suplier" 
                                           value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['kode_suplier']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_suplier" class="form-label">Nama Supplier *</label>
                                    <input type="text" class="form-control" id="nama_suplier" name="nama_suplier" 
                                           value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['nama_suplier']) : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2"><?php echo $edit_supplier ? htmlspecialchars($edit_supplier['alamat']) : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telepon" class="form-label">Telepon</label>
                                    <input type="tel" class="form-control" id="telepon" name="telepon" 
                                           value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['telepon']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['email']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kontak_person" class="form-label">Kontak Person</label>
                                    <input type="text" class="form-control" id="kontak_person" name="kontak_person" 
                                           value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['kontak_person']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="npwp" class="form-label">NPWP</label>
                                    <input type="text" class="form-control" id="npwp" name="npwp" 
                                           value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['npwp']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2"><?php echo $edit_supplier ? htmlspecialchars($edit_supplier['keterangan']) : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Aktif" <?php echo $edit_supplier && $edit_supplier['status'] == 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                                <option value="Nonaktif" <?php echo $edit_supplier && $edit_supplier['status'] == 'Nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo $edit_supplier ? 'Update' : 'Simpan'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus supplier ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="data-suplier.php" id="deleteForm">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#suppliersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                order: [[0, 'asc']]
            });
            
            <?php if ($edit_supplier): ?>
                var modal = new bootstrap.Modal(document.getElementById('addSupplierModal'));
                modal.show();
            <?php endif; ?>
        });

        function editSupplier(id) {
            window.location.href = 'data-suplier.php?edit=' + id;
        }

        function deleteSupplier(id) {
            document.getElementById('deleteId').value = id;
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>