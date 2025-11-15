<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Analytics & Báo cáo</h5>
    <div>
        <a href="?route=admin/analytics&period=day" class="btn btn-sm <?php echo ($period ?? 'month') === 'day' ? 'btn-primary' : 'btn-outline-primary'; ?>">Ngày</a>
        <a href="?route=admin/analytics&period=week" class="btn btn-sm <?php echo ($period ?? 'month') === 'week' ? 'btn-primary' : 'btn-outline-primary'; ?>">Tuần</a>
        <a href="?route=admin/analytics&period=month" class="btn btn-sm <?php echo ($period ?? 'month') === 'month' ? 'btn-primary' : 'btn-outline-primary'; ?>">Tháng</a>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Tổng doanh thu</div>
            <div class="stat-value text-warning"><?php echo number_format($summaryStats['total_revenue'] ?? 0); ?>₫</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Tổng giao dịch</div>
            <div class="stat-value text-info"><?php echo number_format($summaryStats['total_transactions'] ?? 0); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Tổng vé bán</div>
            <div class="stat-value text-success"><?php echo number_format($summaryStats['total_tickets'] ?? 0); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Giá vé trung bình</div>
            <div class="stat-value text-primary"><?php echo number_format($summaryStats['avg_ticket_price'] ?? 0); ?>₫</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="stat-card">
            <h6 class="mb-3">Biểu đồ doanh thu theo <?php echo $period === 'day' ? 'ngày' : ($period === 'week' ? 'tuần' : 'tháng'); ?></h6>
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
                    <?php foreach ($topMoviesByRevenue as $index => $movie): ?>
                        <li class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">
                                        <span class="badge bg-secondary me-2">#<?php echo $index + 1; ?></span>
                                        <?php echo htmlspecialchars($movie['title']); ?>
                                    </div>
                                    <div class="text-success mt-1"><?php echo number_format($movie['revenue']); ?>₫</div>
                                    <small class="text-muted"><?php echo $movie['ticket_count']; ?> vé đã bán</small>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Revenue by Payment Method -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="stat-card">
            <h6 class="mb-3">Doanh thu theo phương thức thanh toán</h6>
            <canvas id="paymentMethodChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <h6 class="mb-3">Chi tiết phương thức thanh toán</h6>
            <?php if (empty($revenueByMethod)): ?>
                <p class="text-muted">Chưa có dữ liệu</p>
            <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Phương thức</th>
                            <th class="text-end">Doanh thu</th>
                            <th class="text-end">Số giao dịch</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($revenueByMethod as $method): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($method['method']); ?></td>
                                <td class="text-end text-success"><?php echo number_format($method['revenue']); ?>₫</td>
                                <td class="text-end"><?php echo number_format($method['count']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
    const revenueData = <?php echo json_encode($revenueData ?? []); ?>;
    
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueData.map(item => item.period),
            datasets: [{
                label: 'Doanh thu (₫)',
                data: revenueData.map(item => parseFloat(item.revenue || 0)),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            }, {
                label: 'Số giao dịch',
                data: revenueData.map(item => parseFloat(item.transaction_count || 0)),
                type: 'line',
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                yAxisID: 'y1',
                borderWidth: 2,
                tension: 0.4,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                const value = context.parsed.y;
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(value);
                            } else {
                                return 'Giao dịch: ' + context.parsed.y;
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return (value / 1000000).toFixed(1) + 'M₫';
                            } else if (value >= 1000) {
                                return (value / 1000).toFixed(0) + 'K₫';
                            }
                            return value + '₫';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Payment Method Chart
const paymentCtx = document.getElementById('paymentMethodChart');
if (paymentCtx) {
    const paymentData = <?php echo json_encode($revenueByMethod ?? []); ?>;
    
    const colors = [
        'rgba(255, 99, 132, 0.8)',
        'rgba(54, 162, 235, 0.8)',
        'rgba(255, 206, 86, 0.8)',
        'rgba(75, 192, 192, 0.8)',
        'rgba(153, 102, 255, 0.8)'
    ];
    
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: paymentData.map(item => item.method),
            datasets: [{
                label: 'Doanh thu (₫)',
                data: paymentData.map(item => parseFloat(item.revenue || 0)),
                backgroundColor: colors.slice(0, paymentData.length),
                borderColor: colors.map(c => c.replace('0.8', '1')),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return context.label + ': ' + new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(value) + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}
</script>

