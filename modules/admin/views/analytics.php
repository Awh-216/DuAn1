<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Analytics & Báo cáo</h5>
    <div>
        <a href="?route=admin/analytics&period=day" class="btn btn-sm <?php echo ($period ?? 'month') === 'day' ? 'btn-primary' : 'btn-outline-primary'; ?>">Ngày</a>
        <a href="?route=admin/analytics&period=week" class="btn btn-sm <?php echo ($period ?? 'month') === 'week' ? 'btn-primary' : 'btn-outline-primary'; ?>">Tuần</a>
        <a href="?route=admin/analytics&period=month" class="btn btn-sm <?php echo ($period ?? 'month') === 'month' ? 'btn-primary' : 'btn-outline-primary'; ?>">Tháng</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="stat-card">
            <h6 class="mb-3">Biểu đồ doanh thu</h6>
            <canvas id="revenueChart" height="80"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="mb-3">Top phim doanh thu cao</h6>
            <?php if (empty($topMoviesByRevenue)): ?>
                <p class="text-muted">Chưa có dữ liệu</p>
            <?php else: ?>
                <ul class="list-unstyled">
                    <?php foreach ($topMoviesByRevenue as $movie): ?>
                        <li class="mb-3 pb-3 border-bottom">
                            <div class="fw-bold"><?php echo htmlspecialchars($movie['title']); ?></div>
                            <div class="text-success"><?php echo number_format($movie['revenue']); ?>₫</div>
                            <small class="text-muted"><?php echo $movie['ticket_count']; ?> vé</small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('revenueChart');
if (ctx) {
    const revenueData = <?php echo json_encode($revenueData); ?>;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: revenueData.map(item => item.period),
            datasets: [{
                label: 'Doanh thu (₫)',
                data: revenueData.map(item => parseFloat(item.revenue || 0)),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
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

