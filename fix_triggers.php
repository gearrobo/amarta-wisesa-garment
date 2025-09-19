<?php
include 'config/db.php';

// Drop existing triggers
$conn->query("DROP TRIGGER IF EXISTS update_stok_after_insert_transaksi");
$conn->query("DROP TRIGGER IF EXISTS update_stok_after_delete_transaksi");
$conn->query("DROP TRIGGER IF EXISTS update_stok_after_update_transaksi");

// Recreate triggers with correct column name
$trigger1 = "
CREATE TRIGGER update_stok_after_insert_transaksi AFTER INSERT ON inventory_transaksi_gudang
FOR EACH ROW
BEGIN
  IF NEW.jenis = 'masuk' THEN
    UPDATE inventory_gudang
    SET stok_akhir = stok_akhir + NEW.jumlah_masuk
    WHERE id = NEW.inventory_gudang_id;
  ELSEIF NEW.jenis = 'keluar' THEN
    UPDATE inventory_gudang
    SET stok_akhir = stok_akhir - NEW.jumlah_keluar
    WHERE id = NEW.inventory_gudang_id;
  END IF;
END
";

$trigger2 = "
CREATE TRIGGER update_stok_after_delete_transaksi AFTER DELETE ON inventory_transaksi_gudang
FOR EACH ROW
BEGIN
  IF OLD.jenis = 'masuk' THEN
    UPDATE inventory_gudang
    SET stok_akhir = stok_akhir - OLD.jumlah_masuk
    WHERE id = OLD.inventory_gudang_id;
  ELSEIF OLD.jenis = 'keluar' THEN
    UPDATE inventory_gudang
    SET stok_akhir = stok_akhir + OLD.jumlah_keluar
    WHERE id = OLD.inventory_gudang_id;
  END IF;
END
";

$trigger3 = "
CREATE TRIGGER update_stok_after_update_transaksi AFTER UPDATE ON inventory_transaksi_gudang
FOR EACH ROW
BEGIN
  -- Revert old transaction
  IF OLD.jenis = 'masuk' THEN
    UPDATE inventory_gudang
    SET stok_akhir = stok_akhir - OLD.jumlah_masuk
    WHERE id = OLD.inventory_gudang_id;
  ELSEIF OLD.jenis = 'keluar' THEN
    UPDATE inventory_gudang
    SET stok_akhir = stok_akhir + OLD.jumlah_keluar
    WHERE id = OLD.inventory_gudang_id;
  END IF;

  -- Apply new transaction
  IF NEW.jenis = 'masuk' THEN
    UPDATE inventory_gudang
    SET stok_akhir = stok_akhir + NEW.jumlah_masuk
    WHERE id = NEW.inventory_gudang_id;
  ELSEIF NEW.jenis = 'keluar' THEN
    UPDATE inventory_gudang
    SET stok_akhir = stok_akhir - NEW.jumlah_keluar
    WHERE id = NEW.inventory_gudang_id;
  END IF;
END
";

if ($conn->query($trigger1) && $conn->query($trigger2) && $conn->query($trigger3)) {
    echo "Triggers fixed successfully!";
} else {
    echo "Error fixing triggers: " . $conn->error;
}

$conn->close();
?>
