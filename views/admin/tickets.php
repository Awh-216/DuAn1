<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý vé</h5>
</div>

<!-- Filters -->
<form method="GET" class="mb-3">
    <input type="hidden" name="route" value="admin/tickets">
    <div class="row g-2">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="Đã đặt" <?php echo ($status ?? '') === 'Đã đặt' ? 'selected' : ''; ?>>Đã đặt</option>
                <option value="Đã hủy" <?php echo ($status ?? '') === 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="fas fa-filter"></i> Lọc
            </button>
        </div>
    </div>
</form>

<!-- Tickets Table -->
<div class="stat-card">
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
                        <td colspan="10" class="text-center text-muted">Không có vé nào</td>
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
                                        <a href="?route=admin/tickets/cancel&id=<?php echo $ticket['id']; ?>" class="btn btn-outline-warning" title="Hủy vé" onclick="return confirm('Bạn chắc chắn muốn hủy vé?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <a href="?route=admin/tickets/refund&id=<?php echo $ticket['id']; ?>" class="btn btn-outline-danger" title="Hoàn tiền" onclick="return confirm('Bạn chắc chắn muốn hoàn tiền?')">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?route=admin/tickets/view&id=<?php echo $ticket['id']; ?>" class="btn btn-outline-info" title="Xem chi tiết">
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
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($page ?? 1) == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?route=admin/tickets&page=<?php echo $i; ?>&status=<?php echo urlencode($status ?? ''); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

