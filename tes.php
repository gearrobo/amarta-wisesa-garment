<?php
include 'config/db.php';

// Jika request AJAX untuk ambil bahan berdasarkan gudang
if (isset($_GET['action']) && $_GET['action'] === 'get_bahan') {
    $id_gudang = intval($_GET['id_gudang'] ?? 0);
    $result = $conn->query("SELECT id, nama_bahan FROM bahan WHERE id_gudang = $id_gudang ORDER BY nama_bahan ASC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

// Ambil data gudang
$gudang = $conn->query("SELECT id_gudang, nama_gudang FROM gudang ORDER BY nama_gudang ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pilih Gudang & Bahan</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h3>Pilih Gudang dan Bahan</h3>

    <form method="post" action="simpan.php">
        <label>Gudang</label><br>
        <select id="gudang" name="id_gudang">
            <option value="">-- Pilih Gudang --</option>
            <?php while ($g = $gudang->fetch_assoc()) { ?>
                <option value="<?= $g['id_gudang'] ?>"><?= $g['nama_gudang'] ?></option>
            <?php } ?>
        </select>

        <br><br>
        <label>Bahan</label><br>
        <select id="bahan" name="id_bahan">
            <option value="">-- Pilih Bahan --</option>
        </select>

        <br><br>
        <button type="submit">Simpan</button>
    </form>

    <script>
    $(document).ready(function() {
        $("#gudang").change(function() {
            var id_gudang = $(this).val();
            $("#bahan").html('<option>Loading...</option>');

            $.get("tes.php", { action: "get_bahan", id_gudang: id_gudang }, function(data) {
                var bahan = JSON.parse(data);
                var html = '<option value="">-- Pilih Bahan --</option>';
                bahan.forEach(function(b) {
                    html += '<option value="'+b.id+'">'+b.nama_bahan+'</option>';
                });
                $("#bahan").html(html);
            });
        });
    });
    </script>
</body>
</html>
