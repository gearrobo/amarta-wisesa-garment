-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Agu 2025 pada 09.53
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 7.4.33

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
-- Struktur dari tabel `absensi`
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
-- Struktur dari tabel `departemen`
--

CREATE TABLE `departemen` (
  `id_departemen` int(11) NOT NULL,
  `nama_departemen` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `departemen`
--

INSERT INTO `departemen` (`id_departemen`, `nama_departemen`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Produksi', 'Departemen yang menangani proses produksi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(2, 'Quality Assurance', 'Departemen yang menangani kontrol kualitas', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(3, 'Gudang', 'Departemen yang menangani penyimpanan dan distribusi', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(4, 'HR & GA', 'Departemen yang menangani sumber daya manusia', '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(5, 'Finance', 'Departemen yang menangani keuangan', '2025-08-18 15:13:05', '2025-08-18 15:13:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gaji`
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
-- Struktur dari tabel `gudang`
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
-- Dumping data untuk tabel `gudang`
--

INSERT INTO `gudang` (`id_gudang`, `nama`, `alamat`, `kepala_gudang`, `kapasitas`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Probolinggo', 'Jl. Probolinggo', 'Admin Probolinggo', 5000, 'Gudang utama untuk penyimpanan bahan baku', '2025-08-20 03:28:41', '2025-08-21 03:57:55'),
(2, 'Pasuruan', 'Jl. Pasuruan', 'Admin Pasuruan', 3000, 'Gudang untuk produk jadi dan finishing', '2025-08-20 03:28:41', '2025-08-21 03:57:19'),
(3, 'Kasin', 'Jl. Brigjend. Katamso No.48-50, Kauman, Kec. Klojen, Kota Malang, Jawa Timur 65119', 'Stefanus', 4000, 'Gudang khusus untuk bahan baku tekstil', '2025-08-20 03:28:41', '2025-08-21 03:56:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hpp`
--

CREATE TABLE `hpp` (
  `id` int(11) NOT NULL,
  `id_persiapan` int(11) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(50) NOT NULL DEFAULT 'pcs',
  `harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hpp`
--

INSERT INTO `hpp` (`id`, `id_persiapan`, `nama_barang`, `jumlah`, `satuan`, `harga`, `total`, `created_at`, `updated_at`) VALUES
(1, 2, 'Kain Biru', 900, 'pcs', '120000.00', '108000000.00', '2025-08-22 07:28:14', '2025-08-22 07:28:14'),
(2, 2, 'Kancing', 15000, 'pcs', '250.00', '3750000.00', '2025-08-22 07:30:31', '2025-08-22 07:30:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `warehouse` varchar(100) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `harga_per_unit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `inventory`
--

INSERT INTO `inventory` (`id`, `nama_barang`, `warehouse`, `unit`, `jumlah`, `harga_per_unit`, `keterangan`, `created_at`, `updated_at`) VALUES
(8, 'Kain Flanel', 'Kasin', 'm²', 783, '150000.00', 'Beli untuk Kebutuhan Project Jakarta', '2025-08-21 10:59:11', NULL),
(9, 'Kancing', 'Kasin', 'pcs', 95000, '250.00', 'Kebutuhan Project Jakarta', '2025-08-21 11:00:12', NULL),
(10, 'Kancing', 'Probolinggo', 'pcs', 78000, '250.00', 'Stock Opname', '2025-08-21 11:04:00', NULL),
(11, 'Benang Polyester', 'Probolinggo', 'kg', 20, '100000.00', '', '2025-08-21 21:37:32', NULL),
(12, 'Kancing Besar', 'Kasin', 'pcs', 5000000, '10.00', '', '2025-08-21 23:17:59', NULL),
(13, 'Jas Almamater', 'Pasuruan', 'pcs', 2000, '200000.00', '', '2025-08-21 23:41:06', NULL),
(14, 'Kain Flanel', 'Kasin', 'm²', 100, '100000.00', '', '2025-08-22 14:16:42', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `inventory_gudang`
--

CREATE TABLE `inventory_gudang` (
  `id_inventory` int(11) NOT NULL,
  `id_gudang` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `stok_akhir` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(20) NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `inventory_gudang`
--

INSERT INTO `inventory_gudang` (`id_inventory`, `id_gudang`, `kode_barang`, `nama_barang`, `jumlah`, `stok_akhir`, `satuan`, `tanggal_masuk`, `tanggal_update`) VALUES
(1, 1, 'BRG001', 'Kain Katun Premium', 500, 500, 'roll', '2024-01-15', '2025-08-21 15:51:45'),
(2, 1, 'BRG002', 'Benang Polyester', 220, 240, 'kg', '2024-01-20', '2025-08-21 16:38:23'),
(3, 2, 'BRG003', 'Kaos Polos L', 1000, 1000, 'pcs', '2024-01-25', '2025-08-21 15:51:45'),
(4, 2, 'BRG004', 'Celana Jeans M', 300, 300, 'pcs', '2024-01-28', '2025-08-21 15:51:45'),
(5, 3, 'BRG005', 'Kain Katun', 100000, 100000, 'meter', '2025-08-21', '2025-08-21 15:51:45'),
(6, 2, 'BRG006', 'Jas Almamater', 7000, 14000, 'pcs', '2025-08-21', '2025-08-21 16:41:06'),
(7, 3, 'INV0008', 'Kain Flanel', 883, 1766, 'm²', '2025-08-21', '2025-08-22 07:16:42'),
(8, 3, 'INV0009', 'Kancing', 95000, 190000, 'pcs', '2025-08-21', '2025-08-21 16:38:23'),
(9, 1, 'INV0010', 'Kancing', 78000, 156000, 'pcs', '2025-08-21', '2025-08-21 16:38:23'),
(10, 3, 'INV0012', 'Kancing Besar', 5000000, 10000000, 'pcs', '2025-08-21', '2025-08-21 16:38:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `inventory_transaksi`
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
-- Struktur dari tabel `inventory_transaksi_gudang`
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
-- Dumping data untuk tabel `inventory_transaksi_gudang`
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
(23, 7, 'masuk', 100, 0, NULL, 'Tambah stok dari inventory.php: ', '2025-08-22 14:16:42', NULL, '2025-08-22 07:16:42', '2025-08-22 07:16:42');

--
-- Trigger `inventory_transaksi_gudang`
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
-- Struktur dari tabel `jabatan`
--

CREATE TABLE `jabatan` (
  `id_jabatan` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jabatan`
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
-- Struktur dari tabel `karyawan`
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
-- Dumping data untuk tabel `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nik`, `nama_lengkap`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telepon`, `email`, `type_karyawan`, `status_karyawan`, `tanggal_masuk`, `tanggal_keluar`, `id_jabatan`, `id_departemen`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'K001', 'Budi Santoso', 'L', 'Jakarta', '1985-05-15', 'Jl. Merdeka No. 123', '081234567890', 'budi@amarta.com', 'tetap', 'aktif', '2020-01-15', NULL, 2, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(2, 'K002', 'Siti Nurhaliza', 'P', 'Bandung', '1990-08-20', 'Jl. Sudirman No. 45', '082345678901', 'siti@amarta.com', 'tetap', 'aktif', '2019-03-10', NULL, 5, 4, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(3, 'KH001', 'Ahmad Dahlan', 'L', 'Yogyakarta', '1995-12-10', 'Jl. Malioboro No. 78', '083456789012', 'ahmad@amarta.com', 'harian', 'aktif', '2023-06-01', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(4, 'KH002', 'Rina Marlina', 'P', 'Medan', '1998-03-25', 'Jl. Thamrin No. 90', '084567890123', 'rina@amarta.com', 'harian', 'aktif', '2023-07-15', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(5, 'KB001', 'Joko Widodo', 'L', 'Solo', '1992-07-30', 'Jl. Slamet Riyadi No. 56', '085678901234', 'joko@amarta.com', 'borongan', 'aktif', '2023-08-01', NULL, 1, 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(8, '12345678', 'Resky Putra P', 'L', 'Kota Depok', '1999-08-08', 'Dimun', '083456789012', 'androexe5@gmail.com', 'tetap', 'aktif', '2025-08-18', NULL, 7, 1, NULL, '2025-08-18 16:59:04', '2025-08-19 06:42:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `karyawan_harian_borongan`
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
-- Dumping data untuk tabel `karyawan_harian_borongan`
--

INSERT INTO `karyawan_harian_borongan` (`id_karyawan`, `upah_per_hari`, `upah_per_jam`, `upah_borongan`, `metode_pembayaran`, `rekening_bank`, `nama_bank`) VALUES
(3, '150000.00', '0.00', '0.00', 'harian', NULL, NULL),
(4, '150000.00', '0.00', '0.00', 'harian', NULL, NULL),
(5, '0.00', '0.00', '2500000.00', 'borongan', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `karyawan_tetap`
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
-- Dumping data untuk tabel `karyawan_tetap`
--

INSERT INTO `karyawan_tetap` (`id_karyawan`, `npwp`, `bpjs_ketenagakerjaan`, `bpjs_kesehatan`, `gaji_pokok`, `tunjangan_jabatan`, `tunjangan_transport`, `tunjangan_makan`, `rekening_bank`, `nama_bank`) VALUES
(1, '123456789012345', 'BPJS-TK001', 'BPJS-KS001', '5000000.00', '1500000.00', '500000.00', '300000.00', NULL, NULL),
(2, '987654321098765', 'BPJS-TK002', 'BPJS-KS002', '7000000.00', '2000000.00', '750000.00', '500000.00', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori_barang`
--

INSERT INTO `kategori_barang` (`id_kategori`, `nama_kategori`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Kain', 'Kategori untuk berbagai jenis kain', '2025-08-22 07:47:00', '2025-08-22 07:47:00'),
(2, 'Benang', 'Kategori untuk berbagai jenis benang', '2025-08-22 07:47:00', '2025-08-22 07:47:00'),
(3, 'Aksesoris', 'Kategori untuk aksesoris pakaian', '2025-08-22 07:47:00', '2025-08-22 07:47:00'),
(4, 'Bahan Baku', 'Kategori untuk bahan baku produksi', '2025-08-22 07:47:00', '2025-08-22 07:47:00'),
(5, 'Produk Jadi', 'Kategori untuk produk yang sudah jadi', '2025-08-22 07:47:00', '2025-08-22 07:47:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mesin`
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
-- Dumping data untuk tabel `mesin`
--

INSERT INTO `mesin` (`id_mesin`, `seri_number`, `nama_mesin`, `lokasi`, `tanggal_masuk`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'SN-001', 'Mesin Jahit Singer', 'Line A - Station 1', '2024-01-15', 'Mesin jahit untuk produksi kaos', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
(3, 'SN-003', 'Mesin Bordir Komputer', 'Line B - Station 1', '2024-02-01', 'Mesin bordir digital 10 kepala', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
(4, 'SN-004', 'Mesin Cutting Laser', 'Area Cutting', '2024-02-10', 'Mesin cutting presisi untuk kain', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
(5, 'SN-005', 'Mesin Press Kaos', 'Area Finishing', '2024-02-15', 'Mesin press untuk sablon digital', '2025-08-19 03:25:49', '2025-08-19 03:25:49'),
(11, 'SN-006', 'Mesin Jahit', 'Warehouse', '2025-08-19', 'mesin ya', '2025-08-19 03:32:14', '2025-08-19 03:42:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
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
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `id_karyawan`, `username`, `password`, `role`, `status_aktif`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05'),
(2, 2, 'hr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hr', 1, NULL, '2025-08-18 15:13:05', '2025-08-18 15:13:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `persiapan`
--

CREATE TABLE `persiapan` (
  `id` int(11) NOT NULL,
  `id_sps` int(11) NOT NULL,
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
-- Dumping data untuk tabel `persiapan`
--

INSERT INTO `persiapan` (`id`, `id_sps`, `kode_barang`, `nama_barang`, `jumlah`, `satuan`, `harga`, `total`, `tanggal_persiapan`, `pola`, `marker`, `upload_spk`, `status`, `sp_srx`, `created_at`, `updated_at`) VALUES
(1, 3, '123', 'Kain Biru', 500, 'meter', '10.00', '5000.00', '2025-08-27', NULL, NULL, NULL, 'pending', '', '2025-08-20 14:45:29', '2025-08-20 14:45:29'),
(2, 4, '123', 'T-Shirt', 900, 'meter', '120000.00', '5000.00', '2025-08-30', '1755752875_FNS- KTD SH S M 01(KATADATA MEN)-PROD.hpg', '1755752875_FNS- KTD SH S M 01(KATADATA MEN)-PROD.hpg', '1755754247_Surat Perintah Kerja (SPK).pdf', 'proses', 'SPK0001', '2025-08-21 09:42:15', '2025-08-22 14:33:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sps`
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
-- Dumping data untuk tabel `sps`
--

INSERT INTO `sps` (`id`, `tanggal`, `sps_no`, `customer`, `item`, `artikel`, `qty`, `size`, `sample_product`, `design`, `st_chart`, `material_sm`, `pola_sample`, `buat_sample`, `kirim`, `approval`, `sp_srx`) VALUES
(2, '2025-08-19', 'SPS-01', 'Resky-jakarta', 'T-Shirt', 'T-Shirt', 2, 'L', '1755572545_68a3e9415efd7_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '1755571849_stempel soendev.png', '2025-08-26', 'Approved', 'SPK-20250820-0002'),
(3, '2025-08-20', 'SPS-02', 'Joko-Jakarta', 'Kemeja', 'Kemeja', 1, 'M', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-27', 'Approved', ''),
(4, '2025-08-21', 'SPS-04', 'Aji', 'T-Shirt', 'T-Shirt', 1, 'M', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-30', 'Approved', 'SPK-20250821-0004');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_inventory_gudang_summary`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_inventory_gudang_summary` (
`id_inventory` int(11)
,`id_gudang` int(11)
,`nama_gudang` varchar(100)
,`kode_barang` varchar(50)
,`nama_barang` varchar(100)
,`jumlah_awal` int(11)
,`stok_akhir` int(11)
,`satuan` varchar(20)
,`tanggal_masuk` date
,`total_transaksi` bigint(21)
,`total_masuk` decimal(32,0)
,`total_keluar` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_inventory_gudang_summary`
--
DROP TABLE IF EXISTS `v_inventory_gudang_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_inventory_gudang_summary`  AS SELECT `ig`.`id_inventory` AS `id_inventory`, `ig`.`id_gudang` AS `id_gudang`, `g`.`nama` AS `nama_gudang`, `ig`.`kode_barang` AS `kode_barang`, `ig`.`nama_barang` AS `nama_barang`, `ig`.`jumlah` AS `jumlah_awal`, `ig`.`stok_akhir` AS `stok_akhir`, `ig`.`satuan` AS `satuan`, `ig`.`tanggal_masuk` AS `tanggal_masuk`, count(`itg`.`id`) AS `total_transaksi`, coalesce(sum(`itg`.`jumlah_masuk`),0) AS `total_masuk`, coalesce(sum(`itg`.`jumlah_keluar`),0) AS `total_keluar` FROM ((`inventory_gudang` `ig` left join `gudang` `g` on(`ig`.`id_gudang` = `g`.`id_gudang`)) left join `inventory_transaksi_gudang` `itg` on(`ig`.`id_inventory` = `itg`.`inventory_gudang_id`)) GROUP BY `ig`.`id_inventory``id_inventory`  ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD UNIQUE KEY `unique_absensi` (`id_karyawan`,`tanggal`);

--
-- Indeks untuk tabel `departemen`
--
ALTER TABLE `departemen`
  ADD PRIMARY KEY (`id_departemen`);

--
-- Indeks untuk tabel `gaji`
--
ALTER TABLE `gaji`
  ADD PRIMARY KEY (`id_gaji`),
  ADD UNIQUE KEY `unique_gaji` (`id_karyawan`,`periode_bulan`,`periode_tahun`);

--
-- Indeks untuk tabel `gudang`
--
ALTER TABLE `gudang`
  ADD PRIMARY KEY (`id_gudang`),
  ADD KEY `idx_nama` (`nama`),
  ADD KEY `idx_kepala_gudang` (`kepala_gudang`);

--
-- Indeks untuk tabel `hpp`
--
ALTER TABLE `hpp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_persiapan` (`id_persiapan`),
  ADD KEY `idx_hpp_nama_barang` (`nama_barang`),
  ADD KEY `idx_hpp_created_at` (`created_at`);

--
-- Indeks untuk tabel `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nama_barang` (`nama_barang`),
  ADD KEY `idx_warehouse` (`warehouse`);

--
-- Indeks untuk tabel `inventory_gudang`
--
ALTER TABLE `inventory_gudang`
  ADD PRIMARY KEY (`id_inventory`),
  ADD KEY `id_gudang` (`id_gudang`);

--
-- Indeks untuk tabel `inventory_transaksi`
--
ALTER TABLE `inventory_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_id` (`inventory_id`);

--
-- Indeks untuk tabel `inventory_transaksi_gudang`
--
ALTER TABLE `inventory_transaksi_gudang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_gudang_id` (`inventory_gudang_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id_jabatan`);

--
-- Indeks untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `id_jabatan` (`id_jabatan`),
  ADD KEY `id_departemen` (`id_departemen`);

--
-- Indeks untuk tabel `karyawan_harian_borongan`
--
ALTER TABLE `karyawan_harian_borongan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indeks untuk tabel `karyawan_tetap`
--
ALTER TABLE `karyawan_tetap`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indeks untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`id_kategori`),
  ADD KEY `idx_nama_kategori` (`nama_kategori`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `mesin`
--
ALTER TABLE `mesin`
  ADD PRIMARY KEY (`id_mesin`),
  ADD UNIQUE KEY `seri_number` (`seri_number`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `persiapan`
--
ALTER TABLE `persiapan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_sps` (`id_sps`);

--
-- Indeks untuk tabel `sps`
--
ALTER TABLE `sps`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `departemen`
--
ALTER TABLE `departemen`
  MODIFY `id_departemen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `gaji`
--
ALTER TABLE `gaji`
  MODIFY `id_gaji` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gudang`
--
ALTER TABLE `gudang`
  MODIFY `id_gudang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `hpp`
--
ALTER TABLE `hpp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `inventory_gudang`
--
ALTER TABLE `inventory_gudang`
  MODIFY `id_inventory` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `inventory_transaksi`
--
ALTER TABLE `inventory_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `inventory_transaksi_gudang`
--
ALTER TABLE `inventory_transaksi_gudang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id_jabatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `mesin`
--
ALTER TABLE `mesin`
  MODIFY `id_mesin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `persiapan`
--
ALTER TABLE `persiapan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `sps`
--
ALTER TABLE `sps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `gaji`
--
ALTER TABLE `gaji`
  ADD CONSTRAINT `gaji_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hpp`
--
ALTER TABLE `hpp`
  ADD CONSTRAINT `hpp_ibfk_1` FOREIGN KEY (`id_persiapan`) REFERENCES `persiapan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `inventory_gudang`
--
ALTER TABLE `inventory_gudang`
  ADD CONSTRAINT `inventory_gudang_ibfk_1` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `inventory_transaksi`
--
ALTER TABLE `inventory_transaksi`
  ADD CONSTRAINT `inventory_transaksi_ibfk_1` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `inventory_transaksi_gudang`
--
ALTER TABLE `inventory_transaksi_gudang`
  ADD CONSTRAINT `inventory_transaksi_gudang_ibfk_1` FOREIGN KEY (`inventory_gudang_id`) REFERENCES `inventory_gudang` (`id_inventory`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_transaksi_gudang_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `karyawan_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE SET NULL,
  ADD CONSTRAINT `karyawan_ibfk_2` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `karyawan_harian_borongan`
--
ALTER TABLE `karyawan_harian_borongan`
  ADD CONSTRAINT `karyawan_harian_borongan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `karyawan_tetap`
--
ALTER TABLE `karyawan_tetap`
  ADD CONSTRAINT `karyawan_tetap_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `pengguna_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `persiapan`
--
ALTER TABLE `persiapan`
  ADD CONSTRAINT `persiapan_ibfk_1` FOREIGN KEY (`id_sps`) REFERENCES `sps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
