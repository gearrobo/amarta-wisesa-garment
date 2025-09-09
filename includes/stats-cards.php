<?php
// Stats Cards Component - Abu-abu (Gray) Theme
// Include this file where you want to display stats cards
?>

<!-- Stats Cards Section -->
<div class="stats-section">
    <div class="container">
        <div class="row">
            <!-- Total Orders Card -->
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-number">1,234</div>
                    <div class="stats-label">Total Orders</div>
                    <div class="stats-trend up">
                        <i class="fas fa-arrow-up"></i> +12% this month
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-number">Rp 45.2M</div>
                    <div class="stats-label">Total Revenue</div>
                    <div class="stats-trend up">
                        <i class="fas fa-arrow-up"></i> +8% this month
                    </div>
                </div>
            </div>

            <!-- Customers Card -->
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number">567</div>
                    <div class="stats-label">Total Customers</div>
                    <div class="stats-trend up">
                        <i class="fas fa-arrow-up"></i> +15% this month
                    </div>
                </div>
            </div>

            <!-- Products Card -->
            <div class="col-md-3 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="stats-number">89</div>
                    <div class="stats-label">Total Products</div>
                    <div class="stats-trend down">
                        <i class="fas fa-arrow-down"></i> -3% this month
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Cards with Variations -->
<div class="container mt-4">
    <div class="row">
        <!-- Pending Orders -->
        <div class="col-md-4 mb-4">
            <div class="stats-card warning">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">23</div>
                <div class="stats-label">Pending Orders</div>
                <div class="stats-trend">
                    <i class="fas fa-info-circle"></i> Needs attention
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="col-md-4 mb-4">
            <div class="stats-card success">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">1,189</div>
                <div class="stats-label">Completed Orders</div>
                <div class="stats-trend up">
                    <i class="fas fa-arrow-up"></i> 96% completion rate
                </div>
            </div>
        </div>

        <!-- Cancelled Orders -->
        <div class="col-md-4 mb-4">
            <div class="stats-card danger">
                <div class="stats-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stats-number">22</div>
                <div class="stats-label">Cancelled Orders</div>
                <div class="stats-trend down">
                    <i class="fas fa-arrow-down"></i> -5% from last month
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-section {
    padding: 2rem 0;
    background: linear-gradient(135deg, #fafafa, #f0f0f0);
}

.stats-section .container {
    max-width: 1200px;
}

@media (max-width: 768px) {
    .stats-card {
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .stats-card .stats-number {
        font-size: 2rem;
    }
    
    .stats-card .stats-icon {
        width: 50px;
        height: 50px;
    }
    
    .stats-card .stats-icon i {
        font-size: 20px;
    }
}
</style>
