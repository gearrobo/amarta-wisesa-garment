-- Add id_kategori column to inventory table
ALTER TABLE inventory ADD COLUMN id_kategori INT NULL;

-- Add foreign key constraint
ALTER TABLE inventory ADD CONSTRAINT fk_inventory_kategori 
FOREIGN KEY (id_kategori) REFERENCES kategori_barang(id_kategori);
