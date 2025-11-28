<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Tổng số vé</div>
            <div class="stat-value text-primary"><?php echo number_format($stats['total']); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Vé đã bán</div>
            <div class="stat-value text-success"><?php echo number_format($stats['sold']); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Vé đã hủy</div>
            <div class="stat-value text-danger"><?php echo number_format($stats['cancelled']); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Chờ thanh toán</div>
            <div class="stat-value text-warning"><?php echo number_format($stats['pending']); ?></div>
        </div>
    </div>
</div>

<div class="stat-card">
    <h5 class="mb-3">Danh sách vé</h5>
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
                        <td colspan="8" class="text-center text-muted">Chưa có vé nào</td>
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
                            <td><?php echo htmlspecialchars($ticket['seat']); ?></td>
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
</div>


