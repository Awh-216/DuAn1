<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý vé</h5>
</div>

<!-- Thống kê tổng quan -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Tổng số vé</div>
                <div class="stat-value"><?php echo number_format($overallStats['total_tickets'] ?? 0); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Vé đã bán</div>
                <div class="stat-value"><?php echo number_format($overallStats['tickets_sold'] ?? 0); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Vé đã hủy</div>
                <div class="stat-value"><?php echo number_format($overallStats['tickets_cancelled'] ?? 0); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Doanh thu</div>
                <div class="stat-value"><?php echo number_format($overallStats['total_revenue'] ?? 0); ?>₫</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<form method="GET" class="mb-4">
    <input type="hidden" name="route" value="admin/tickets">
    <div class="stat-card">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Lọc theo phim</label>
                <select name="movie_id" class="form-select">
                    <option value="">Tất cả phim</option>
                    <?php foreach ($movies ?? [] as $movie): ?>
                        <option value="<?php echo $movie['id']; ?>" <?php echo ($movie_id ?? '') == $movie['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($movie['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Lọc theo trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Đã đặt" <?php echo ($status ?? '') === 'Đã đặt' ? 'selected' : ''; ?>>Đã đặt</option>
                    <option value="Đã hủy" <?php echo ($status ?? '') === 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a href="?route=admin/tickets" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Xóa bộ lọc
                </a>
            </div>
        </div>
    </div>
</form>

<!-- Phân loại vé theo phim - Thống kê tồn kho -->
<?php if (!empty($inventoryStats)): ?>
<div class="stat-card mb-4">
    <h6 class="mb-3">
        <i class="fas fa-chart-bar me-2"></i>Phân loại vé theo phim - Thống kê tồn kho
    </h6>
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Phim</th>
                    <th>Số suất chiếu</th>
                    <th>Vé đã bán</th>
                    <th>Vé tồn kho</th>
                    <th>Doanh thu</th>
                    <th>Tỷ lệ bán</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventoryStats as $stat): ?>
                    <?php 
                    $total_seats = $stat['total_showtimes'] * 132; // Mỗi showtime có 132 ghế (12 hàng x 12 cột)
                    $sold = $stat['tickets_sold'] ?? 0;
                    $available = $stat['tickets_available'] ?? $total_seats;
                    $revenue = $stat['total_revenue'] ?? 0;
                    $sell_rate = $total_seats > 0 ? round(($sold / $total_seats) * 100, 2) : 0;
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($stat['movie_title']); ?></strong>
                        </td>
                        <td><?php echo number_format($stat['total_showtimes']); ?></td>
                        <td>
                            <span class="badge bg-success"><?php echo number_format($sold); ?></span>
                        </td>
                        <td>
                            <span class="badge <?php echo $available > 50 ? 'bg-info' : ($available > 20 ? 'bg-warning' : 'bg-danger'); ?>">
                                <?php echo number_format($available); ?>
                            </span>
                        </td>
                        <td><?php echo number_format($revenue); ?>₫</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar <?php echo $sell_rate >= 80 ? 'bg-success' : ($sell_rate >= 50 ? 'bg-warning' : 'bg-danger'); ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo $sell_rate; ?>%"
                                     aria-valuenow="<?php echo $sell_rate; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <?php echo $sell_rate; ?>%
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="?route=admin/tickets&movie_id=<?php echo $stat['movie_id']; ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Xem vé
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Danh sách vé -->
<div class="stat-card">
    <h6 class="mb-3">
        <i class="fas fa-list me-2"></i>Danh sách vé
        <?php if ($movie_id): ?>
            <span class="badge bg-info">Phim: <?php echo htmlspecialchars(array_column(array_filter($movies ?? [], function($m) use ($movie_id) { return $m['id'] == $movie_id; }), 'title')[0] ?? ''); ?></span>
        <?php endif; ?>
    </h6>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Người dùng</th>
                    <th>Phim</th>
                    <th>Rạp</th>
                    <th>Ngày/Giờ</th>
                    <th>Ghế</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>QR Code</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Không có vé nào
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo $ticket['id']; ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($ticket['user_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($ticket['user_email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['movie_title']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['theater_name']); ?></td>
                            <td>
                                <div><?php echo date('d/m/Y', strtotime($ticket['show_date'])); ?></div>
                                <small class="text-muted"><?php echo date('H:i', strtotime($ticket['show_time'])); ?></small>
                            </td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($ticket['seat']); ?></span></td>
                            <td><?php echo number_format($ticket['price']); ?>₫</td>
                            <td>
                                <span class="badge bg-<?php echo $ticket['status'] === 'Đã đặt' ? 'success' : 'danger'; ?>">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($ticket['qr_code']): ?>
                                    <small class="text-muted"><?php echo substr($ticket['qr_code'], 0, 15); ?>...</small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($ticket['status'] === 'Đã đặt'): ?>
                                        <a href="?route=admin/tickets/cancel&id=<?php echo $ticket['id']; ?>" 
                                           class="btn btn-outline-warning" 
                                           title="Hủy vé" 
                                           onclick="return confirm('Bạn chắc chắn muốn hủy vé?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <a href="?route=admin/tickets/refund&id=<?php echo $ticket['id']; ?>" 
                                           class="btn btn-outline-danger" 
                                           title="Hoàn tiền" 
                                           onclick="return confirm('Bạn chắc chắn muốn hoàn tiền?')">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?route=admin/tickets/view&id=<?php echo $ticket['id']; ?>" 
                                       class="btn btn-outline-info" 
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if (isset($total_pages) && $total_pages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($page ?? 1) == $i ? 'active' : ''; ?>">
                        <a class="page-link" 
                           href="?route=admin/tickets&page=<?php echo $i; ?>&status=<?php echo urlencode($status ?? ''); ?>&movie_id=<?php echo urlencode($movie_id ?? ''); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<style>
.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-right: 15px;
}

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.row > .col-md-3 .stat-card {
    display: flex;
    align-items: center;
    margin-bottom: 0;
}
</style>
