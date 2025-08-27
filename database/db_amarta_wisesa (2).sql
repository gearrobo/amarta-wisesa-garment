-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 27, 2025 at 01:59 PM
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
  `id` int(11) NOT NULL,
  `nama_departemen` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departemen`
--

INSERT INTO `departemen` (`id`, `nama_departemen`, `deskripsi`, `created_at`, `updated_at`) VALUES
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
  `id` int(11) NOT NULL,
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

INSERT INTO `gudang` (`id`, `nama`, `alamat`, `kepala_gudang`, `kapasitas`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Probolinggo', 'Jl. Probolinggo', 'Admin Probolinggo', 5000, 'Gudang utama untuk penyimpanan bahan baku', '2025-08-20 03:28:41', '2025-08-21 03:57:55'),
(2, 'Pasuruan', 'Jl. Pasuruan', 'Admin Pasuruan', 3000, 'Gudang untuk produk jadi dan finishing', '2025-08-20 03:28:41', '2025-08-21 03:57:19'),
(3, 'Kasin', 'Jl. Brigjend. Katamso No.48-50, Kauman, Kec. Klojen, Kota Malang, Jawa Timur 65119', 'Stefanus', 4000, 'Gudang khusus untuk bahan baku tekstil', '2025-08-20 03:28:41', '2025-08-21 03:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `gudang_backup`
--

CREATE TABLE `gudang_backup` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kepala_gudang` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kapasitas` int(11) NOT NULL COMMENT 'Kapasitas dalam meter persegi',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gudang_backup`
--

INSERT INTO `gudang_backup` (`id`, `nama`, `alamat`, `kepala_gudang`, `kapasitas`, `keterangan`, `created_at`, `updated_at`) VALUES
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
  `id_inventory` int(11) UNSIGNED DEFAULT NULL,
  `no_urut` varchar(50) DEFAULT NULL,
  `bahan` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `barang_jadi` int(11) DEFAULT NULL,
  `stok_order` int(11) DEFAULT NULL,
  `efisiensi_consp` decimal(10,2) DEFAULT NULL,
  `efisiensi_rap` int(50) DEFAULT NULL,
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

