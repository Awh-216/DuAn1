<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Tổng số vé</div>
                <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
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
                <div class="stat-value"><?php echo number_format($stats['sold']); ?></div>
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
                <div class="stat-value"><?php echo number_format($stats['cancelled']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Chờ thanh toán</div>
                <div class="stat-value"><?php echo number_format($stats['pending']); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Doanh thu -->
<?php if (isset($stats['revenue']) && $stats['revenue'] > 0): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Tổng doanh thu</div>
                <div class="stat-value"><?php echo number_format($stats['revenue']); ?>₫</div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filters -->
<form method="GET" class="mb-4">
    <input type="hidden" name="route" value="moderator/tickets">
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
                    <option value="Chờ thanh toán" <?php echo ($status ?? '') === 'Chờ thanh toán' ? 'selected' : ''; ?>>Chờ thanh toán</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a href="?route=moderator/tickets" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Xóa bộ lọc
                </a>
            </div>
        </div>
    </div>
</form>

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
                    <th>Khách hàng</th>
                    <th>Phim</th>
                    <th>Ngày/Giờ</th>
                    <th>Ghế</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Không có vé nào
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>#<?php echo $ticket['id']; ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($ticket['user_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($ticket['user_email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['movie_title']); ?></td>
                            <td>
                                <div><?php echo date('d/m/Y', strtotime($ticket['show_date'])); ?></div>
                                <small class="text-muted"><?php echo date('H:i', strtotime($ticket['show_time'])); ?></small>
                            </td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($ticket['seat']); ?></span></td>
                            <td><?php echo number_format($ticket['price']); ?>₫</td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $ticket['status'] === 'Đã đặt' ? 'success' : 
                                        ($ticket['status'] === 'Đã hủy' ? 'danger' : 'warning'); 
                                ?>">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?></td>
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
                           href="?route=moderator/tickets&page=<?php echo $i; ?>&status=<?php echo urlencode($status ?? ''); ?>&movie_id=<?php echo urlencode($movie_id ?? ''); ?>">
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

.row > .col-md-3 .stat-card,
.row > .col-md-12 .stat-card {
    display: flex;
    align-items: center;
    margin-bottom: 0;
}
</style>



