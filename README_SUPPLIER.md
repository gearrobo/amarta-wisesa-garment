# Supplier Management System

## Overview
This is a comprehensive supplier management system built with PHP, MySQL, and Bootstrap 5. It provides full CRUD (Create, Read, Update, Delete) functionality for managing supplier data.

## Features
- ✅ Full CRUD operations for suppliers
- ✅ Responsive Bootstrap 5 design
- ✅ DataTables integration for advanced table features
- ✅ Search and filtering capabilities
- ✅ Pagination
- ✅ Modal forms for add/edit operations
- ✅ Confirmation dialogs for delete operations
- ✅ Form validation
- ✅ Success/error message handling

## Database Setup

### 1. Create Supplier Table
Run the SQL script in `database/create_supplier_table.sql` to create the supplier table in the main database.

### 2. Database Configuration
The system uses the existing database configuration from `config/db.php`.

### 3. Execute SQL Script
You can run the SQL script using phpMyAdmin or MySQL command line:
```sql
-- Use the main database
USE db_amarta_wisesa;

-- Run the create table script
SOURCE database/create_supplier_table.sql
```

## File Structure
- `data-suplier.php` - Main supplier management interface
- `database/create_supplier_table.sql` - SQL script to create supplier table
- `config/db.php` - Database configuration
- `includes/sidebar.php` - Navigation sidebar

## Usage Instructions

### Accessing the System
1. Navigate to `data-suplier.php` in your browser
2. The system will display all suppliers in a responsive table
3. Use the buttons to add, edit, delete, or view supplier details

### Adding New Suppliers
1. Click the "Tambah Supplier" button
2. Fill in the form with supplier details
3. Click "Simpan" to save the new supplier

### Editing Suppliers
1. Click the "Edit" button on any supplier row
2. Modify the supplier information in the modal
3. Click "Update" to save changes

### Deleting Suppliers
1. Click the "Delete" button on any supplier row
2. Confirm the deletion in the dialog
3. The supplier will be removed from the system

## Technical Details

### Technologies Used
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5, jQuery, DataTables
- **Icons**: Font Awesome 6

### Database Schema
The supplier table includes:
- `id` (Primary Key)
- `kode_suplier` (Unique supplier code)
- `nama_suplier` (Supplier name)
- `alamat` (Address)
- `telepon` (Phone)
- `email` (Email)
- `kontak_person` (Contact person)
- `npwp` (Tax ID)
- `keterangan` (Description)
- `status` (Active/Inactive)
- `created_at` (Creation timestamp)
- `updated_at` (Last update timestamp)

## Testing
After setting up the database, you can test the connection by running:
```bash
php test-database.php
```

## Troubleshooting
- If you get "Table doesn't exist" error, make sure to run the SQL script first
- Check database credentials in `config/db.php`
- Ensure MySQL service is running
- Verify the database name is correct (db_amarta_wisesa)

## Sample Data
The system comes with 3 sample suppliers:
- PT Maju Sejahtera (Textile raw materials)
- CV Sumber Jaya (Fashion accessories)
- PT Global Textile (Imported fabrics)
