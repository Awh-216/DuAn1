<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>System Logs (Audit Trail)</h5>
</div>

<!-- Filters -->
<form method="GET" class="mb-3">
    <input type="hidden" name="route" value="admin/logs">
    <div class="row g-2">
        <div class="col-md-3">
            <select name="module" class="form-select">
                <option value="">Tất cả modules</option>
                <option value="users" <?php echo ($module ?? '') === 'users' ? 'selected' : ''; ?>>Users</option>
                <option value="movies" <?php echo ($module ?? '') === 'movies' ? 'selected' : ''; ?>>Movies</option>
                <option value="tickets" <?php echo ($module ?? '') === 'tickets' ? 'selected' : ''; ?>>Tickets</option>
                <option value="theaters" <?php echo ($module ?? '') === 'theaters' ? 'selected' : ''; ?>>Theaters</option>
                <option value="system" <?php echo ($module ?? '') === 'system' ? 'selected' : ''; ?>>System</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="fas fa-filter"></i> Lọc
            </button>
        </div>
    </div>
</form>

<!-- Logs Table -->
<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Người thực hiện</th>
                    <th>Hành động</th>
                    <th>Module</th>
                    <th>Đối tượng</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Không có log nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($log['user_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($log['user_email']); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($log['action']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($log['module']); ?></td>
                            <td>
                                <?php if ($log['target_type'] && $log['target_id']): ?>
                                    <?php echo htmlspecialchars($log['target_type']); ?> #<?php echo $log['target_id']; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?php echo htmlspecialchars($log['ip_address'] ?? 'N/A'); ?></small></td>
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
                        <a class="page-link" href="?route=admin/logs&page=<?php echo $i; ?>&module=<?php echo urlencode($module ?? ''); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

