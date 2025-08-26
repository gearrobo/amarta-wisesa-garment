    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="img/amarta-wisesa.png" alt="Amarta Wisesa Logo" class="sidebar-logo">
        </div>
        <ul class="sidebar-nav">
            <li>
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="sps-sample.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sps-sample.php' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i>
                    Order Samples
                </a>
            </li>
            <li>
                <a href="persiapan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'persiapan.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i>
                    Persiapan Produksi
                </a>
            </li>
            <li>
                <a href="produksi.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'produksi.php' ? 'active' : ''; ?>">
                    <i class="fas fa-industry"></i>
                    Produksi
                </a>
            </li>
            <li>
                <a href="payroll.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'payroll.php' ? 'active' : ''; ?>">
                    <i class="fas fa-money-bill-wave"></i>
                    Payroll
                </a>
            </li>
            <li>
                <a href="inventory.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'active' : ''; ?>">
                    <i class="fas fa-boxes"></i>
                    Inventory
                </a>
            </li>
            <li>
                <a href="master-data.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'master-data.php' ? 'active' : ''; ?>">
                    <i class="fas fa-database"></i>
                    Master Data
                </a>
            </li>
            <li>
                <a href="pengaturan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pengaturan.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    Pengaturan
                </a>
            </li>
        </ul>
    </nav>
