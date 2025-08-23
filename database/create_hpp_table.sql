-- Create HPP table with new structure
CREATE TABLE IF NOT EXISTS `hpp` (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_persiapan INT UNSIGNED NOT NULL,
    no_urut INT,
    bahan VARCHAR(100),
    qty INT,
    barang_jadi INT,
    stok_order INT,
    efisiensi_consp DECIMAL(10,2),
    efisiensi_rap DECIMAL(10,2),
    stok_material INT,
    po INT,
    harga_per_meter DECIMAL(15,2),
    rap_x_harga_per_m DECIMAL(15,2),
    total_harga_bahan DECIMAL(15,2),
    biaya_tenaga_kerja_per_qty DECIMAL(15,2),
    total_biaya_tenaga_kerja DECIMAL(15,2),
    listrik DECIMAL(15,2),
    air DECIMAL(15,2),
    overhead DECIMAL(15,2),
    total_biaya DECIMAL(15,2),
    hpp DECIMAL(15,2),
    profit DECIMAL(5,2),
    harga_jual DECIMAL(15,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_hpp_persiapan FOREIGN KEY (id_persiapan) REFERENCES persiapan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE INDEX idx_hpp_persiapan ON hpp(id_persiapan);
CREATE INDEX idx_hpp_bahan ON hpp(bahan);
CREATE INDEX idx_hpp_created ON hpp(created_at);

-- Drop old hpp table if exists (uncomment if needed)
-- DROP TABLE IF EXISTS hpp;

-- Insert sample data for testing
INSERT INTO `hpp` (
    id_persiapan, no_urut, bahan, qty, barang_jadi, stok_order, 
    efisiensi_consp, efisiensi_rap, stok_material, po, harga_per_meter,
    rap_x_harga_per_m, total_harga_bahan, biaya_tenaga_kerja_per_qty,
    total_biaya_tenaga_kerja, listrik, air, overhead, total_biaya,
    hpp, profit, harga_jual
) VALUES 
(1, 1, 'Kain Katun', 100, 95, 100, 1.05, 1.10, 105, 1001, 25000.00, 27500.00, 2750000.00, 5000.00, 500000.00, 200000.00, 100000.00, 300000.00, 3550000.00, 35500.00, 30.00, 46150.00),
(1, 2, 'Benang', 100, 95, 100, 1.02, 1.05, 102, 1002, 5000.00, 5250.00, 525000.00, 1000.00, 100000.00, 50000.00, 25000.00, 75000.00, 725000.00, 7250.00, 25.00, 9062.50);

-- Create view for HPP summary
CREATE OR REPLACE VIEW hpp_summary AS
SELECT 
    h.id,
    h.id_persiapan,
    p.nama_persiapan,
    h.bahan,
    h.qty,
    h.barang_jadi,
    h.hpp,
    h.harga_jual,
    h.profit,
    h.created_at
FROM hpp h
JOIN persiapan p ON h.id_persiapan = p.id;
