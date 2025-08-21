<?php
include 'config/db.php'; // koneksi database

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    echo "Ambil ID: " . $id . "<br>";

    $sql = "SELECT * FROM sps WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Customer: " . $row["customer"]. " - Item: " . $row["item"]. "<br>";

        // Data persiapan (contoh dummy)
        $kode_barang = '123';
        $nama_barang = 'Kain Biru';
        $jumlah = 500;
        $satuan = 'meter';
        $harga = 10;
        $total = $jumlah * $harga;
        $status = 'pending';
        $tanggal_persiapan = $row['kirim']; // pastikan format YYYY-MM-DD

        $insert = "INSERT INTO persiapan 
          (id_sps, kode_barang, nama_barang, jumlah, satuan, harga, total, tanggal_persiapan, status) 
          VALUES (?,?,?,?,?,?,?,?,?)";

        $stmt2 = $conn->prepare($insert);
        if ($stmt2) {
            $stmt2->bind_param(
                "issisddss",
                $row['id'],        // id_sps (FK ke sps.id)
                $kode_barang,      // string
                $nama_barang,      // string
                $jumlah,           // int
                $satuan,           // string
                $harga,            // double
                $total,            // double
                $tanggal_persiapan,// string (date)
                $status            // string
            );

            if ($stmt2->execute()) {
                echo "✅ Data berhasil disimpan ke persiapan!<br>";
            } else {
                echo "❌ Error insert: " . $stmt2->error . "<br>";
            }

            $stmt2->close();
        } else {
            echo "❌ Error prepare insert: " . $conn->error;
        }
    }
}
