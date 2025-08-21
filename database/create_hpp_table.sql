-- Create hpp table with relationship to persiapan
CREATE TABLE IF NOT EXISTS `hpp` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_persiapan` int(11) NOT NULL,
    `nama_barang` varchar(255) NOT NULL,
    `jumlah` int(11) NOT NULL DEFAULT 0,
    `satuan` varchar(50) NOT NULL DEFAULT 'pcs',
    `harga` decimal(15,2) NOT NULL DEFAULT 0.00,
    `total` decimal(15,2) NOT NULL DEFAULT 0.00,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `id_persiapan` (`id_persiapan`),
    CONSTRAINT `hpp_ibfk_1` FOREIGN KEY (`id_persiapan`) REFERENCES `persiapan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add indexes for better performance
CREATE INDEX idx_hpp_nama_barang ON hpp(nama_barang);
CREATE INDEX idx_hpp_created_at ON hpp(created_at);
