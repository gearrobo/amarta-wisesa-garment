-- Create supplier table in main database (db_amarta_wisesa)
USE `db_amarta_wisesa`;

CREATE TABLE IF NOT EXISTS `suplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_suplier` varchar(20) NOT NULL,
  `nama_suplier` varchar(100) NOT NULL,
  `alamat` text,
  `telepon` varchar(20),
  `email` varchar(100),
  `kontak_person` varchar(100),
  `npwp` varchar(50),
  `keterangan` text,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_suplier` (`kode_suplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data
INSERT INTO `suplier` (`kode_suplier`, `nama_suplier`, `alamat`, `telepon`, `email`, `kontak_person`, `npwp`, `keterangan`, `status`) VALUES
('SUP001', 'PT Maju Sejahtera', 'Jl. Sudirman No. 123, Jakarta', '021-5551234', 'info@majusejahtera.com', 'Budi Santoso', '123456789012345', 'Supplier bahan baku tekstil', 'Aktif'),
('SUP002', 'CV Sumber Jaya', 'Jl. Ahmad Yani No. 45, Bandung', '022-9876543', 'contact@sumberjaya.co.id', 'Siti Nurhaliza', '987654321098765', 'Supplier aksesoris fashion', 'Aktif'),
('SUP003', 'PT Global Textile', 'Jl. Gatot Subroto No. 78, Surabaya', '031-7654321', 'admin@globaltextile.com', 'Ahmad Rahman', '567890123456789', 'Supplier kain import', 'Aktif');

-- Verify table creation
SELECT * FROM suplier;
