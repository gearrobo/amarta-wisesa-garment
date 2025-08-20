<?php
include 'includes/header.php';
include 'config/db.php';
?>

<div class="main-content">
    <!-- Main content -->
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
    <div class="box-tools pull-right">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                <i class="fa fa-plus"></i> Tambah Persiapan
            </button>
        </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table id="tablePersiapan" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. SPS</th>
                    <th>Nama Buyer</th>
                    <th>Style</th>
                    <th>Tanggal Persiapan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM persiapan ORDER BY id DESC";
                $result = mysqli_query($conn, $query);
                $no = 1;
                while($row = mysqli_fetch_assoc($result)) {
                    $status_class = '';
                    $status_text = '';
                    
                    switch($row['status']) {
                        case 'pending':
                            $status_class = 'label-warning';
                            $status_text = 'Pending';
                            break;
                        case 'proses':
                            $status_class = 'label-info';
                            $status_text = 'Dalam Proses';
                            break;
                        case 'selesai':
                            $status_class = 'label-success';
                            $status_text = 'Selesai';
                            break;
                        default:
                            $status_class = 'label-default';
                            $status_text = 'Belum Ditentukan';
                    }
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['no_sps'] ?></td>
                    <td><?= $row['nama_buyer'] ?></td>
                    <td><?= $row['style'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_persiapan'])) ?></td>
                    <td><span class="label <?= $status_class ?>"><?= $status_text ?></span></td>
                    <td>
                        <button class="btn btn-xs btn-info" onclick="editPersiapan(<?= $row['id'] ?>)">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-xs btn-danger" onclick="hapusPersiapan(<?= $row['id'] ?>)">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->


<!-- Modal Tambah Persiapan -->
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Tambah Data Persiapan</h4>
            </div>
            <form action="proses_persiapan.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>No. SPS</label>
                        <input type="text" class="form-control" name="no_sps" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Buyer</label>
                        <input type="text" class="form-control" name="nama_buyer" required>
                    </div>
                    <div class="form-group">
                        <label>Style</label>
                        <input type="text" class="form-control" name="style" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Persiapan</label>
                        <input type="date" class="form-control" name="tanggal_persiapan" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="proses">Dalam Proses</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    $('#tablePersiapan').DataTable({
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Indonesian.json"
        }
    });
});

function editPersiapan(id) {
    // Implementasi edit via AJAX atau redirect ke halaman edit
    window.location.href = 'edit_persiapan.php?id=' + id;
}

function hapusPersiapan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = 'proses_persiapan.php?hapus=' + id;
    }
}
</script>
