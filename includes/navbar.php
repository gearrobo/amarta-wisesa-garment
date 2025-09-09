<nav class="navbar navbar-expand ">
        <div class="container-fluid">
            <span class="navbar-brand">CV. Amarta Wisesa</span>

            <ul class="navbar-nav ms-auto">
                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-lg me-2"></i>
                        <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-item text-muted">
                            Role: <strong><?php echo htmlspecialchars($_SESSION['role'] ?? 'Unknown'); ?></strong>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-id-card me-2"></i>Profil Saya</a></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>