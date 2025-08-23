-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 23, 2025 at 02:28 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_amarta_wisesa`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id_absensi` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status_hadir` enum('hadir','izin','sakit','alpha','cuti') DEFAULT 'hadir',
  `keterangan` text DEFAULT NULL,
  `lembur_jam` decimal(4,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departemen`
--

CREATE TABLE `departemen` (
  `id_departemen` int(11) NOT NULL,
  `nama_departemen` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departemen`
--

INSERT INTO `departemen` (`id_departemen`, `nama_departemen`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Produksi', 'Departemen yang menangani proses produksi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(2, 'Quality Assurance', 'Departemen yang menangani kontrol kualitas', '2025-08-18 15:13:05', '2025-08-22 15:43:18'),
(3, 'Gudang', 'Departemen yang menangani penyimpanan dan distribusi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(4, 'HR & GA', 'Departemen yang menangani sumber daya manusia', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(5, 'Finance', 'Departemen yang menangani keuangan', '2025-08-18 15:13:05', '2025-08-18 15:13:05');

-- --------------------------------------------------------

--
-- Table structure for table `gaji`
--

CREATE TABLE `gaji` (
  `id_gaji` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `periode_bulan` int(11) NOT NULL,
  `periode_tahun` int(11) NOT NULL,
  `gaji_pokok` decimal(12,2) DEFAULT 0.00,
  `tunjangan` decimal(12,2) DEFAULT 0.00,
  `lembur` decimal(12,2) DEFAULT 0.00,
  `potongan` decimal(12,2) DEFAULT 0.00,
  `total_gaji` decimal(12,2) NOT NULL,
  `status` enum('draft','disetujui','dibayar') DEFAULT 'draft',
  `tanggal_bayar` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gudang`
--

CREATE TABLE `gudang` (
  `id_gudang` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `kepala_gudang` varchar(100) NOT NULL,
  `kapasitas` int(11) NOT NULL COMMENT 'Kapasitas dalam meter persegi',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gudang`
--

INSERT INTO `gudang` (`id_gudang`, `nama`, `alamat`, `kepala_gudang`, `kapasitas`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Probolinggo', 'Jl. Probolinggo', 'Admin Probolinggo', 5000, 'Gudang utama untuk penyimpanan bahan baku', '2025-08-20 03:28:41', '2025-08-21 03:57:55'),
(2, 'Pasuruan', 'Jl. Pasuruan', 'Admin Pasuruan', 3000, 'Gudang untuk produk jadi dan finishing', '2025-08-20 03:28:41', '2025-08-21 03:57:19'),
(3, 'Kasin', 'Jl. Brigjend. Katamso No.48-50, Kauman, Kec. Klojen, Kota Malang, Jawa Timur 65119', 'Stefanus', 4000, 'Gudang khusus untuk bahan baku tekstil', '2025-08-20 03:28:41', '2025-08-21 03:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `hpp`
--

CREATE TABLE `hpp` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_persiapan` int(11) UNSIGNED NOT NULL,
  `no_urut` int(11) DEFAULT NULL,
  `bahan` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `barang_jadi` int(11) DEFAULT NULL,
  `stok_order` int(11) DEFAULT NULL,
  `efisiensi_consp` decimal(10,2) DEFAULT NULL,
  `efisiensi_rap` decimal(10,2) DEFAULT NULL,
  `stok_material` int(11) DEFAULT NULL,
  `po` int(11) DEFAULT NULL,
  `harga_per_meter` decimal(15,2) DEFAULT NULL,
  `rap_x_harga_per_m` decimal(15,2) DEFAULT NULL,
  `total_harga_bahan` decimal(15,2) DEFAULT NULL,
  `biaya_tenaga_kerja_per_qty` decimal(15,2) DEFAULT NULL,
  `total_biaya_tenaga_kerja` decimal(15,2) DEFAULT NULL,
  `listrik` decimal(15,2) DEFAULT NULL,
  `air` decimal(15,2) DEFAULT NULL,
  `overhead` decimal(15,2) DEFAULT NULL,
  `total_biaya` decimal(15,2) DEFAULT NULL,
  `hpp` decimal(15,2) DEFAULT NULL,
  `profit` decimal(5,2) DEFAULT NULL,
  `harga_jual` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hpp`
--

INSERT INTO `hpp` (`id`, `id_persiapan`, `no_urut`, `bahan`, `qty`, `barang_jadi`, `stok_order`, `efisiensi_consp`, `efisiensi_rap`, `stok_material`, `po`, `harga_per_meter`, `rap_x_harga_per_m`, `total_harga_bahan`, `biaya_tenaga_kerja_per_qty`, `total_biaya_tenaga_kerja`, `listrik`, `air`, `overhead`, `total_biaya`, `hpp`, `profit`, `harga_jual`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Kain Katun', 100, 95, 100, '1.05', '1.10', 105, 1001, '25000.00', '27500.00', '2750000.00', '5000.00', '500000.00', '200000.00', '100000.00', '300000.00', '3550000.00', '35500.00', '30.00', '46150.00', '2025-08-23 10:33:43', '2025-08-23 10:33:43'),
(2, 1, 2, 'Benang', 100, 95, 100, '1.02', '1.05', 102, 1002, '5000.00', '5250.00', '525000.00', '1000.00', '100000.00', '50000.00', '25000.00', '75000.00', '725000.00', '7250.00', '25.00', '9062.50', '2025-08-23 10:33:43', '2025-08-23 10:33:43');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(50) DEFAULT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `warehouse` varchar(100) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `harga_per_unit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `id_kategori` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `kode_barang`, `nama_barang`, `warehouse`, `unit`, `jumlah`, `harga_per_unit`, `keterangan`, `created_at`, `updated_at`, `id_kategori`) VALUES
(8, NULL, 'Kain Flanel', 'Kasin', 'm²', 783, '150000.00', 'Beli untuk Kebutuhan Project Jakarta', '2025-08-21 10:59:11', NULL, NULL),
(9, NULL, 'Kancing', 'Kasin', 'pcs', 95000, '250.00', 'Kebutuhan Project Jakarta', '2025-08-21 11:00:12', NULL, NULL),
(10, NULL, 'Kancing', 'Probolinggo', 'pcs', 78000, '250.00', 'Stock Opname', '2025-08-21 11:04:00', NULL, NULL),
(11, NULL, 'Benang Polyester', 'Probolinggo', 'kg', 20, '100000.00', '', '2025-08-21 21:37:32', NULL, NULL),
(12, NULL, 'Kancing Besar', 'Kasin', 'pcs', 5000000, '10.00', '', '2025-08-21 23:17:59', NULL, NULL),
(13, NULL, 'Jas Almamater', 'Pasuruan', 'pcs', 2000, '200000.00', '', '2025-08-21 23:41:06', NULL, NULL),
(14, NULL, 'Kain Flanel', 'Kasin', 'm²', 100, '100000.00', '', '2025-08-22 14:16:42', NULL, NULL),
(16, NULL, 'Kain Flanel', 'Kasin', 'm²', 100, '100000.00', '', '2025-08-22 15:30:32', NULL, NULL),
(17, 'BRG005', 'Kain Katun', 'Kasin', 'm²', 100000, '100000.00', '', '2025-08-22 15:32:30', '2025-08-22 19:50:54', NULL),
(20, 'BRG002', 'Kain Perca', 'Kasin', 'm²', 1000, '50000.00', '', '2025-08-22 18:34:38', NULL, NULL),
(21, 'BRG006', 'Kain Perca', 'Kasin', 'm²', 10, '55000.00', '', '2025-08-23 08:47:58', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_gudang`
--

CREATE TABLE `inventory_gudang` (
  `id_inventory` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `stok_akhir` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(20) NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_gudang`
--

INSERT INTO `inventory_gudang` (`id_inventory`, `id_gudang`, `nama_barang`, `jumlah`, `stok_akhir`, `satuan`, `tanggal_masuk`, `tanggal_update`) VALUES
(1, 1, 'Kain Katun Premium', 500, 500, 'roll', '2024-01-15', '2025-08-21 15:51:45'),
(2, 1, 'Benang Polyester', 220, 240, 'kg', '2024-01-20', '2025-08-21 16:38:23'),
(3, 2, 'Kaos Polos L', 1000, 1000, 'pcs', '2024-01-25', '2025-08-21 15:51:45'),
(4, 2, 'Celana Jeans M', 300, 300, 'pcs', '2024-01-28', '2025-08-21 15:51:45'),
(5, 3, 'Kain Katun', 200200, 300400, 'meter', '2025-08-21', '2025-08-22 09:15:39'),
(6, 2, 'Jas Almamater', 7000, 14000, 'pcs', '2025-08-21', '2025-08-21 16:41:06'),
(7, 3, 'Kain Flanel', 983, 1966, 'm²', '2025-08-21', '2025-08-22 08:30:32'),
(8, 3, 'Kancing', 95000, 190000, 'pcs', '2025-08-21', '2025-08-21 16:38:23'),
(9, 1, 'Kancing', 78000, 156000, 'pcs', '2025-08-21', '2025-08-21 16:38:23'),
(10, 3, 'Kancing Besar', 5000000, 10000000, 'pcs', '2025-08-21', '2025-08-21 16:38:23'),
(11, 3, 'Kain Perca', 1010, 2020, 'm²', '2025-08-22', '2025-08-23 01:47:58');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transaksi`
--

CREATE TABLE `inventory_transaksi` (
  `id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `jumlah_masuk` int(11) DEFAULT 0,
  `jumlah_keluar` int(11) DEFAULT 0,
  `harga_per_unit` decimal(15,2) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_transaksi` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transaksi_gudang`
--

CREATE TABLE `inventory_transaksi_gudang` (
  `id` int(11) NOT NULL,
  `inventory_gudang_id` int(11) NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `jumlah_masuk` int(11) DEFAULT 0,
  `jumlah_keluar` int(11) DEFAULT 0,
  `harga_per_unit` decimal(15,2) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_transaksi` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_transaksi_gudang`
--

INSERT INTO `inventory_transaksi_gudang` (`id`, `inventory_gudang_id`, `jenis`, `jumlah_masuk`, `jumlah_keluar`, `harga_per_unit`, `keterangan`, `tanggal_transaksi`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 1, 'masuk', 500, 0, NULL, 'Stok awal dari inventory_gudang', '2024-01-15 00:00:00', NULL, '2025-08-21 16:07:27', '2025-08-21 16:07:27'),
(3, 2, 'masuk', 200, 0, NULL, 'Stok awal dari inventory_gudang', '2024-01-20 00:00:00', NULL, '2025-08-21 16:07:27', '2025-08-21 16:07:27'),
(4, 3, 'masuk', 1000, 0, NULL, 'Stok awal dari inventory_gudang', '2024-01-25 00:00:00', NULL, '2025-08-21 16:07:27', '2025-08-21 16:07:27'),
(5, 4, 'masuk', 300, 0, NULL, 'Stok awal dari inventory_gudang', '2024-01-28 00:00:00', NULL, '2025-08-21 16:07:27', '2025-08-21 16:07:27'),
(6, 5, 'masuk', 100000, 0, NULL, 'Stok awal dari inventory_gudang', '2025-08-21 00:00:00', NULL, '2025-08-21 16:07:27', '2025-08-21 16:07:27'),
(9, 1, 'masuk', 500, 0, NULL, 'Stok awal dari inventory_gudang', '2024-01-15 00:00:00', NULL, '2025-08-21 16:10:56', '2025-08-21 16:10:56'),
(10, 2, 'masuk', 200, 0, NULL, 'Stok awal dari inventory_gudang', '2024-01-20 00:00:00', NULL, '2025-08-21 16:10:56', '2025-08-21 16:10:56'),
(11, 3, 'masuk', 1000, 0, NULL, 'Stok awal dari inventory_gudang', '2024-01-25 00:00:00', NULL, '2025-08-21 16:10:56', '2025-08-21 16:10:56'),
(12, 4, 'masuk', 300, 0, NULL, 'Stok awal dari inventory_gudang', '2024-01-28 00:00:00', NULL, '2025-08-21 16:10:56', '2025-08-21 16:10:56'),
(13, 5, 'masuk', 100000, 0, NULL, 'Stok awal dari inventory_gudang', '2025-08-21 00:00:00', NULL, '2025-08-21 16:10:56', '2025-08-21 16:10:56'),
(16, 6, 'masuk', 5000, 0, NULL, 'Stok awal masuk', '2025-08-21 00:00:00', NULL, '2025-08-21 16:16:37', '2025-08-21 16:16:37'),
(17, 7, 'masuk', 783, 0, NULL, 'Migrasi data dari inventory lama - Beli untuk Kebutuhan Project Jakarta', '2025-08-21 10:59:11', NULL, '2025-08-21 16:38:23', '2025-08-21 16:38:23'),
(18, 8, 'masuk', 95000, 0, NULL, 'Migrasi data dari inventory lama - Kebutuhan Project Jakarta', '2025-08-21 11:00:12', NULL, '2025-08-21 16:38:23', '2025-08-21 16:38:23'),
(19, 9, 'masuk', 78000, 0, NULL, 'Migrasi data dari inventory lama - Stock Opname', '2025-08-21 11:04:00', NULL, '2025-08-21 16:38:23', '2025-08-21 16:38:23'),
(20, 2, 'masuk', 20, 0, NULL, 'Migrasi data dari inventory lama - ', '2025-08-21 21:37:32', NULL, '2025-08-21 16:38:23', '2025-08-21 16:38:23'),
(21, 10, 'masuk', 5000000, 0, NULL, 'Migrasi data dari inventory lama - ', '2025-08-21 23:17:59', NULL, '2025-08-21 16:38:23', '2025-08-21 16:38:23'),
(22, 6, 'masuk', 2000, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-21 23:41:06', NULL, '2025-08-21 16:41:06', '2025-08-21 16:41:06'),
(23, 7, 'masuk', 100, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-22 14:16:42', NULL, '2025-08-22 07:16:42', '2025-08-22 07:16:42'),
(24, 7, 'masuk', 100, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-22 15:30:32', NULL, '2025-08-22 08:30:32', '2025-08-22 08:30:32'),
(25, 5, 'masuk', 100000, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-22 15:32:30', NULL, '2025-08-22 08:32:30', '2025-08-22 08:32:30'),
(26, 5, 'masuk', 200, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-22 16:15:39', NULL, '2025-08-22 09:15:39', '2025-08-22 09:15:39'),
(27, 11, 'masuk', 1000, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-22 18:34:38', NULL, '2025-08-22 11:34:38', '2025-08-22 11:34:38'),
(28, 11, 'masuk', 10, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-23 08:47:58', NULL, '2025-08-23 01:47:58', '2025-08-23 01:47:58');

--
-- Triggers `inventory_transaksi_gudang`
--
DELIMITER $$
CREATE TRIGGER `update_stok_after_delete_transaksi` AFTER DELETE ON `inventory_transaksi_gudang` FOR EACH ROW BEGIN
  IF OLD.jenis = 'masuk' THEN
    UPDATE `inventory_gudang` 
    SET `stok_akhir` = `stok_akhir` - OLD.jumlah_masuk 
    WHERE `id_inventory` = OLD.inventory_gudang_id;
  ELSEIF OLD.jenis = 'keluar' THEN
    UPDATE `inventory_gudang` 
    SET `stok_akhir` = `stok_akhir` + OLD.jumlah_keluar 
    WHERE `id_inventory` = OLD.inventory_gudang_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_stok_after_insert_transaksi` AFTER INSERT ON `inventory_transaksi_gudang` FOR EACH ROW BEGIN
  IF NEW.jenis = 'masuk' THEN
    UPDATE `inventory_gudang` 
    SET `stok_akhir` = `stok_akhir` + NEW.jumlah_masuk 
    WHERE `id_inventory` = NEW.inventory_gudang_id;
  ELSEIF NEW.jenis = 'keluar' THEN
    UPDATE `inventory_gudang` 
    SET `stok_akhir` = `stok_akhir` - NEW.jumlah_keluar 
    WHERE `id_inventory` = NEW.inventory_gudang_id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_stok_after_update_transaksi` AFTER UPDATE ON `inventory_transaksi_gudang` FOR EACH ROW BEGIN
  -- Revert old transaction
  IF OLD.jenis = 'masuk' THEN
    UPDATE `inventory_gudang` 
    SET `stok_akhir` = `stok_akhir` - OLD.jumlah_masuk 
    WHERE `id_inventory` = OLD.inventory_gudang_id;
  ELSEIF OLD.jenis = 'keluar' THEN
    UPDATE `inventory_gudang` 
    SET `stok_akhir` = `stok_akhir` + OLD.jumlah_keluar 
    WHERE `id_inventory` = OLD.inventory_gudang_id;
  END IF;
  
  -- Apply new transaction
  IF NEW.jenis = 'masuk' THEN
    UPDATE `inventory_gudang` 
    SET `stok_akhir` = `stok_akhir` + NEW.jumlah_masuk 
    WHERE `id_inventory` = NEW.inventory_gudang_id;
  ELSEIF NEW.jenis = 'keluar' THEN
    UPDATE `inventory_gudang` 
    SET `stok_akhir` = `stok_akhir` - NEW.jumlah_keluar 
    WHERE `id_inventory` = NEW.inventory_gudang_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id_jabatan` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id_jabatan`, `nama_jabatan`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Operator Produksi', 'Karyawan yang bekerja di lini produksi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(2, 'Supervisor Produksi', 'Mengawasi dan mengkoordinir lini produksi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(3, 'Quality Control', 'Memastikan kualitas produk sesuai standar', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(4, 'Admin Gudang', 'Mengelola stok dan administrasi gudang', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(5, 'HR Manager', 'Mengelola sumber daya manusia', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(6, 'Finance', 'Mengelola keuangan dan gaji karyawan', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(7, 'Pekerja', 'Orang yang bekerja Lepas atau Pekerja Harian atau Borongan.', '2025-08-18 15:41:53', '2025-08-18 15:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(11) NOT NULL,
  `nik` varchar(200) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `type_karyawan` enum('tetap','harian','borongan') NOT NULL,
  `status_karyawan` enum('aktif','nonaktif','cuti','keluar') DEFAULT 'aktif',
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `id_departemen` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nik`, `nama_lengkap`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telepon`, `email`, `type_karyawan`, `status_karyawan`, `tanggal_masuk`, `tanggal_keluar`, `id_jabatan`, `id_departemen`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'K001', 'Budi Santoso', 'L', 'Jakarta', '1985-05-15', 'Jl. Merdeka No. 123', '081234567890', 'budi@amarta.com', 'tetap', 'aktif', '2020-01-15', NULL, 2, 3, NULL, '2025-08-18 15:13:05', '2025-08-22 15:03:54'),
(2, 'K002', 'Siti Nurhaliza', 'P', 'Bandung', '1990-08-20', 'Jl. Sudirman No. 45', '082345678901', 'siti@amarta.com', 'tetap', 'aktif', '2019-03-10', NULL, 5, 4, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(3, 'KH001', 'Ahmad Dahlan', 'L', 'Yogyakarta', '1995-12-10', 'Jl. Malioboro No. 78', '083456789012', 'ahmad@amarta.com', 'harian', 'aktif', '2023-06-01', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(4, 'KH002', 'Rina Marlina', 'P', 'Medan', '1998-03-25', 'Jl. Thamrin No. 90', '084567890123', 'rina@amarta.com', 'harian', 'aktif', '2023-07-15', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(5, 'KB001', 'Joko Widodo', 'L', 'Solo', '1992-07-30', 'Jl. Slamet Riyadi No. 56', '085678901234', 'joko@amarta.com', 'borongan', 'aktif', '2023-08-01', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(8, '12345678', 'Resky Putra P', 'L', 'Kota Depok', '1999-08-08', 'Dimun', '083456789012', 'androexe5@gmail.com', 'tetap', 'aktif', '2025-08-18', NULL, 7, 1, NULL, '2025-08-18 16:59:04', '2025-08-19 06:42:44');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan_harian_borongan`
--

CREATE TABLE `karyawan_harian_borongan` (
  `id_karyawan` int(11) NOT NULL,
  `upah_per_hari` decimal(10,2) DEFAULT 0.00,
  `upah_per_jam` decimal(10,2) DEFAULT 0.00,
  `upah_borongan` decimal(12,2) DEFAULT 0.00,
  `metode_pembayaran` enum('harian','borongan','mingguan') DEFAULT 'harian',
  `rekening_bank` varchar(50) DEFAULT NULL,
  `nama_bank` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan_harian_borongan`
--

INSERT INTO `karyawan_harian_borongan` (`id_karyawan`, `upah_per_hari`, `upah_per_jam`, `upah_borongan`, `metode_pembayaran`, `rekening_bank`, `nama_bank`) VALUES
(3, '150000.00', '0.00', '0.00', 'harian', NULL, NULL),
(4, '150000.00', '0.00', '0.00', 'harian', NULL, NULL),
(5, '0.00', '0.00', '2500000.00', 'borongan', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `karyawan_tetap`
--

CREATE TABLE `karyawan_tetap` (
  `id_karyawan` int(11) NOT NULL,
  `npwp` varchar(20) DEFAULT NULL,
  `bpjs_ketenagakerjaan` varchar(30) DEFAULT NULL,
  `bpjs_kesehatan` varchar(30) DEFAULT NULL,
  `gaji_pokok` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tunjangan_jabatan` decimal(12,2) DEFAULT 0.00,
  `tunjangan_transport` decimal(12,2) DEFAULT 0.00,
  `tunjangan_makan` decimal(12,2) DEFAULT 0.00,
  `rekening_bank` varchar(50) DEFAULT NULL,
  `nama_bank` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan_tetap`
--

INSERT INTO `karyawan_tetap` (`id_karyawan`, `npwp`, `bpjs_ketenagakerjaan`, `bpjs_kesehatan`, `gaji_pokok`, `tunjangan_jabatan`, `tunjangan_transport`, `tunjangan_makan`, `rekening_bank`, `nama_bank`) VALUES
(1, '123456789012345', 'BPJS-TK001', 'BPJS-KS001', '5000000.00', '1500000.00', '500000.00', '300000.00', NULL, NULL),
(2, '987654321098765', 'BPJS-TK002', 'BPJS-KS002', '7000000.00', '2000000.00', '750000.00', '500000.00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_barang`
--

INSERT INTO `kategori_barang` (`id_kategori`, `nama_kategori`, `keterangan`, `created_at`, `updated_at`) VALUES
(2, 'Benang', 'Kategori untuk berbagai jenis benang', '2025-08-22 07:47:00', '2025-08-22 07:47:00'),
(6, 'Kain Flanel', '', '2025-08-22 08:29:56', '2025-08-22 08:29:56'),
(7, 'Kain Katun', '', '2025-08-22 08:31:53', '2025-08-22 08:31:53'),
(8, 'Kain Perca', '', '2025-08-22 08:49:03', '2025-08-22 08:49:03');

-- --------------------------------------------------------

--
-- Table structure for table `mesin`
--

CREATE TABLE `mesin` (
  `id_mesin` int(11) NOT NULL,
  `seri_number` varchar(50) NOT NULL,
  `nama_mesin` varchar(100) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mesin`
--

INSERT INTO `mesin` (`id_mesin`, `seri_number`, `nama_mesin`, `lokasi`, `tanggal_masuk`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'SN-001', 'Mesin Jahit Singer', 'Line A - Station 1', '2024-01-15', 'Mesin jahit untuk produksi kaos', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
(3, 'SN-003', 'Mesin Bordir Komputer', 'Line B - Station 1', '2024-02-01', 'Mesin bordir digital 10 kepala', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
(4, 'SN-004', 'Mesin Cutting Laser', 'Area Cutting', '2024-02-10', 'Mesin cutting presisi untuk kain', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
(5, 'SN-005', 'Mesin Press Kaos', 'Area Finishing', '2024-02-15', 'Mesin press untuk sablon digital', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
(11, 'SN-006', 'Mesin Jahit', 'Warehouse', '2025-08-19', 'mesin ya', '2025-08-19 03:32:14', '2025-08-19 03:42:03');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `id_karyawan` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','hr','karyawan') DEFAULT 'karyawan',
  `status_aktif` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `id_karyawan`, `username`, `password`, `role`, `status_aktif`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(2, 2, 'hr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hr', 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05');

-- --------------------------------------------------------

--
-- Table structure for table `persiapan`
--

CREATE TABLE `persiapan` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_sps` int(11) NOT NULL,
  `spp_no` varchar(50) NOT NULL DEFAULT '',
  `kode_barang` varchar(50) DEFAULT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `tanggal_persiapan` date NOT NULL,
  `pola` varchar(255) DEFAULT NULL,
  `marker` varchar(255) DEFAULT NULL,
  `upload_spk` varchar(255) DEFAULT NULL,
  `status` enum('pending','proses','selesai','batal') DEFAULT 'pending',
  `sp_srx` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `persiapan`
--

INSERT INTO `persiapan` (`id`, `id_sps`, `spp_no`, `kode_barang`, `nama_barang`, `jumlah`, `satuan`, `harga`, `total`, `tanggal_persiapan`, `pola`, `marker`, `upload_spk`, `status`, `sp_srx`, `created_at`, `updated_at`) VALUES
(1, 3, '', '123', 'Kain Biru', 500, 'meter', '10.00', '5000.00', '2025-08-27', NULL, NULL, NULL, 'pending', '', '2025-08-20 14:45:29', '2025-08-20 14:45:29'),
(2, 4, '', '123', 'T-Shirt', 900, 'meter', '120000.00', '5000.00', '2025-08-30', '1755752875_FNS- KTD SH S M 01(KATADATA MEN)-PROD.hpg', '1755752875_FNS- KTD SH S M 01(KATADATA MEN)-PROD.hpg', '1755754247_Surat Perintah Kerja (SPK).pdf', 'proses', 'SPK0001', '2025-08-21 09:42:15', '2025-08-22 14:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `sps`
--

CREATE TABLE `sps` (
  `id` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `sps_no` varchar(20) DEFAULT NULL,
  `customer` varchar(100) DEFAULT NULL,
  `item` varchar(100) DEFAULT NULL,
  `artikel` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `sample_product` varchar(255) DEFAULT NULL,
  `design` varchar(255) DEFAULT NULL,
  `st_chart` varchar(255) DEFAULT NULL,
  `material_sm` varchar(255) DEFAULT NULL,
  `pola_sample` varchar(255) DEFAULT NULL,
  `buat_sample` varchar(255) DEFAULT NULL,
  `kirim` date DEFAULT NULL,
  `approval` varchar(100) DEFAULT NULL,
  `sp_srx` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sps`
--

INSERT INTO `sps` (`id`, `tanggal`, `sps_no`, `customer`, `item`, `artikel`, `qty`, `size`, `sample_product`, `design`, `st_chart`, `material_sm`, `pola_sample`, `buat_sample`, `kirim`, `approval`, `sp_srx`) VALUES
(2, '2025-08-19', 'SPS-01', 'Resky-jakarta', 'T-Shirt', 'T-Shirt', 2, 'L', '1755572545_68a3e9415efd7_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '2025-08-26', 'Approved', 'SPK-20250820-0002'),
(3, '2025-08-20', 'SPS-02', 'Joko-Jakarta', 'Kemeja', 'Kemeja', 1, 'M', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-27', 'Approved', ''),
(4, '2025-08-21', 'SPS-04', 'Aji', 'T-Shirt', 'T-Shirt', 1, 'M', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-30', 'Approved', 'SPK-20250821-0004');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD UNIQUE KEY `unique_absensi` (`id_karyawan`,`tanggal`);

--
-- Indexes for table `departemen`
--
ALTER TABLE `departemen`
  ADD PRIMARY KEY (`id_departemen`);

--
-- Indexes for table `gaji`
--
ALTER TABLE `gaji`
  ADD PRIMARY KEY (`id_gaji`),
  ADD UNIQUE KEY `unique_gaji` (`id_karyawan`,`periode_bulan`,`periode_tahun`);

--
-- Indexes for table `gudang`
--
ALTER TABLE `gudang`
  ADD PRIMARY KEY (`id_gudang`),
  ADD KEY `idx_nama` (`nama`),
  ADD KEY `idx_kepala_gudang` (`kepala_gudang`);

--
-- Indexes for table `hpp`
--
ALTER TABLE `hpp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hpp_persiapan` (`id_persiapan`);

--
-- Indexes for table `persiapan`
--
ALTER TABLE `persiapan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_persiapan_sps` (`id_sps`);

--
-- Indexes for table `sps`
--
ALTER TABLE `sps`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hpp`
--
ALTER TABLE `hpp`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `persiapan`
--
ALTER TABLE `persiapan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hpp`
--
ALTER TABLE `hpp`
  ADD CONSTRAINT `fk_hpp_persiapan` FOREIGN KEY (`id_persiapan`) REFERENCES `persiapan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `persiapan`
--
ALTER TABLE `persiapan`
  ADD CONSTRAINT `fk_persiapan_sps` FOREIGN KEY (`id_sps`) REFERENCES `sps` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
