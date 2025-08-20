-- =============================================
-- SQL Script for Data Gudang (Warehouse Management)
-- Amarta Wisesa Garment
-- =============================================

-- Drop table if exists (for development purposes)
DROP TABLE IF EXISTS `gudang`;

-- Create gudang (warehouse) table
CREATE TABLE `gudang` (
  `id_gudang` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `kepala_gudang` varchar(100) NOT NULL,
  `kapasitas` int(11) NOT NULL COMMENT 'Kapasitas dalam meter persegi',
  `keterangan` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_gudang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for warehouses
INSERT INTO `gudang` (`nama`, `alamat`, `kepala_gudang`, `kapasitas`, `keterangan`) VALUES
('Gudang Utama Jakarta', 'Jl. Industri No. 123, Jakarta Barat', 'Budi Santoso', 5000, 'Gudang utama untuk penyimpanan bahan baku'),
('Gudang Finishing Bandung', 'Jl. Pahlawan No. 45, Bandung', 'Siti Nurhaliza', 3000, 'Gudang untuk produk jadi dan finishing'),
('Gudang Bahan Surabaya', 'Jl. Ahmad Yani No. 78, Surabaya', 'Andi Wijaya', 4000, 'Gudang khusus untuk bahan baku tekstil'),
('Gudang Distribusi Semarang', 'Jl. Diponegoro No. 90, Semarang', 'Rina Marlina', 2500, 'Gudang untuk distribusi ke toko-toko'),
('Gudang Produksi Tangerang', 'Jl. Raya Serpong KM 15, Tangerang', 'Dedi Kurniawan', 6000, 'Gudang dekat area produksi utama'),
('Gudang Cadangan Bekasi', 'Jl. Sudirman No. 56, Bekasi', 'Maya Sari', 2000, 'Gudang cadangan untuk stok overflow');

-- Create indexes for better performance
CREATE INDEX idx_nama ON gudang(nama);
CREATE INDEX idx_kepala_gudang ON gudang(kepala_gudang);

-- =============================================
-- Additional SQL for related functionality
-- =============================================

-- Create table for warehouse inventory (optional - for future expansion)
DROP TABLE IF EXISTS `inventory_gudang`;
CREATE TABLE `inventory_gudang` (
  `id_inventory` int(11) NOT NULL AUTO_INCREMENT,
  `id_gudang` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(20) NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_inventory`),
  KEY `id_gudang` (`id_gudang`),
  CONSTRAINT `inventory_gudang_ibfk_1` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample inventory data (optional)
INSERT INTO `inventory_gudang` (`id_gudang`, `kode_barang`, `nama_barang`, `jumlah`, `satuan`, `tanggal_masuk`) VALUES
(1, 'BRG001', 'Kain Katun Premium', 500, 'roll', '2024-01-15'),
(1, 'BRG002', 'Benang Polyester', 200, 'kg', '2024-01-20'),
(2, 'BRG003', 'Kaos Polos L', 1000, 'pcs', '2024-01-25'),
(2, 'BRG004', 'Celana Jeans M', 300, 'pcs', '2024-01-28');

-- =============================================
-- Views for reporting (optional)
-- =============================================

-- Create view for warehouse summary
DROP VIEW IF EXISTS `v_gudang_summary`;
CREATE VIEW `v_gudang_summary` AS
SELECT 
    g.id_gudang,
    g.nama,
    g.alamat,
    g.kepala_gudang,
    g.kapasitas,
    COUNT(i.id_inventory) as jumlah_barang,
    COALESCE(SUM(i.jumlah), 0) as total_stok,
    g.keterangan
FROM gudang g
LEFT JOIN inventory_gudang i ON g.id_gudang = i.id_gudang
GROUP BY g.id_gudang;

-- =============================================
-- Stored procedures for common operations (optional)
-- =============================================

DELIMITER //

-- Procedure to add new warehouse
DROP PROCEDURE IF EXISTS `sp_tambah_gudang`;
CREATE PROCEDURE `sp_tambah_gudang`(
    IN p_nama VARCHAR(100),
    IN p_alamat TEXT,
    IN p_kepala_gudang VARCHAR(100),
    IN p_kapasitas INT,
    IN p_keterangan TEXT
)
BEGIN
    INSERT INTO gudang (nama, alamat, kepala_gudang, kapasitas, keterangan)
    VALUES (p_nama, p_alamat, p_kepala_gudang, p_kapasitas, p_keterangan);
    
    SELECT LAST_INSERT_ID() as id_gudang_baru;
END//

-- Procedure to get warehouse statistics
DROP PROCEDURE IF EXISTS `sp_get_gudang_stats`;
CREATE PROCEDURE `sp_get_gudang_stats`()
BEGIN
    SELECT 
        COUNT(*) as total_gudang,
        SUM(kapasitas) as total_kapasitas,
        AVG(kapasitas) as rata_rata_kapasitas,
        MAX(kapasitas) as kapasitas_terbesar,
        MIN(kapasitas) as kapasitas_terkecil
    FROM gudang;
END//

DELIMITER ;

-- =============================================
-- Usage Instructions
-- =============================================
-- 1. Run this SQL file in your MySQL database
-- 2. Make sure your database user has appropriate permissions
-- 3. Update config/db.php with your database credentials
-- 4. The table is ready to use with data-gudang.php
