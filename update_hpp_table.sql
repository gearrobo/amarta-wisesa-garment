-- Update HPP table to add harga column
ALTER TABLE `hpp` ADD COLUMN `harga` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `consp`;

-- Update existing records to set default harga if needed
UPDATE `hpp` SET `harga` = 1000 WHERE `harga` = 0.00;

-- Add index for better performance
ALTER TABLE `hpp` ADD INDEX `idx_id_persiapan` (`id_persiapan`);
ALTER TABLE `hpp` ADD INDEX `idx_gudang` (`gudang`);
ALTER TABLE `hpp` ADD INDEX `idx_kategori` (`kategori_barang`);
