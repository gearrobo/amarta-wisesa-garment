<nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>Amarta Wisesa</h4>
            <small>SPS Management System</small>
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
                    Surat Perintah Samples
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    Laporan
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
