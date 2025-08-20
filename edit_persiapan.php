<?php
include 'includes/header.php';
include 'includes/sidebar.php';

// Ambil data persiapan berdasarkan ID
$id = intval($_GET['id']);
$query = "SELECT * FROM persiapan WHERE id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = "Data persiapan tidak ditemukan";
    header("Location: persiapan.php");
    exit();
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Edit Persiapan Produksi
            <small>Edit data persiapan</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="persiapan.php">Persiapan</a></li>
            <li class="active">Edit</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Data Persiapan</h3>
                    </div>
                    <!-- /.box-header -->
                    <form action="proses_persiapan.php" method="POST">
                        <input type="hidden" name="update_persiapan" value="1">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                        <div class="box-body">
                            <div class="form-group">
                                <label>No. SPS</label>
                                <input type="text" class="form-control" name="no_sps" value="<?= $data['no_sps'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Nama Buyer</label>
                                <input type="text" class="form-control" name="nama_buyer" value="<?= $data['nama_buyer'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Style</label>
                                <input type="text" class="form-control" name="style" value="<?= $data['style'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Persiapan</label>
                                <input type="date" class="form-control" name="tanggal_persiapan" value="<?= $data['tanggal_persiapan'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="pending" <?= $data['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="proses" <?= $data['status'] == 'proses' ? 'selected' : '' ?>>Dalam Proses</option>
                                    <option value="selesai" <?= $data['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Update Data</button>
                            <a href="persiapan.php" class="btn btn-default">Batal</a>
                        </div>
                    </form>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include 'includes/footer.php'; ?>