INSERT INTO `hpp` (`id`, `id_persiapan`, `id_inventory`, `no_urut`, `bahan`, `qty`, `barang_jadi`, `stok_order`, `efisiensi_consp`, `efisiensi_rap`, `stok_material`, `po`, `harga_per_meter`, `rap_x_harga_per_m`, `total_harga_bahan`, `biaya_tenaga_kerja_per_qty`, `total_biaya_tenaga_kerja`, `listrik`, `air`, `overhead`, `total_biaya`, `hpp`, `profit`, `harga_jual`, `created_at`, `updated_at`) VALUES
(10, 2, 13, '1', 'Kain Katun', 1000, 980, 20, '1.00', 980, 100, 880, '100000.00', '98000000.00', '98000000000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '98000000000.00', '98000000.00', '30.00', '127400000.00', '2025-08-25 04:22:27', '2025-08-25 04:22:27'),
(11, 2, 14, '2', 'Benang', 1000, 20, 980, '0.50', 490, 50, 440, '100000.00', '49000000.00', '49000000000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '49000000000.00', '49000000.00', '30.00', '63700000.00', '2025-08-25 04:29:01', '2025-08-25 04:29:01'),
(17, 3, 17, '1', 'Kain Faring', 1000, 20, 980, '1.00', 980, 50, 930, '85000.00', '83300000.00', '83300000000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '83300000000.00', '83300000.00', '30.00', '108290000.00', '2025-08-25 23:55:06', '2025-08-25 23:55:06'),
(20, 8, 15, 'HPP68A9CB49B578C', 'Jarum', 50, 0, 50, '1.00', 50, 50, 0, '100000.00', '5000000.00', '250000000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '250000000.00', '5000000.00', '30.00', '6500000.00', '2025-08-27 06:33:57', '2025-08-27 06:33:57');

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
(5, 'SN-09XXPi-90', 'Kain Katun', 'Kasin', 'meter', 50, '100000.00', '', '2025-08-24 19:38:04', '2025-08-25 10:11:42', NULL),
(6, 'SN-09XXPi-80', 'Benang', 'Kasin', 'roll', 50, '100000.00', '', '2025-08-24 19:41:57', '2025-08-25 10:11:30', NULL),
(7, 'SN-09XXPi-70', 'Jarum', 'Kasin', 'pcs', 50, '100000.00', '', '2025-08-24 19:42:37', '2025-08-25 10:10:26', NULL),
(8, 'SN-09XXPi-90', 'Kain Katun', 'Kasin', 'meter', 5, '100000.00', '', '2025-08-25 10:12:43', '2025-08-25 10:13:20', NULL),
(9, 'SN-09XXPi-90', 'Kain Katun', 'Kasin', 'meter', 100, '10000.00', '', '2025-08-25 11:20:51', NULL, NULL),
(12, 'SN-09XXPi-60', 'Kain Faring', 'Kasin', 'meter', 50, '85000.00', '', '2025-08-25 16:40:12', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_gudang`
--

CREATE TABLE `inventory_gudang` (
  `id` int(11) UNSIGNED NOT NULL,
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

INSERT INTO `inventory_gudang` (`id`, `id_inventory`, `id_gudang`, `nama_barang`, `jumlah`, `stok_akhir`, `satuan`, `tanggal_masuk`, `tanggal_update`) VALUES
(13, 5, 3, 'Kain Katun', 0, 0, 'meter', '2025-08-24', '2025-08-25 04:22:27'),
(14, 6, 3, 'Benang', 0, 0, 'roll', '2025-08-24', '2025-08-25 04:29:01'),
(15, 7, 3, 'Jarum', 0, 0, 'pcs', '2025-08-24', '2025-08-27 06:33:57'),
(17, 12, 3, 'Kain Faring', 0, 0, 'meter', '2025-08-25', '2025-08-25 23:55:06');

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
(1, 13, 'masuk', 5000, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-24 19:38:04', NULL, '2025-08-24 12:38:04', '2025-08-24 12:38:04'),
(2, 14, 'masuk', 5000, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-24 19:41:57', NULL, '2025-08-24 12:41:57', '2025-08-24 12:41:57'),
(3, 15, 'masuk', 5000, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-24 19:42:37', NULL, '2025-08-24 12:42:37', '2025-08-24 12:42:37'),
(4, 13, 'masuk', 10, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-25 10:12:43', NULL, '2025-08-25 03:12:43', '2025-08-25 03:12:43'),
(5, 13, 'keluar', 0, 1000, NULL, 'Pengurangan stok untuk HPP ID: 8 - Kain Katun', '2025-08-25 10:50:22', NULL, '2025-08-25 03:50:22', '2025-08-25 03:50:22'),
(6, 13, 'keluar', 0, 55, NULL, 'Pengurangan stok untuk HPP ID: 9 - Kain Katun', '2025-08-25 11:12:32', NULL, '2025-08-25 04:12:32', '2025-08-25 04:12:32'),
(7, 13, 'keluar', 0, 1000, NULL, 'Pengurangan stok untuk HPP ID: 9 - Kain Katun', '2025-08-25 11:12:32', NULL, '2025-08-25 04:12:32', '2025-08-25 04:12:32'),
(8, 13, 'masuk', 100, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-25 11:20:51', NULL, '2025-08-25 04:20:51', '2025-08-25 04:20:51'),
(9, 13, 'keluar', 0, 100, NULL, 'Pengurangan stok untuk HPP ID: 10 - Kain Katun', '2025-08-25 11:22:27', NULL, '2025-08-25 04:22:27', '2025-08-25 04:22:27'),
(10, 14, 'keluar', 0, 50, NULL, 'Pengurangan stok untuk HPP ID: 11 - Benang', '2025-08-25 11:29:01', NULL, '2025-08-25 04:29:01', '2025-08-25 04:29:01'),
(11, 16, 'masuk', 100, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-25 15:49:35', NULL, '2025-08-25 08:49:35', '2025-08-25 08:49:35'),
(12, 16, 'masuk', 50, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-25 15:50:53', NULL, '2025-08-25 08:50:53', '2025-08-25 08:50:53'),
(13, 17, 'masuk', 50, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-25 16:40:12', NULL, '2025-08-25 09:40:12', '2025-08-25 09:40:12'),
(14, 17, 'masuk', 20, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-25 16:40:44', NULL, '2025-08-25 09:40:44', '2025-08-25 09:40:44'),
(15, 15, 'keluar', 0, 50, NULL, 'Pengurangan stok untuk HPP ID: 12 - Jarum', '2025-08-26 06:26:40', NULL, '2025-08-25 23:26:40', '2025-08-25 23:26:40'),
(16, 15, 'masuk', 50, 0, NULL, 'Pengembalian stok dari penghapusan HPP ID: 12 - Jarum', '2025-08-26 06:27:11', NULL, '2025-08-25 23:27:11', '2025-08-25 23:27:11'),
(17, 15, 'keluar', 0, 50, NULL, 'Pengurangan stok untuk HPP ID: 15 - Jarum', '2025-08-26 06:27:56', NULL, '2025-08-25 23:27:56', '2025-08-25 23:27:56'),
(18, 15, 'masuk', 50, 0, NULL, 'Pengembalian stok dari penghapusan HPP ID: 15 - Jarum', '2025-08-26 06:35:23', NULL, '2025-08-25 23:35:23', '2025-08-25 23:35:23'),
(19, 15, 'keluar', 0, 40, NULL, 'Pengurangan stok untuk HPP ID: 16 - Jarum', '2025-08-26 06:36:06', NULL, '2025-08-25 23:36:06', '2025-08-25 23:36:06'),
(20, 15, 'masuk', 40, 0, NULL, 'Pengembalian stok dari penghapusan HPP ID: 16 - Jarum', '2025-08-26 06:52:43', NULL, '2025-08-25 23:52:43', '2025-08-25 23:52:43'),
(21, 17, 'keluar', 0, 50, NULL, 'Pengurangan stok untuk HPP ID: 17 - Kain Faring', '2025-08-26 06:55:06', NULL, '2025-08-25 23:55:06', '2025-08-25 23:55:06'),
(22, 15, 'keluar', 0, 50, NULL, 'Pengurangan stok untuk HPP ID: 20 - Jarum', '2025-08-27 13:33:57', NULL, '2025-08-27 06:33:57', '2025-08-27 06:33:57');

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
  `id` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id`, `nama_jabatan`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Operator Produksi', 'Karyawan yang bekerja di lini produksi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(2, 'Supervisor Produksi', 'Mengawasi dan mengkoordinir lini produksi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(3, 'Quality Control', 'Memastikan kualitas produk sesuai standar', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(4, 'Admin Gudang', 'Mengelola stok dan administrasi gudang', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(5, 'HR Manager', 'Mengelola sumber daya manusia', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(6, 'Finance', 'Mengelola keuangan dan gaji karyawan', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(7, 'Pekerja', 'Orang yang bekerja Lepas atau Pekerja Harian atau Borongan.', '2025-08-18 15:41:53', '2025-08-18 15:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `jumlah_pekerja`
--

CREATE TABLE `jumlah_pekerja` (
  `id` int(11) UNSIGNED NOT NULL,
  `bahan_baku` varchar(500) NOT NULL,
  `jumlah_order` int(11) NOT NULL,
  `jenis_pekerjaan` varchar(50) NOT NULL,
  `target_waktu` int(11) NOT NULL,
  `jumlah_waktu_kerja` int(15) NOT NULL,
  `harga_perunit` decimal(25,6) NOT NULL,
  `man_hours` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) UNSIGNED NOT NULL,
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

INSERT INTO `karyawan` (`id`, `nik`, `nama_lengkap`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telepon`, `email`, `type_karyawan`, `status_karyawan`, `tanggal_masuk`, `tanggal_keluar`, `id_jabatan`, `id_departemen`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'K001', 'Budi Santoso', 'L', 'Jakarta', '1985-05-15', 'Jl. Merdeka No. 123', '081234567890', 'budi@amarta.com', 'tetap', 'aktif', '2020-01-15', NULL, 2, 3, NULL, '2025-08-18 15:13:05', '2025-08-22 15:03:54'),
(2, 'K002', 'Siti Nurhaliza', 'P', 'Bandung', '1990-08-20', 'Jl. Sudirman No. 45', '082345678901', 'siti@amarta.com', 'tetap', 'aktif', '2019-03-10', NULL, 5, 4, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(3, 'KH001', 'Ahmad Dahlan', 'L', 'Yogyakarta', '1995-12-10', 'Jl. Malioboro No. 78', '083456789012', 'ahmad@amarta.com', 'harian', 'aktif', '2023-06-01', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(4, 'KH002', 'Rina Marlina', 'P', 'Medan', '1998-03-25', 'Jl. Thamrin No. 90', '084567890123', 'rina@amarta.com', 'harian', 'aktif', '2023-07-15', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(5, 'KB001', 'Joko Widodo', 'L', 'Solo', '1992-07-30', 'Jl. Slamet Riyadi No. 56', '085678901234', 'joko@amarta.com', 'borongan', 'aktif', '2023-08-01', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(8, '12345678', 'Resky Putra P', 'L', 'Kota Depok', '1999-08-08', 'Dimun', '083456789012', 'androexe5@gmail.com', 'tetap', 'aktif', '2025-08-18', NULL, 7, 1, NULL, '2025-08-18 16:59:04', '2025-08-19 06:42:44'),
(14, '12345678', 'gatot nugroho', 'L', 'Kota Depok', '1992-05-08', 'Jatimulya\r\nPuri Insani', '083456789012', 'androexe5@gmail.com', 'tetap', 'aktif', '2025-08-26', NULL, 3, 1, NULL, '2025-08-26 06:59:45', '2025-08-26 06:59:45');

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
(2, '987654321098765', 'BPJS-TK002', 'BPJS-KS002', '7000000.00', '2000000.00', '750000.00', '500000.00', NULL, NULL),
(14, '', '', '', '0.00', '0.00', '0.00', '0.00', '0', '');

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
(7, 'Kain Katun', '', '2025-08-22 08:31:53', '2025-08-22 08:31:53'),
(8, 'Jarum', '', '2025-08-22 08:49:03', '2025-08-24 12:17:46'),
(0, 'Kain Faring', '', '2025-08-25 08:49:01', '2025-08-25 08:49:01');

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
  `id_sps` int(10) UNSIGNED NOT NULL,
  `spp_no` varchar(50) NOT NULL DEFAULT '',
  `kode_barang` varchar(50) DEFAULT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `harga` decimal(65,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `tanggal_persiapan` date NOT NULL,
  `pola` varchar(255) DEFAULT NULL,
  `marker` varchar(255) DEFAULT NULL,
  `upload_spk` varchar(255) DEFAULT NULL,
  `status` enum('pending','proses','selesai','batal') DEFAULT 'pending',
  `sp_srx` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `persiapan`
--

INSERT INTO `persiapan` (`id`, `id_sps`, `spp_no`, `kode_barang`, `nama_barang`, `jumlah`, `satuan`, `harga`, `total`, `tanggal_persiapan`, `pola`, `marker`, `upload_spk`, `status`, `sp_srx`, `created_at`, `updated_at`) VALUES
(1, 3, '', '123', 'Kain Biru', 500, 'meter', '10.00', '5000.00', '2025-08-27', NULL, NULL, NULL, 'pending', NULL, '2025-08-20 14:45:29', '2025-08-27 14:47:37'),
(2, 4, 'SPP0002', '123', 'T-Shirt', 900, 'pcs', '120000.00', '5000.00', '2025-08-30', '1755752875_FNS- KTD SH S M 01(KATADATA MEN)-PROD.hpg', '1755752875_FNS- KTD SH S M 01(KATADATA MEN)-PROD.hpg', '1755754247_Surat Perintah Kerja (SPK).pdf', 'selesai', 'SPP-0002', '2025-08-21 09:42:15', '2025-08-27 14:44:18'),
(3, 5, 'SPP0001', '123', 'Kain Biru', 500, 'meter', '10.00', '5000.00', '2025-08-31', NULL, NULL, NULL, 'pending', NULL, '2025-08-25 16:15:03', '2025-08-27 14:47:50'),
(4, 6, '', '123', 'Kain Biru', 500, 'meter', '10.00', '5000.00', '2025-09-02', NULL, NULL, NULL, 'pending', NULL, '2025-08-26 11:26:01', '2025-08-27 14:48:02'),
(5, 7, '', '123', 'Kain Biru', 500, 'meter', '10.00', '5000.00', '2025-09-02', NULL, NULL, NULL, 'pending', NULL, '2025-08-26 11:27:55', '2025-08-27 14:47:59'),
(6, 8, '', '123', 'Kain Biru', 500, 'meter', '10.00', '5000.00', '2025-09-02', NULL, NULL, NULL, 'batal', NULL, '2025-08-26 11:33:06', '2025-08-27 14:47:57'),
(7, 11, '', '123', 'Kain Biru', 500, 'meter', '10.00', '5000.00', '2025-09-06', NULL, NULL, NULL, 'proses', NULL, '2025-08-26 12:00:28', '2025-08-27 14:47:55'),
(8, 12, 'SPP0003', 'BRG-20250826-0012', 'Kemeja', 1000, 'pcs', '2500000000.00', '20.00', '2025-09-06', NULL, NULL, NULL, 'selesai', 'SPP-)1x)8', '2025-08-26 12:05:57', '2025-08-27 14:45:54');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `kode_produk` varchar(50) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produksi`
--

CREATE TABLE `produksi` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_sps` int(10) UNSIGNED NOT NULL,
  `kerjaan` varchar(255) NOT NULL,
  `target` int(11) DEFAULT 0,
  `hasil` int(11) DEFAULT 0,
  `pekerja` varchar(100) DEFAULT NULL,
  `status` enum('proses','pending','selesai') DEFAULT 'pending',
  `qc` varchar(100) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produksi`
--

INSERT INTO `produksi` (`id`, `id_sps`, `kerjaan`, `target`, `hasil`, `pekerja`, `status`, `qc`, `timestamp`) VALUES
(13, 4, 'Jahit Kantong', 1000, 1, 'Ahmad Dahlan | harian', 'proses', 'gatot nugroho', '2025-08-27 07:58:20'),
(14, 4, 'Kancing', 1000, 1, 'Ahmad Dahlan | harian', 'proses', 'gatot nugroho', '2025-08-27 08:03:47'),
(15, 4, 'Bordir', 1000, 1, 'Joko Widodo | borongan', 'proses', 'gatot nugroho', '2025-08-27 08:04:11'),
(16, 12, 'Jahit Kantong', 1500, 1, 'Ahmad Dahlan | harian', 'proses', 'gatot nugroho', '2025-08-27 08:13:49');

-- --------------------------------------------------------

--
-- Table structure for table `sps`
--

CREATE TABLE `sps` (
  `id` int(10) UNSIGNED NOT NULL,
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
(4, '2025-08-21', 'SPS-04', 'Aji', 'T-Shirt', 'T-Shirt', 1, 'M', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-30', 'Approved', 'SPK-20250821-0004'),
(5, '2025-08-25', 'SPS-05', 'Resky', 'Kemeja', 'Kemeja', 1000, 'M', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-31', 'Approved', 'SPK-20250825-0005'),
(6, '2025-08-26', 'SPS-03', 'Putra', 'Jas', 'Jas', 1, 'S', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-02', 'Approved', 'SPK-20250827-0006'),
(7, '2025-08-26', 'SPS-06', 'Putri', 'Kemeja', 'Kemeja', 1, 'M', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-02', 'Approved', 'SPK-20250826-0007'),
(8, '2025-08-26', 'SPS-07', 'Joko', 'Kemeja', 'Kemeja', 1, 'M', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-02', 'Approved', 'SPK-20250826-0008'),
(11, '2025-08-27', 'SPS-1029', 'Tono - Depok', 'T-Shirt', 'T-Shirt Jersey', 1, 'L', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-06', 'Approved', 'SPK-20250826-0011'),
(12, '2025-08-28', 'SPS-1030', 'Resky', 'Kemeja', 'Kemeja', 2, 'M', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-06', 'Approved', 'SPK-20250826-0012');

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
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nama` (`nama`),
  ADD KEY `idx_kepala_gudang` (`kepala_gudang`);

--
-- Indexes for table `hpp`
--
ALTER TABLE `hpp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hpp_persiapan` (`id_persiapan`),
  ADD KEY `fk_hpp_inventory` (`id_inventory`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_gudang`
--
ALTER TABLE `inventory_gudang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inventory_gudang_gudang` (`id_gudang`),
  ADD KEY `fk_inventory_gudang_inventory` (`id_inventory`);

--
-- Indexes for table `inventory_transaksi_gudang`
--
ALTER TABLE `inventory_transaksi_gudang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jumlah_pekerja`
--
ALTER TABLE `jumlah_pekerja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_karyawan_jabatan` (`id_jabatan`),
  ADD KEY `fk_karyawan_departemen` (`id_departemen`);

--
-- Indexes for table `persiapan`
--
ALTER TABLE `persiapan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_persiapan_sps` (`id_sps`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `produksi`
--
ALTER TABLE `produksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produksi_sps` (`id_sps`);

--
-- Indexes for table `sps`
--
ALTER TABLE `sps`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departemen`
--
ALTER TABLE `departemen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hpp`
--
ALTER TABLE `hpp`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `inventory_gudang`
--
ALTER TABLE `inventory_gudang`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `inventory_transaksi_gudang`
--
ALTER TABLE `inventory_transaksi_gudang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jumlah_pekerja`
--
ALTER TABLE `jumlah_pekerja`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `persiapan`
--
ALTER TABLE `persiapan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produksi`
--
ALTER TABLE `produksi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sps`
--
ALTER TABLE `sps`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hpp`
--
ALTER TABLE `hpp`
  ADD CONSTRAINT `fk_hpp_inventory` FOREIGN KEY (`id_inventory`) REFERENCES `inventory_gudang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hpp_persiapan` FOREIGN KEY (`id_persiapan`) REFERENCES `persiapan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_gudang`
--
ALTER TABLE `inventory_gudang`
  ADD CONSTRAINT `fk_inventory_gudang_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id`),
  ADD CONSTRAINT `fk_inventory_gudang_inventory` FOREIGN KEY (`id_inventory`) REFERENCES `inventory` (`id`);

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `fk_karyawan_departemen` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_karyawan_jabatan` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `persiapan`
--
ALTER TABLE `persiapan`
  ADD CONSTRAINT `fk_persiapan_sps` FOREIGN KEY (`id_sps`) REFERENCES `sps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `produksi`
--
ALTER TABLE `produksi`
  ADD CONSTRAINT `fk_produksi_sps` FOREIGN KEY (`id_sps`) REFERENCES `sps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
