<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Hỗ trợ khách hàng</h5>
</div>

<!-- Filters -->
<form method="GET" class="mb-3">
    <input type="hidden" name="route" value="admin/support">
    <div class="row g-2">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="Mới" <?php echo ($status ?? '') === 'Mới' ? 'selected' : ''; ?>>Mới</option>
                <option value="Đang xử lý" <?php echo ($status ?? '') === 'Đang xử lý' ? 'selected' : ''; ?>>Đang xử lý</option>
                <option value="Đã giải quyết" <?php echo ($status ?? '') === 'Đã giải quyết' ? 'selected' : ''; ?>>Đã giải quyết</option>
                <option value="Đã đóng" <?php echo ($status ?? '') === 'Đã đóng' ? 'selected' : ''; ?>>Đã đóng</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="priority" class="form-select">
                <option value="">Tất cả độ ưu tiên</option>
                <option value="Khẩn cấp" <?php echo ($priority ?? '') === 'Khẩn cấp' ? 'selected' : ''; ?>>Khẩn cấp</option>
                <option value="Cao" <?php echo ($priority ?? '') === 'Cao' ? 'selected' : ''; ?>>Cao</option>
                <option value="Trung bình" <?php echo ($priority ?? '') === 'Trung bình' ? 'selected' : ''; ?>>Trung bình</option>
                <option value="Thấp" <?php echo ($priority ?? '') === 'Thấp' ? 'selected' : ''; ?>>Thấp</option>
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
                    <th>Tiêu đề</th>
                    <th>Trạng thái</th>
                    <th>Độ ưu tiên</th>
                    <th>Người xử lý</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Không có ticket nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo $ticket['id']; ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($ticket['user_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($ticket['user_email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($ticket['status']) {
                                        'Mới' => 'primary',
                                        'Đang xử lý' => 'warning',
                                        'Đã giải quyết' => 'success',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($ticket['priority']) {
                                        'Khẩn cấp' => 'danger',
                                        'Cao' => 'warning',
                                        'Trung bình' => 'info',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo htmlspecialchars($ticket['priority']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['assigned_name'] ?? 'Chưa gán'); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?></td>
                            <td>
                                <a href="?route=admin/support/view&id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

