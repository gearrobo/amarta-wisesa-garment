-- Create kategori_barang table
CREATE TABLE IF NOT EXISTS kategori_barang (
    id_kategori INT(11) PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(255) NOT NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add index for better performance
CREATE INDEX idx_nama_kategori ON kategori_barang(nama_kategori);
CREATE INDEX idx_created_at ON kategori_barang(created_at);

-- Insert some sample data
INSERT INTO kategori_barang (nama_kategori, keterangan) VALUES
('Kain', 'Kategori untuk berbagai jenis kain'),
('Benang', 'Kategori untuk berbagai jenis benang'),
('Aksesoris', 'Kategori untuk aksesoris pakaian'),
('Bahan Baku', 'Kategori untuk bahan baku produksi'),
('Produk Jadi', 'Kategori untuk produk yang sudah jadi');
