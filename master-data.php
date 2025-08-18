<?php
// Include header
include 'includes/header.php';
?>

<div class="main-content">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Master Data</h1>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Data</li>
                </ol>
            </nav>

            <!-- Master Data Content -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Kelola Master Data</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Halaman ini digunakan untuk mengelola data master sistem.</p>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Data Karyawan</h5>
                                    <p class="card-text">Kelola data karyawan dan staf</p>
                                    <a href="#" class="btn btn-primary">Kelola</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-industry fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Data Produk</h5>
                                    <p class="card-text">Kelola data produk dan item</p>
                                    <a href="#" class="btn btn-success">Kelola</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-truck fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Data Supplier</h5>
                                    <p class="card-text">Kelola data supplier dan vendor</p>
                                    <a href="#" class="btn btn-info">Kelola</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-cogs fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Data Mesin</h5>
                                    <p class="card-text">Kelola data mesin dan peralatan</p>
                                    <a href="#" class="btn btn-warning">Kelola</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <i class="fas fa-warehouse fa-3x text-danger mb-3"></i>
                                    <h5 class="card-title">Data Gudang</h5>
                                    <p class="card-text">Kelola data gudang dan lokasi</p>
                                    <a href="#" class="btn btn-danger">Kelola</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-secondary mb-3"></i>
                                    <h5 class="card-title">Data Departemen</h5>
                                    <p class="card-text">Kelola data departemen dan divisi</p>
                                    <a href="#" class="btn btn-secondary">Kelola</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
