<!-- Theater Info Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-2"><?php echo htmlspecialchars($theater['name']); ?></h5>
                    <p class="text-muted mb-1">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($theater['location'] ?? 'N/A'); ?>
                    </p>
                    <?php if ($theater['address']): ?>
                        <p class="text-muted mb-1">
                            <i class="fas fa-address-card"></i> <?php echo htmlspecialchars($theater['address']); ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($theater['phone']): ?>
                        <p class="text-muted mb-0">
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($theater['phone']); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="text-end">
                    <span class="badge bg-success"><?php echo $theater['total_screens']; ?> phòng chiếu</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Tổng suất chiếu</div>
                    <div class="stat-value text-primary"><?php echo number_format($stats['total_showtimes']); ?></div>
                </div>
                <div class="stat-icon bg-primary">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Suất chiếu hôm nay</div>
                    <div class="stat-value text-info"><?php echo number_format($stats['today_showtimes']); ?></div>
                </div>
                <div class="stat-icon bg-info">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Tổng vé đã bán</div>
                    <div class="stat-value text-success"><?php echo number_format($stats['total_tickets']); ?></div>
                </div>
                <div class="stat-icon bg-success">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Vé hôm nay</div>
                    <div class="stat-value text-warning"><?php echo number_format($stats['today_tickets']); ?></div>
                </div>
                <div class="stat-icon bg-warning">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-label">Tổng doanh thu</div>
            <div class="stat-value text-success"><?php echo number_format($stats['total_revenue']); ?>₫</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-label">Doanh thu hôm nay</div>
            <div class="stat-value text-primary"><?php echo number_format($stats['today_revenue']); ?>₫</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart -->
    <div class="col-md-8">
        <div class="stat-card">
            <h5 class="mb-3">Doanh thu 7 ngày gần nhất</h5>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>
    
    <!-- Top Movies -->
    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="mb-3">Top phim bán chạy</h6>
            <?php if (empty($topMovies)): ?>
                <p class="text-muted">Chưa có dữ liệu</p>
            <?php else: ?>
                <ul class="list-unstyled">
                    <?php foreach ($topMovies as $movie): ?>
                        <li class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span><?php echo htmlspecialchars($movie['title']); ?></span>
                                <span class="badge bg-primary"><?php echo $movie['ticket_count']; ?> vé</span>
                            </div>
                            <small class="text-muted"><?php echo number_format($movie['revenue'] ?? 0); ?>₫</small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart');
if (ctx) {
    const revenueData = <?php echo json_encode($revenueByDay); ?>;
    
    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
    };
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => formatDate(item.date)),
            datasets: [{
                label: 'Doanh thu (₫)',
                data: revenueData.map(item => parseFloat(item.revenue || 0)),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.y;
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(value);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return (value / 1000000).toFixed(1) + 'M₫';
                            } else if (value >= 1000) {
                                return (value / 1000).toFixed(0) + 'K₫';
                            }
                            return value + '₫';
                        }
                    }
                }
            }
        }
    });
}
</script>

