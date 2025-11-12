<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Tổng người dùng</div>
                    <div class="stat-value text-primary"><?php echo number_format($stats['total_users']); ?></div>
                </div>
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Tổng phim</div>
                    <div class="stat-value text-success"><?php echo number_format($stats['total_movies']); ?></div>
                </div>
                <div class="stat-icon bg-success">
                    <i class="fas fa-film"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Vé đã bán</div>
                    <div class="stat-value text-info"><?php echo number_format($stats['total_tickets']); ?></div>
                </div>
                <div class="stat-icon bg-info">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Tổng doanh thu</div>
                    <div class="stat-value text-warning"><?php echo number_format($stats['total_revenue']); ?>₫</div>
                </div>
                <div class="stat-icon bg-warning">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Doanh thu hôm nay</div>
            <div class="stat-value text-success"><?php echo number_format($stats['today_revenue']); ?>₫</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Doanh thu tuần này</div>
            <div class="stat-value text-info"><?php echo number_format($stats['week_revenue']); ?>₫</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Doanh thu tháng này</div>
            <div class="stat-value text-primary"><?php echo number_format($stats['month_revenue']); ?>₫</div>
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
    
    <!-- Quick Stats -->
    <div class="col-md-4">
        <div class="stat-card mb-3">
            <h6 class="mb-3">Thống kê nhanh</h6>
            <div class="mb-2">
                <small class="text-muted">Người dùng hoạt động hôm nay</small>
                <div class="fw-bold"><?php echo $stats['active_users_today']; ?></div>
            </div>
            <div class="mb-2">
                <small class="text-muted">Ticket hỗ trợ chờ xử lý</small>
                <div class="fw-bold text-warning"><?php echo $stats['pending_tickets']; ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <h6 class="mb-3">Top phim xem nhiều</h6>
            <?php if (empty($topMovies)): ?>
                <p class="text-muted">Chưa có dữ liệu</p>
            <?php else: ?>
                <ul class="list-unstyled">
                    <?php foreach ($topMovies as $movie): ?>
                        <li class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span><?php echo htmlspecialchars($movie['title']); ?></span>
                                <span class="badge bg-primary"><?php echo $movie['view_count']; ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Upcoming Showtimes -->
<div class="row mt-4">
    <div class="col-12">
        <div class="stat-card">
            <h5 class="mb-3">Suất chiếu sắp tới</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Phim</th>
                            <th>Rạp</th>
                            <th>Ngày</th>
                            <th>Giờ</th>
                            <th>Giá vé</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcomingShowtimes)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Không có suất chiếu sắp tới</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($upcomingShowtimes as $showtime): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($showtime['movie_title']); ?></td>
                                    <td><?php echo htmlspecialchars($showtime['theater_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($showtime['show_date'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($showtime['show_time'])); ?></td>
                                    <td><?php echo number_format($showtime['price']); ?>₫</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart');
if (ctx) {
    const revenueData = <?php echo json_encode($revenueByDay); ?>;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => item.date),
            datasets: [{
                label: 'Doanh thu (₫)',
                data: revenueData.map(item => parseFloat(item.revenue || 0)),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
</script>

