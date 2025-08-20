-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_amarta_wisesa
CREATE DATABASE IF NOT EXISTS `db_amarta_wisesa` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_amarta_wisesa`;

-- Dumping structure for table db_amarta_wisesa.absensi
CREATE TABLE IF NOT EXISTS `absensi` (
  `id_absensi` int NOT NULL AUTO_INCREMENT,
  `id_karyawan` int NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status_hadir` enum('hadir','izin','sakit','alpha','cuti') COLLATE utf8mb4_general_ci DEFAULT 'hadir',
  `keterangan` text COLLATE utf8mb4_general_ci,
  `lembur_jam` decimal(4,2) DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_absensi`),
  UNIQUE KEY `unique_absensi` (`id_karyawan`,`tanggal`),
  CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.absensi: ~0 rows (approximately)

-- Dumping structure for table db_amarta_wisesa.departemen
CREATE TABLE IF NOT EXISTS `departemen` (
  `id_departemen` int NOT NULL AUTO_INCREMENT,
  `nama_departemen` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_departemen`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.departemen: ~5 rows (approximately)
INSERT IGNORE INTO `departemen` (`id_departemen`, `nama_departemen`, `deskripsi`, `created_at`, `updated_at`) VALUES
	(1, 'Produksi', 'Departemen yang menangani proses produksi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(2, 'Quality Assurance', 'Departemen yang menangani kontrol kualitas', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(3, 'Gudang', 'Departemen yang menangani penyimpanan dan distribusi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(4, 'HR & GA', 'Departemen yang menangani sumber daya manusia', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(5, 'Finance', 'Departemen yang menangani keuangan', '2025-08-18 15:13:05', '2025-08-18 15:13:05');

-- Dumping structure for table db_amarta_wisesa.gaji
CREATE TABLE IF NOT EXISTS `gaji` (
  `id_gaji` int NOT NULL AUTO_INCREMENT,
  `id_karyawan` int NOT NULL,
  `periode_bulan` int NOT NULL,
  `periode_tahun` int NOT NULL,
  `gaji_pokok` decimal(12,2) DEFAULT '0.00',
  `tunjangan` decimal(12,2) DEFAULT '0.00',
  `lembur` decimal(12,2) DEFAULT '0.00',
  `potongan` decimal(12,2) DEFAULT '0.00',
  `total_gaji` decimal(12,2) NOT NULL,
  `status` enum('draft','disetujui','dibayar') COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `tanggal_bayar` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_gaji`),
  UNIQUE KEY `unique_gaji` (`id_karyawan`,`periode_bulan`,`periode_tahun`),
  CONSTRAINT `gaji_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.gaji: ~0 rows (approximately)

-- Dumping structure for table db_amarta_wisesa.gudang
CREATE TABLE IF NOT EXISTS `gudang` (
  `id_gudang` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kepala_gudang` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kapasitas` int NOT NULL COMMENT 'Kapasitas dalam meter persegi',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_gudang`),
  KEY `idx_nama` (`nama`),
  KEY `idx_kepala_gudang` (`kepala_gudang`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_amarta_wisesa.gudang: ~6 rows (approximately)
INSERT IGNORE INTO `gudang` (`id_gudang`, `nama`, `alamat`, `kepala_gudang`, `kapasitas`, `keterangan`, `created_at`, `updated_at`) VALUES
	(1, 'Gudang Utama Jakarta', 'Jl. Industri No. 123, Jakarta Barat', 'Budi Santoso', 5000, 'Gudang utama untuk penyimpanan bahan baku', '2025-08-20 03:28:41', '2025-08-20 03:28:41'),
	(2, 'Gudang Finishing Bandung', 'Jl. Pahlawan No. 45, Bandung', 'Siti Nurhaliza', 3000, 'Gudang untuk produk jadi dan finishing', '2025-08-20 03:28:41', '2025-08-20 03:28:41'),
	(3, 'Gudang Bahan Surabaya', 'Jl. Ahmad Yani No. 78, Surabaya', 'Andi Wijaya', 4000, 'Gudang khusus untuk bahan baku tekstil', '2025-08-20 03:28:41', '2025-08-20 03:28:41'),
	(5, 'Gudang Produksi Tangerang', 'Jl. Raya Serpong KM 15, Tangerang', 'Dedi Kurniawan', 6000, 'Gudang dekat area produksi utama', '2025-08-20 03:28:41', '2025-08-20 03:28:41'),
	(6, 'Gudang Cadangan Bekasi', 'Jl. Sudirman No. 56, Bekasi', 'Maya Sari', 2000, 'Gudang cadangan untuk stok overflow', '2025-08-20 03:28:41', '2025-08-20 03:28:41'),
	(7, 'Gudang Probolinggo', 'Probolinggo', 'Rezky', 5000, 'gudang kain', '2025-08-20 03:31:39', '2025-08-20 03:31:39');

-- Dumping structure for table db_amarta_wisesa.inventory
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `warehouse` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah` int NOT NULL DEFAULT '0',
  `harga_per_unit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nama_barang` (`nama_barang`),
  KEY `idx_warehouse` (`warehouse`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.inventory: ~1 rows (approximately)
INSERT IGNORE INTO `inventory` (`id`, `nama_barang`, `warehouse`, `unit`, `jumlah`, `harga_per_unit`, `keterangan`, `created_at`, `updated_at`) VALUES
	(7, 'Kain Hijau', 'Gudang B', 'm²', 1000, 25000.00, 'ya', '2025-08-20 11:55:19', NULL);

-- Dumping structure for table db_amarta_wisesa.inventory_gudang
CREATE TABLE IF NOT EXISTS `inventory_gudang` (
  `id_inventory` int NOT NULL AUTO_INCREMENT,
  `id_gudang` int NOT NULL,
  `kode_barang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL DEFAULT '0',
  `satuan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_inventory`),
  KEY `id_gudang` (`id_gudang`),
  CONSTRAINT `inventory_gudang_ibfk_1` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_amarta_wisesa.inventory_gudang: ~4 rows (approximately)
INSERT IGNORE INTO `inventory_gudang` (`id_inventory`, `id_gudang`, `kode_barang`, `nama_barang`, `jumlah`, `satuan`, `tanggal_masuk`, `tanggal_update`) VALUES
	(1, 1, 'BRG001', 'Kain Katun Premium', 500, 'roll', '2024-01-15', '2025-08-20 03:28:41'),
	(2, 1, 'BRG002', 'Benang Polyester', 200, 'kg', '2024-01-20', '2025-08-20 03:28:41'),
	(3, 2, 'BRG003', 'Kaos Polos L', 1000, 'pcs', '2024-01-25', '2025-08-20 03:28:41'),
	(4, 2, 'BRG004', 'Celana Jeans M', 300, 'pcs', '2024-01-28', '2025-08-20 03:28:41');

-- Dumping structure for view db_amarta_wisesa.inventory_summary
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `inventory_summary` (
	`warehouse` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_general_ci',
	`total_jenis_barang` BIGINT(19) NOT NULL,
	`total_stok` DECIMAL(32,0) NULL,
	`total_nilai` DECIMAL(47,2) NULL
) ENGINE=MyISAM;

-- Dumping structure for table db_amarta_wisesa.inventory_transaksi
CREATE TABLE IF NOT EXISTS `inventory_transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inventory_id` int NOT NULL,
  `jenis` enum('masuk','keluar') COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah_masuk` int DEFAULT '0',
  `jumlah_keluar` int DEFAULT '0',
  `harga_per_unit` decimal(15,2) DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `tanggal_transaksi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_id` (`inventory_id`),
  CONSTRAINT `inventory_transaksi_ibfk_1` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.inventory_transaksi: ~0 rows (approximately)

-- Dumping structure for table db_amarta_wisesa.jabatan
CREATE TABLE IF NOT EXISTS `jabatan` (
  `id_jabatan` int NOT NULL AUTO_INCREMENT,
  `nama_jabatan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_jabatan`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.jabatan: ~7 rows (approximately)
INSERT IGNORE INTO `jabatan` (`id_jabatan`, `nama_jabatan`, `deskripsi`, `created_at`, `updated_at`) VALUES
	(1, 'Operator Produksi', 'Karyawan yang bekerja di lini produksi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(2, 'Supervisor Produksi', 'Mengawasi dan mengkoordinir lini produksi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(3, 'Quality Control', 'Memastikan kualitas produk sesuai standar', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(4, 'Admin Gudang', 'Mengelola stok dan administrasi gudang', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(5, 'HR Manager', 'Mengelola sumber daya manusia', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(6, 'Finance', 'Mengelola keuangan dan gaji karyawan', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(7, 'Pekerja', 'Orang yang bekerja Lepas atau Pekerja Harian atau Borongan.', '2025-08-18 15:41:53', '2025-08-18 15:41:53');

-- Dumping structure for table db_amarta_wisesa.karyawan
CREATE TABLE IF NOT EXISTS `karyawan` (
  `id_karyawan` int NOT NULL AUTO_INCREMENT,
  `nik` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_general_ci NOT NULL,
  `tempat_lahir` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `no_telepon` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type_karyawan` enum('tetap','harian','borongan') COLLATE utf8mb4_general_ci NOT NULL,
  `status_karyawan` enum('aktif','nonaktif','cuti','keluar') COLLATE utf8mb4_general_ci DEFAULT 'aktif',
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `id_jabatan` int DEFAULT NULL,
  `id_departemen` int DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_karyawan`),
  UNIQUE KEY `nik` (`nik`),
  KEY `id_jabatan` (`id_jabatan`),
  KEY `id_departemen` (`id_departemen`),
  CONSTRAINT `karyawan_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE SET NULL,
  CONSTRAINT `karyawan_ibfk_2` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.karyawan: ~6 rows (approximately)
INSERT IGNORE INTO `karyawan` (`id_karyawan`, `nik`, `nama_lengkap`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telepon`, `email`, `type_karyawan`, `status_karyawan`, `tanggal_masuk`, `tanggal_keluar`, `id_jabatan`, `id_departemen`, `foto`, `created_at`, `updated_at`) VALUES
	(1, 'K001', 'Budi Santoso', 'L', 'Jakarta', '1985-05-15', 'Jl. Merdeka No. 123', '081234567890', 'budi@amarta.com', 'tetap', 'aktif', '2020-01-15', NULL, 2, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(2, 'K002', 'Siti Nurhaliza', 'P', 'Bandung', '1990-08-20', 'Jl. Sudirman No. 45', '082345678901', 'siti@amarta.com', 'tetap', 'aktif', '2019-03-10', NULL, 5, 4, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(3, 'KH001', 'Ahmad Dahlan', 'L', 'Yogyakarta', '1995-12-10', 'Jl. Malioboro No. 78', '083456789012', 'ahmad@amarta.com', 'harian', 'aktif', '2023-06-01', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(4, 'KH002', 'Rina Marlina', 'P', 'Medan', '1998-03-25', 'Jl. Thamrin No. 90', '084567890123', 'rina@amarta.com', 'harian', 'aktif', '2023-07-15', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(5, 'KB001', 'Joko Widodo', 'L', 'Solo', '1992-07-30', 'Jl. Slamet Riyadi No. 56', '085678901234', 'joko@amarta.com', 'borongan', 'aktif', '2023-08-01', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(8, '12345678', 'Resky Putra P', 'L', 'Kota Depok', '1999-08-08', 'Dimun', '083456789012', 'androexe5@gmail.com', 'tetap', 'aktif', '2025-08-18', NULL, 7, 1, NULL, '2025-08-18 16:59:04', '2025-08-19 06:42:44');

-- Dumping structure for table db_amarta_wisesa.karyawan_harian_borongan
CREATE TABLE IF NOT EXISTS `karyawan_harian_borongan` (
  `id_karyawan` int NOT NULL,
  `upah_per_hari` decimal(10,2) DEFAULT '0.00',
  `upah_per_jam` decimal(10,2) DEFAULT '0.00',
  `upah_borongan` decimal(12,2) DEFAULT '0.00',
  `metode_pembayaran` enum('harian','borongan','mingguan') COLLATE utf8mb4_general_ci DEFAULT 'harian',
  `rekening_bank` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_bank` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_karyawan`),
  CONSTRAINT `karyawan_harian_borongan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.karyawan_harian_borongan: ~3 rows (approximately)
INSERT IGNORE INTO `karyawan_harian_borongan` (`id_karyawan`, `upah_per_hari`, `upah_per_jam`, `upah_borongan`, `metode_pembayaran`, `rekening_bank`, `nama_bank`) VALUES
	(3, 150000.00, 0.00, 0.00, 'harian', NULL, NULL),
	(4, 150000.00, 0.00, 0.00, 'harian', NULL, NULL),
	(5, 0.00, 0.00, 2500000.00, 'borongan', NULL, NULL);

-- Dumping structure for table db_amarta_wisesa.karyawan_tetap
CREATE TABLE IF NOT EXISTS `karyawan_tetap` (
  `id_karyawan` int NOT NULL,
  `npwp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bpjs_ketenagakerjaan` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bpjs_kesehatan` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gaji_pokok` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tunjangan_jabatan` decimal(12,2) DEFAULT '0.00',
  `tunjangan_transport` decimal(12,2) DEFAULT '0.00',
  `tunjangan_makan` decimal(12,2) DEFAULT '0.00',
  `rekening_bank` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_bank` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_karyawan`),
  CONSTRAINT `karyawan_tetap_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.karyawan_tetap: ~2 rows (approximately)
INSERT IGNORE INTO `karyawan_tetap` (`id_karyawan`, `npwp`, `bpjs_ketenagakerjaan`, `bpjs_kesehatan`, `gaji_pokok`, `tunjangan_jabatan`, `tunjangan_transport`, `tunjangan_makan`, `rekening_bank`, `nama_bank`) VALUES
	(1, '123456789012345', 'BPJS-TK001', 'BPJS-KS001', 5000000.00, 1500000.00, 500000.00, 300000.00, NULL, NULL),
	(2, '987654321098765', 'BPJS-TK002', 'BPJS-KS002', 7000000.00, 2000000.00, 750000.00, 500000.00, NULL, NULL);

-- Dumping structure for table db_amarta_wisesa.mesin
CREATE TABLE IF NOT EXISTS `mesin` (
  `id_mesin` int NOT NULL AUTO_INCREMENT,
  `seri_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_mesin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `lokasi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mesin`),
  UNIQUE KEY `seri_number` (`seri_number`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.mesin: ~5 rows (approximately)
INSERT IGNORE INTO `mesin` (`id_mesin`, `seri_number`, `nama_mesin`, `lokasi`, `tanggal_masuk`, `keterangan`, `created_at`, `updated_at`) VALUES
	(1, 'SN-001', 'Mesin Jahit Singer', 'Line A - Station 1', '2024-01-15', 'Mesin jahit untuk produksi kaos', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
	(3, 'SN-003', 'Mesin Bordir Komputer', 'Line B - Station 1', '2024-02-01', 'Mesin bordir digital 10 kepala', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
	(4, 'SN-004', 'Mesin Cutting Laser', 'Area Cutting', '2024-02-10', 'Mesin cutting presisi untuk kain', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
	(5, 'SN-005', 'Mesin Press Kaos', 'Area Finishing', '2024-02-15', 'Mesin press untuk sablon digital', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
	(11, 'SN-006', 'Mesin Jahit', 'Warehouse', '2025-08-19', 'mesin ya', '2025-08-19 03:32:14', '2025-08-19 03:42:03');

-- Dumping structure for table db_amarta_wisesa.pengguna
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id_pengguna` int NOT NULL AUTO_INCREMENT,
  `id_karyawan` int DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','hr','karyawan') COLLATE utf8mb4_general_ci DEFAULT 'karyawan',
  `status_aktif` tinyint(1) DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `id_karyawan` (`id_karyawan`),
  CONSTRAINT `pengguna_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.pengguna: ~2 rows (approximately)
INSERT IGNORE INTO `pengguna` (`id_pengguna`, `id_karyawan`, `username`, `password`, `role`, `status_aktif`, `last_login`, `created_at`, `updated_at`) VALUES
	(1, 1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
	(2, 2, 'hr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hr', 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05');

-- Dumping structure for table db_amarta_wisesa.persiapan
CREATE TABLE IF NOT EXISTS `persiapan` (
  `id` int NOT NULL,
  `id_sps` int NOT NULL,
  `tanggal_persiapan` date NOT NULL,
  `pola` varchar(255) DEFAULT NULL,
  `marker` varchar(255) DEFAULT NULL,
  `status` enum('pending','proses','selesai') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_sps` (`id_sps`),
  CONSTRAINT `persiapan_ibfk_1` FOREIGN KEY (`id_sps`) REFERENCES `sps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table db_amarta_wisesa.persiapan: ~0 rows (approximately)

-- Dumping structure for table db_amarta_wisesa.sps
CREATE TABLE IF NOT EXISTS `sps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tanggal` date DEFAULT NULL,
  `sps_no` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `item` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `artikel` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `size` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sample_product` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `design` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `st_chart` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `material_sm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pola_sample` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `buat_sample` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kirim` date DEFAULT NULL,
  `approval` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sp_srx` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_amarta_wisesa.sps: ~1 rows (approximately)
INSERT IGNORE INTO `sps` (`id`, `tanggal`, `sps_no`, `customer`, `item`, `artikel`, `qty`, `size`, `sample_product`, `design`, `st_chart`, `material_sm`, `pola_sample`, `buat_sample`, `kirim`, `approval`, `sp_srx`) VALUES
	(2, '2025-08-19', 'SPS-01', 'Resky-jakarta', 'T-Shirt', 'T-Shirt', 2, 'L', '1755572545_68a3e9415efd7_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '2025-08-26', 'Approved', 'SPK-20250819-0002');

-- Dumping structure for procedure db_amarta_wisesa.sp_get_gudang_stats
DELIMITER //
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

-- Dumping structure for procedure db_amarta_wisesa.sp_tambah_gudang
DELIMITER //
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
DELIMITER ;

-- Dumping structure for view db_amarta_wisesa.v_gudang_summary
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_gudang_summary` (
	`id_gudang` INT(10) NOT NULL,
	`nama` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`alamat` TEXT NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`kepala_gudang` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`kapasitas` INT(10) NOT NULL COMMENT 'Kapasitas dalam meter persegi',
	`jumlah_barang` BIGINT(19) NOT NULL,
	`total_stok` DECIMAL(32,0) NOT NULL,
	`keterangan` TEXT NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Dumping structure for view db_amarta_wisesa.inventory_summary
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `inventory_summary`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `inventory_summary` AS select `inventory`.`warehouse` AS `warehouse`,count(0) AS `total_jenis_barang`,sum(`inventory`.`jumlah`) AS `total_stok`,sum((`inventory`.`jumlah` * `inventory`.`harga_per_unit`)) AS `total_nilai` from `inventory` group by `inventory`.`warehouse`;

-- Dumping structure for view db_amarta_wisesa.v_gudang_summary
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_gudang_summary`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_gudang_summary` AS select `g`.`id_gudang` AS `id_gudang`,`g`.`nama` AS `nama`,`g`.`alamat` AS `alamat`,`g`.`kepala_gudang` AS `kepala_gudang`,`g`.`kapasitas` AS `kapasitas`,count(`i`.`id_inventory`) AS `jumlah_barang`,coalesce(sum(`i`.`jumlah`),0) AS `total_stok`,`g`.`keterangan` AS `keterangan` from (`gudang` `g` left join `inventory_gudang` `i` on((`g`.`id_gudang` = `i`.`id_gudang`))) group by `g`.`id_gudang`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
