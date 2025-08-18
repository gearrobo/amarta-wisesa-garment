<?php
include "includes/header.php";
include "config/db.php";

// Ambil data SPS berdasarkan ID
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM sps WHERE id = $id");
$sps = $result->fetch_assoc();

if (!$sps) {
    die("Data tidak ditemukan!");
}

// Proses update data
if (isset($_POST['update'])) {
    $tanggal = $_POST['tanggal'];
    $sps_no = $_POST['sps_no'];
    $customer = $_POST['customer'];
    $item = $_POST['item'];
    $artikel = $_POST['artikel'];
    $qty = intval($_POST['qty']);
    $size = $_POST['size'] ?? '';
    $kirim = $_POST['kirim'] ?? null;
    $approval = $_POST['approval'] ?? '';
    $sp_srx = $_POST['sp_srx'] ?? '';

    // Handle file uploads jika ada file baru
    $updateFields = [];
    $params = [];
    $types = '';

    // Upload file baru jika ada
    function uploadFile($field, $oldFile) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            $fileType = $_FILES[$field]['type'];
            
            if (!in_array($fileType, $allowedTypes)) {
                return ['error' => 'Tipe file tidak valid'];
            }
            
            if ($_FILES[$field]['size'] > 5000000) {
                return ['error' => 'File terlalu besar'];
            }
            
            $targetDir = "uploads/";
            $filename = time() . "_" . basename($_FILES[$field]['name']);
            $targetFile = $targetDir . $filename;
            
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetFile)) {
                // Hapus file lama jika ada
                if ($oldFile && file_exists($targetDir . $oldFile)) {
                    unlink($targetDir . $oldFile);
                }
                return ['filename' => $filename];
            }
        }
        return ['filename' => $oldFile];
    }

    // Upload files
    $files = ['sample_product', 'design', 'st_chart', 'material_sm', 'pola_sample', 'buat_sample'];
    foreach ($files as $file) {
        $uploadResult = uploadFile($file, $sps[$file]);
        if (isset($uploadResult['error'])) {
            $message = $uploadResult['error'];
            $messageType = 'danger';
            break;
        }
        $updateFields[] = "$file = ?";
        $params[] = $uploadResult['filename'];
        $types .= 's';
    }

    if (!isset($message)) {
        // Update data
        $sql = "UPDATE sps SET 
                tanggal = ?, sps_no = ?, customer = ?, item = ?, artikel = ?, qty = ?, size = ?, 
                kirim = ?, approval = ?, sp_srx = ?, 
                " . implode(', ', $updateFields) . "
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $params = array_merge([$tanggal, $sps_no, $customer, $item, $artikel, $qty, $size, $kirim, $approval, $sp_srx], $params, [$id]);
        $types = 'sssssisssss' . $types . 'i';
        
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            header("Location: sps-sample.php?updated=1");
            exit();
        } else {
            header("Location: sps-sample.php?error=1");
            exit();
        }
    }
}
?>

<div class="main-content">
    <h2>Edit SPS</h2>
    
    <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-4">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="<?= $sps['tanggal'] ?>" required>
        </div>
        <div class="col-md-4">
            <label>No SPS</label>
            <input type="text" name="sps_no" class="form-control" value="<?= $sps['sps_no'] ?>" required>
        </div>
        <div class="col-md-4">
            <label>Customer</label>
            <input type="text" name="customer" class="form-control" value="<?= $sps['customer'] ?>" required>
        </div>
        <div class="col-md-4">
            <label>Item</label>
            <input type="text" name="item" class="form-control" value="<?= $sps['item'] ?>" required>
        </div>
        <div class="col-md-4">
            <label>Artikel</label>
            <input type="text" name="artikel" class="form-control" value="<?= $sps['artikel'] ?>" required>
        </div>
        <div class="col-md-4">
            <label>Qty</label>
            <input type="number" name="qty" class="form-control" value="<?= $sps['qty'] ?>" required>
        </div>
        <div class="col-md-4">
            <label>Size</label>
            <input type="text" name="size" class="form-control" value="<?= $sps['size'] ?>">
        </div>
        <div class="col-md-4">
            <label>Kirim</label>
            <input type="date" name="kirim" class="form-control" value="<?= $sps['kirim'] ?>">
        </div>
        <div class="col-md-4">
            <label>Approval</label>
            <input type="text" name="approval" class="form-control" value="<?= $sps['approval'] ?>">
        </div>
        <div class="col-md-4">
            <label>SP SRX</label>
            <input type="text" name="sp_srx" class="form-control" value="<?= $sps['sp_srx'] ?>">
        </div>

        <?php
        $fileFields = [
            'sample_product' => 'Sample Product',
            'design' => 'Design',
            'st_chart' => 'ST Chart',
            'material_sm' => 'Material SM',
            'pola_sample' => 'Pola Sample',
            'buat_sample' => 'Buat Sample'
        ];
        
        foreach ($fileFields as $field => $label): 
            $currentFile = $sps[$field];
        ?>
        <div class="col-md-6">
            <label><?= $label ?> (.png)</label>
            <input type="file" name="<?= $field ?>" accept="image/png" class="form-control">
            <?php if ($currentFile): ?>
                <small class="text-muted">File saat ini: <?= $currentFile ?></small>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <div class="col-12">
            <button type="submit" name="update" class="btn btn-primary">Update</button>
            <a href="sps-sample.php" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>

    <?php include 'includes/footer.php';