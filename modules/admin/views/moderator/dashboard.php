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
        <div class="stat-card glow-frame">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
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
        <div class="stat-card">
            <h6 class="mb-3">
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
                                    <span class="fw-bold"><?php echo htmlspecialchars($movie['title']); ?></span>
                                </div>
                                <span class="badge bg-primary"><?php echo $movie['ticket_count']; ?> vé</span>
                            </div>
                            <div class="mt-1 ms-5">
                                <small class="text-gradient-gold fw-bold"><?php echo number_format($movie['revenue'] ?? 0); ?>₫</small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Additional Charts Row -->
<div class="row mt-4">
    <!-- Tickets by Day Chart -->
    <div class="col-md-6">
        <div class="stat-card">
            <h6 class="mb-3">
                <i class="fas fa-ticket-alt text-info me-2"></i>
                Số vé bán theo ngày
            </h6>
            <div style="position: relative; height: 250px;">
                <canvas id="ticketsChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Revenue Distribution -->
    <div class="col-md-6">
        <div class="stat-card">
            <h6 class="mb-3">
                <i class="fas fa-chart-pie text-success me-2"></i>
                Phân bố doanh thu theo phim
            </h6>
            <div style="position: relative; height: 250px;">
                <canvas id="movieRevenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Revenue Chart - Enhanced with dark theme
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        const revenueData = <?php echo json_encode($revenueByDay ?? []); ?>;
        
        // Kiểm tra dữ liệu
        console.log('Revenue Data:', revenueData);
        
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
                maintainAspectRatio: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#c5d3e8',
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
                            color: '#8fa4bd',
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
                            color: '#8fa4bd',
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
                            color: '#8fa4bd'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(102, 126, 234, 0.1)'
                        },
                        ticks: {
                            color: '#8fa4bd',
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
                        data: topMovies.map(m => parseFloat(m.revenue || 0)),
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
                                color: '#c5d3e8',
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
                                    return '💰 ' + new Intl.NumberFormat('vi-VN').format(value) + '₫ (' + percentage + '%)';
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



