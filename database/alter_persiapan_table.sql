-- Add new columns to persiapan table for pola, marker, and upload_spk
ALTER TABLE `persiapan` 
ADD COLUMN IF NOT EXISTS `kode_barang` varchar(50) DEFAULT NULL AFTER `id_sps`,
ADD COLUMN IF NOT EXISTS `nama_barang` varchar(100) DEFAULT NULL AFTER `kode_barang`,
ADD COLUMN IF NOT EXISTS `jumlah` int DEFAULT NULL AFTER `nama_barang`,
ADD COLUMN IF NOT EXISTS `satuan` varchar(20) DEFAULT NULL AFTER `jumlah`,
ADD COLUMN IF NOT EXISTS `harga` decimal(10,2) DEFAULT NULL AFTER `satuan`,
ADD COLUMN IF NOT EXISTS `total` decimal(10,2) DEFAULT NULL AFTER `harga`,
ADD COLUMN IF NOT EXISTS `pola` varchar(255) DEFAULT NULL AFTER `total`,
ADD COLUMN IF NOT EXISTS `marker` varchar(255) DEFAULT NULL AFTER `pola`,
ADD COLUMN IF NOT EXISTS `upload_spk` varchar(255) DEFAULT NULL AFTER `marker`;
