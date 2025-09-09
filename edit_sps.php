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
    $types = 'sssssisssss'; // For the non-file fields
    
    // Upload file baru jika ada
    function uploadFile($field, $oldFile) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            $fileType = $_FILES[$field]['type'];
            
            if (!in_array($fileType, $allowedTypes)) {
                return ['error' => 'Tipe file tidak valid untuk ' . $field];
            }
            
            if ($_FILES[$field]['size'] > 5000000) {
                return ['error' => 'File terlalu besar untuk ' . $field];
            }
            
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $filename = time() . "_" . uniqid() . "_" . basename($_FILES[$field]['name']);
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
    $uploadSuccess = true;
    
    foreach ($files as $file) {
        if (isset($_FILES[$file]) && $_FILES[$file]['error'] == 0) {
            $uploadResult = uploadFile($file, $sps[$file]);
            if (isset($uploadResult['error'])) {
                $message = $uploadResult['error'];
                $messageType = 'danger';
                $uploadSuccess = false;
                break;
            }
            $updateFields[] = "$file = ?";
            $params[] = $uploadResult['filename'];
            $types .= 's';
        } else {
            // Keep existing file if no new upload
            $updateFields[] = "$file = ?";
            $params[] = $sps[$file];
            $types .= 's';
        }
    }

    if ($uploadSuccess) {
        // Build the complete SQL query
        $baseFields = [
            "tanggal = ?", "sps_no = ?", "customer = ?", "item = ?", "artikel = ?", 
            "qty = ?", "size = ?", "kirim = ?", "approval = ?", "sp_srx = ?"
        ];
        
        $allFields = array_merge($baseFields, $updateFields);
        $sql = "UPDATE sps SET " . implode(', ', $allFields) . " WHERE id = ?";
        
        // Build parameters array
        $baseParams = [$tanggal, $sps_no, $customer, $item, $artikel, $qty, $size, $kirim, $approval, $sp_srx];
        $allParams = array_merge($baseParams, $params, [$id]);
        
        // Build types string
        $types = str_repeat('s', count($baseParams)); // 10 string parameters
        $types .= str_repeat('s', count($params));   // file parameters (all strings)
        $types .= 'i'; // id parameter
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$allParams);
        
        if ($stmt->execute()) {
            header("Location: sps-sample.php?updated=1");
            exit();
        } else {
            $message = "Gagal update data: " . $stmt->error;
            $messageType = 'danger';
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
            $filePath = "uploads/" . $currentFile;
        ?>
        <div class="col-md-6 mb-4">
            <label><?= $label ?> (.png)</label>
            <input type="file" name="<?= $field ?>" accept="image/png" class="form-control mb-2" onchange="previewImage(this, '<?= $field ?>_preview')">
            
            <div class="preview-container">
                <?php if ($currentFile && file_exists($filePath)): ?>
                    <img src="<?= $filePath ?>" alt="<?= $label ?>" class="img-thumbnail" style="max-width: 200px; max-height: 150px; cursor: pointer;" onclick="openModal('<?= $filePath ?>', '<?= $label ?>')">
                    <br>
                    <small class="text-muted">File saat ini: <?= $currentFile ?></small>
                <?php else: ?>
                    <div class="no-preview text-muted" style="width: 200px; height: 150px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center;">
                        <small>No file uploaded</small>
                    </div>
                <?php endif; ?>
            </div>
            
            <div id="<?= $field ?>_preview" class="mt-2" style="display: none;">
                <img src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
            </div>
        </div>
        <?php endforeach; ?>

        <div class="col-12">
            <button type="submit" name="update" class="btn btn-primary">Update</button>
            <a href="sps-sample.php" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>

    <!-- Modal for Image Preview -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Document Preview">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="downloadBtn" href="" class="btn btn-primary" download>Download</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to preview uploaded image
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const previewImg = preview.querySelector('img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        // Function to open modal with image
        function openModal(imageSrc, title) {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('imageModalLabel');
            const downloadBtn = document.getElementById('downloadBtn');
            
            modalImage.src = imageSrc;
            modalTitle.textContent = title;
            downloadBtn.href = imageSrc;
            
            modal.show();
        }

        // Add some styling for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effect to images
            const images = document.querySelectorAll('.img-thumbnail');
            images.forEach(img => {
                img.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                    this.style.transition = 'transform 0.3s ease';
                });
                
                img.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>

    <?php include 'includes/footer.php';
