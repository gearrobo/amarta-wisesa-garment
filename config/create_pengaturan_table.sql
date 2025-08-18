-- Tabel pengaturan untuk menyimpan konfigurasi sistem
CREATE TABLE IF NOT EXISTS `pengaturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat_perusahaan` text NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data default
INSERT INTO `pengaturan` (`id`, `nama_perusahaan`, `alamat_perusahaan`, `telepon`, `email`, `logo`) VALUES
(1, 'PT Amarta Wisesa', 'Jl. Raya Industri No. 123, Jakarta', '021-12345678', 'info@amartawisesa.com', NULL);
