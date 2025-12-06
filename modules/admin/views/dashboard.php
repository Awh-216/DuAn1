<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card card-primary">
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
        <div class="stat-card card-success">
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
        <div class="stat-card card-info">
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
        <div class="stat-card card-warning">
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
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Doanh thu hôm nay</div>
                    <div class="stat-value text-gradient-forest"><?php echo number_format($stats['today_revenue']); ?>₫</div>
                </div>
                <div class="stat-icon bg-success icon-bounce">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Doanh thu tuần này</div>
                    <div class="stat-value text-gradient-ocean"><?php echo number_format($stats['week_revenue']); ?>₫</div>
                </div>
                <div class="stat-icon bg-info icon-bounce">
                    <i class="fas fa-calendar-week"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Doanh thu tháng này</div>
                    <div class="stat-value text-gradient-gold"><?php echo number_format($stats['month_revenue']); ?>₫</div>
                </div>
                <div class="stat-icon bg-warning icon-bounce">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart - Enhanced -->
    <div class="col-md-8">
        <div class="stat-card glow-frame">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0" style="color: #333;">
                    <i class="fas fa-chart-line text-gradient-primary me-2"></i>
                    Doanh thu 7 ngày gần nhất
                </h5>
                <span class="badge bg-primary">
                    <i class="fas fa-calendar-week me-1"></i>
                    <?php echo date('d/m') . ' - ' . date('d/m', strtotime('-6 days')); ?>
                </span>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="revenueChart"></canvas>
            </div>
            <?php 
            $totalWeekRevenue = array_sum(array_column($revenueByDay ?? [], 'revenue'));
            $avgDayRevenue = $totalWeekRevenue / 7;
            ?>
            <div class="row mt-4 pt-3" style="border-top: 1px solid rgba(102, 126, 234, 0.2);">
                <div class="col-6">
                    <div class="text-center">
                        <small class="text-muted d-block">Tổng tuần</small>
                        <span class="h5 text-gradient-gold"><?php echo number_format($totalWeekRevenue); ?>₫</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <small class="text-muted d-block">Trung bình/ngày</small>
                        <span class="h5 text-gradient-ocean"><?php echo number_format($avgDayRevenue); ?>₫</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Movies -->
    <div class="col-md-4">
        <div class="stat-card mb-3">
            <h6 class="mb-3" style="color: #333;">
                <i class="fas fa-trophy text-warning me-2"></i>
                Top phim bán chạy
            </h6>
            <?php if (empty($topMovies)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-film fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có dữ liệu</p>
                </div>
            <?php else: ?>
                <ul class="list-unstyled">
                    <?php foreach ($topMovies as $index => $movie): ?>
                        <li class="mb-3 p-2 rounded" style="background: rgba(102, 126, 234, <?php echo 0.15 - ($index * 0.02); ?>); transition: all 0.3s;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge <?php echo $index === 0 ? 'bg-warning' : ($index === 1 ? 'bg-secondary' : ($index === 2 ? 'bg-danger' : 'bg-dark')); ?> me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <?php echo $index + 1; ?>
                                    </span>
                                    <span class="fw-bold" style="color: #333;"><?php echo htmlspecialchars($movie['title']); ?></span>
                                </div>
                                <span class="badge bg-primary"><?php echo $movie['view_count']; ?> lượt</span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <div class="stat-card">
            <h6 class="mb-3" style="color: #333;">
                <i class="fas fa-tachometer-alt text-info me-2"></i>
                Thống kê nhanh
            </h6>
            <div class="mb-3 p-2 rounded" style="background: rgba(13, 110, 253, 0.1);">
                <small class="text-muted">Người dùng hoạt động hôm nay</small>
                <div class="fw-bold text-primary" style="font-size: 1.2rem;"><?php echo $stats['active_users_today']; ?></div>
            </div>
            <div class="p-2 rounded" style="background: rgba(255, 193, 7, 0.1);">
                <small class="text-muted">Ticket hỗ trợ chờ xử lý</small>
                <div class="fw-bold text-warning" style="font-size: 1.2rem;"><?php echo $stats['pending_tickets']; ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Charts Row -->
<div class="row mt-4">
    <!-- Tickets by Day Chart -->
    <div class="col-md-6">
        <div class="stat-card">
            <h6 class="mb-3" style="color: #333;">
                <i class="fas fa-ticket-alt text-info me-2"></i>
                Số vé bán theo ngày
            </h6>
            <div style="position: relative; height: 250px;">
                <canvas id="ticketsChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Revenue Distribution by Theater -->
    <div class="col-md-6">
        <div class="stat-card">
            <h6 class="mb-3" style="color: #333;">
                <i class="fas fa-chart-pie text-success me-2"></i>
                Phân bố doanh thu theo phim
            </h6>
            <div style="position: relative; height: 250px;">
                <canvas id="movieRevenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Showtimes -->
<div class="row mt-4">
    <div class="col-12">
        <div class="stat-card">
            <h5 class="mb-3" style="color: #333;">
                <i class="fas fa-calendar-check text-primary me-2"></i>
                Suất chiếu sắp tới
            </h5>
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
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart - Enhanced with gradient
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        const revenueData = <?php echo json_encode($revenueByDay ?? []); ?>;
        
        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
        };
        
        // Gradient cho biểu đồ
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(102, 126, 234, 0.5)');
        gradient.addColorStop(1, 'rgba(102, 126, 234, 0.0)');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueData.map(item => formatDate(item.date)),
                datasets: [{
                    label: 'Doanh thu (₫)',
                    data: revenueData.map(item => parseFloat(item.revenue || 0)),
                    borderColor: '#667eea',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#667eea',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#495057',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(26, 26, 46, 0.95)',
                        titleColor: '#fff',
                        bodyColor: '#c5d3e8',
                        borderColor: 'rgba(102, 126, 234, 0.5)',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 16
                        },
                        callbacks: {
                            title: function(context) {
                                return '📅 ' + context[0].label;
                            },
                            label: function(context) {
                                const value = context.parsed.y;
                                return '💰 ' + new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(value);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 12
                            },
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
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }
    
    // Tickets Chart - Bar chart
    const ticketsCtx = document.getElementById('ticketsChart');
    if (ticketsCtx) {
        const revenueData = <?php echo json_encode($revenueByDay ?? []); ?>;
        
        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
        };
        
        // Tính số vé từ doanh thu (giả sử giá vé trung bình 150k)
        const ticketData = revenueData.map(item => Math.round(parseFloat(item.revenue || 0) / 150000));
        
        new Chart(ticketsCtx, {
            type: 'bar',
            data: {
                labels: revenueData.map(item => formatDate(item.date)),
                datasets: [{
                    label: 'Số vé',
                    data: ticketData,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(240, 147, 251, 0.8)',
                        'rgba(79, 172, 254, 0.8)',
                        'rgba(56, 239, 125, 0.8)',
                        'rgba(242, 153, 74, 0.8)',
                        'rgba(245, 87, 108, 0.8)'
                    ],
                    borderColor: [
                        '#667eea',
                        '#764ba2',
                        '#f093fb',
                        '#4facfe',
                        '#38ef7d',
                        '#f2994a',
                        '#f5576c'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(26, 26, 46, 0.95)',
                        titleColor: '#fff',
                        bodyColor: '#c5d3e8',
                        borderColor: 'rgba(102, 126, 234, 0.5)',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return '🎫 ' + context.parsed.y + ' vé';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)'
                        },
                        ticks: {
                            color: '#6c757d',
                            stepSize: 1
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutBounce'
                }
            }
        });
    }
    
    // Movie Revenue Pie Chart
    const movieCtx = document.getElementById('movieRevenueChart');
    if (movieCtx) {
        const topMovies = <?php echo json_encode($topMovies ?? []); ?>;
        
        if (topMovies.length > 0) {
            new Chart(movieCtx, {
                type: 'doughnut',
                data: {
                    labels: topMovies.map(m => m.title.length > 15 ? m.title.substring(0, 15) + '...' : m.title),
                    datasets: [{
                        data: topMovies.map(m => parseInt(m.view_count || 0)),
                        backgroundColor: [
                            'rgba(102, 126, 234, 0.8)',
                            'rgba(240, 147, 251, 0.8)',
                            'rgba(56, 239, 125, 0.8)',
                            'rgba(242, 153, 74, 0.8)',
                            'rgba(79, 172, 254, 0.8)'
                        ],
                        borderColor: [
                            '#667eea',
                            '#f093fb',
                            '#38ef7d',
                            '#f2994a',
                            '#4facfe'
                        ],
                        borderWidth: 3,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: '#495057',
                                padding: 15,
                                font: {
                                    size: 11
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(26, 26, 46, 0.95)',
                            titleColor: '#fff',
                            bodyColor: '#c5d3e8',
                            borderColor: 'rgba(102, 126, 234, 0.5)',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return '👁️ ' + value + ' lượt xem (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 2000
                    }
                }
            });
        } else {
            movieCtx.parentElement.innerHTML = '<div class="text-center py-5"><i class="fas fa-chart-pie fa-3x text-muted mb-3"></i><p class="text-muted">Chưa có dữ liệu</p></div>';
        }
    }
});
</script>
