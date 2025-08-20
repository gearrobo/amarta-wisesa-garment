-- Tabel untuk menyimpan data persiapan produksi
CREATE TABLE IF NOT EXISTS `persiapan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_sps` varchar(50) NOT NULL,
  `nama_buyer` varchar(100) NOT NULL,
  `style` varchar(100) NOT NULL,
  `tanggal_persiapan` date NOT NULL,
  `status` enum('pending','proses','selesai') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Contoh data untuk testing
INSERT INTO `persiapan` (`no_sps`, `nama_buyer`, `style`, `tanggal_persiapan`, `status`) VALUES
('SPS-2024-001', 'PT. Maju Jaya', 'T-Shirt Basic', '2024-01-15', 'selesai'),
('SPS-2024-002', 'CV. Sukses Abadi', 'Hoodie Zipper', '2024-01-20', 'proses'),
('SPS-2024-003', 'PT. Global Fashion', 'Polo Shirt', '2024-01-25', 'pending');
