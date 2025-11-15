<div class="d-flex justify-content-between align-items-center mb-4">
    <h5><i class="fas fa-history"></i> Lịch sử hoạt động</h5>
    <small class="text-muted">Xem lịch sử thêm, xóa, cập nhật phim, rạp, bình luận</small>
</div>

<!-- Filters -->
<form method="GET" class="mb-3">
    <input type="hidden" name="route" value="admin/logs">
    <div class="row g-2">
        <div class="col-md-4">
            <label class="form-label small">Module</label>
            <select name="module" class="form-select" id="moduleFilter" onchange="this.form.submit()">
                <option value="">Tất cả modules</option>
                <option value="Movie" <?php echo (isset($module) && $module === 'Movie') ? 'selected' : ''; ?>>Phim</option>
                <option value="Theater" <?php echo (isset($module) && $module === 'Theater') ? 'selected' : ''; ?>>Rạp</option>
                <option value="Review" <?php echo (isset($module) && $module === 'Review') ? 'selected' : ''; ?>>Bình luận</option>
                <option value="User" <?php echo (isset($module) && $module === 'User') ? 'selected' : ''; ?>>Người dùng</option>
                <option value="System" <?php echo (isset($module) && $module === 'System') ? 'selected' : ''; ?>>Hệ thống</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small">Hành động</label>
            <select name="action" class="form-select" id="actionFilter" onchange="this.form.submit()">
                <option value="">Tất cả hành động</option>
                <option value="Thêm" <?php echo (isset($action) && strpos($action, 'Thêm') !== false) ? 'selected' : ''; ?>>Thêm</option>
                <option value="Xóa" <?php echo (isset($action) && strpos($action, 'Xóa') !== false) ? 'selected' : ''; ?>>Xóa</option>
                <option value="Cập nhật" <?php echo (isset($action) && strpos($action, 'Cập nhật') !== false) ? 'selected' : ''; ?>>Cập nhật</option>
                <option value="Ghim" <?php echo (isset($action) && strpos($action, 'Ghim') !== false) ? 'selected' : ''; ?>>Ghim/Bỏ ghim</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">&nbsp;</label>
            <button type="submit" class="btn btn-secondary w-100">
                <i class="fas fa-filter"></i> Lọc
            </button>
        </div>
        <div class="col-md-2">
            <label class="form-label small">&nbsp;</label>
            <a href="?route=admin/logs" class="btn btn-outline-secondary w-100">
                <i class="fas fa-redo"></i> Xóa lọc
            </a>
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
                                <div><?php echo htmlspecialchars($log['user_name'] ?? 'N/A'); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($log['user_email'] ?? 'N/A'); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($log['action']); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    $moduleColor = 'secondary';
                                    if ($log['module'] === 'Movie') {
                                        $moduleColor = 'primary';
                                    } elseif ($log['module'] === 'Theater') {
                                        $moduleColor = 'info';
                                    } elseif ($log['module'] === 'Review') {
                                        $moduleColor = 'success';
                                    } elseif ($log['module'] === 'User') {
                                        $moduleColor = 'warning';
                                    }
                                    echo $moduleColor;
                                ?>">
                                    <?php echo htmlspecialchars($log['module']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($log['target_type'] && $log['target_id']): ?>
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($log['target_type']); ?> #<?php echo $log['target_id']; ?>
                                    </span>
                                    <?php if ($log['old_data'] || $log['new_data']): ?>
                                        <button type="button" class="btn btn-sm btn-link p-0 ms-1" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#logDetails<?php echo $log['id']; ?>"
                                                aria-expanded="false"
                                                aria-controls="logDetails<?php echo $log['id']; ?>"
                                                title="Xem chi tiết">
                                            <i class="fas fa-info-circle text-info"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?php echo htmlspecialchars($log['ip_address'] ?? 'N/A'); ?></small></td>
                        </tr>
                        <?php if (($log['old_data'] || $log['new_data']) && ($log['target_type'] && $log['target_id'])): ?>
                            <tr>
                                <td colspan="6" class="p-0">
                                    <div class="collapse" id="logDetails<?php echo $log['id']; ?>">
                                        <div class="card card-body bg-light m-2">
                                            <?php
                                            // Xử lý dữ liệu JSON
                                            $oldDataJson = null;
                                            $newDataJson = null;
                                            
                                            if ($log['old_data']) {
                                                $oldDataJson = json_decode($log['old_data'], true);
                                                if (json_last_error() !== JSON_ERROR_NONE) {
                                                    $oldDataJson = null;
                                                }
                                            }
                                            
                                            if ($log['new_data']) {
                                                $newDataJson = json_decode($log['new_data'], true);
                                                if (json_last_error() !== JSON_ERROR_NONE) {
                                                    $newDataJson = null;
                                                }
                                            }
                                            
                                            // Kiểm tra nếu là review (bình luận) để hiển thị đặc biệt
                                            $isReview = ($log['module'] === 'Review' || $log['target_type'] === 'review');
                                            $isDeleteAction = strpos($log['action'], 'Xóa') !== false;
                                            
                                            if ($isReview && $isDeleteAction && $oldDataJson): ?>
                                                <!-- Hiển thị chi tiết bình luận đã xóa -->
                                                <div class="review-deleted-details">
                                                    <h6 class="text-danger mb-3">
                                                        <i class="fas fa-trash"></i> Chi tiết bình luận đã xóa
                                                    </h6>
                                                    
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <tbody>
                                                                <tr>
                                                                    <th class="bg-light" style="width: 150px;">ID Bình luận:</th>
                                                                    <td><strong>#<?php echo htmlspecialchars($oldDataJson['review_id'] ?? $log['target_id']); ?></strong></td>
                                                                </tr>
                                                                <?php if (isset($oldDataJson['user_name'])): ?>
                                                                <tr>
                                                                    <th class="bg-light">Người bình luận:</th>
                                                                    <td><?php echo htmlspecialchars($oldDataJson['user_name']); ?> (ID: <?php echo htmlspecialchars($oldDataJson['user_id'] ?? 'N/A'); ?>)</td>
                                                                </tr>
                                                                <?php endif; ?>
                                                                <?php if (isset($oldDataJson['movie_title'])): ?>
                                                                <tr>
                                                                    <th class="bg-light">Phim:</th>
                                                                    <td><?php echo htmlspecialchars($oldDataJson['movie_title']); ?> (ID: <?php echo htmlspecialchars($oldDataJson['movie_id'] ?? 'N/A'); ?>)</td>
                                                                </tr>
                                                                <?php endif; ?>
                                                                <?php if (isset($oldDataJson['rating'])): ?>
                                                                <tr>
                                                                    <th class="bg-light">Đánh giá:</th>
                                                                    <td>
                                                                        <?php for ($i = 0; $i < intval($oldDataJson['rating']); $i++): ?>
                                                                            <i class="fas fa-star text-warning"></i>
                                                                        <?php endfor; ?>
                                                                        <span class="ms-2"><?php echo htmlspecialchars($oldDataJson['rating']); ?>/5 sao</span>
                                                                    </td>
                                                                </tr>
                                                                <?php endif; ?>
                                                                <?php if (isset($oldDataJson['comment']) && !empty($oldDataJson['comment'])): ?>
                                                                <tr>
                                                                    <th class="bg-light">Nội dung bình luận:</th>
                                                                    <td>
                                                                        <div class="bg-white p-3 rounded border">
                                                                            <?php echo nl2br(htmlspecialchars($oldDataJson['comment'])); ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php endif; ?>
                                                                <?php if (isset($oldDataJson['created_at'])): ?>
                                                                <tr>
                                                                    <th class="bg-light">Ngày tạo:</th>
                                                                    <td><?php echo date('d/m/Y H:i:s', strtotime($oldDataJson['created_at'])); ?></td>
                                                                </tr>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <!-- Hiển thị dữ liệu dạng JSON cho các trường hợp khác -->
                                                <div class="row">
                                                    <?php if ($oldDataJson || $log['old_data']): ?>
                                                        <div class="<?php echo $newDataJson || $log['new_data'] ? 'col-md-6' : 'col-md-12'; ?>">
                                                            <h6 class="text-danger"><i class="fas fa-arrow-left"></i> Dữ liệu cũ:</h6>
                                                            <?php if ($oldDataJson): ?>
                                                                <pre class="bg-white p-2 rounded small" style="max-height: 300px; overflow-y: auto;"><?php echo htmlspecialchars(json_encode($oldDataJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                                                            <?php else: ?>
                                                                <pre class="bg-white p-2 rounded small text-danger"><?php echo htmlspecialchars($log['old_data']); ?></pre>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($newDataJson || $log['new_data']): ?>
                                                        <div class="<?php echo $oldDataJson || $log['old_data'] ? 'col-md-6' : 'col-md-12'; ?>">
                                                            <h6 class="text-success"><i class="fas fa-arrow-right"></i> Dữ liệu mới:</h6>
                                                            <?php if ($newDataJson): ?>
                                                                <pre class="bg-white p-2 rounded small" style="max-height: 300px; overflow-y: auto;"><?php echo htmlspecialchars(json_encode($newDataJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                                                            <?php else: ?>
                                                                <pre class="bg-white p-2 rounded small text-danger"><?php echo htmlspecialchars($log['new_data']); ?></pre>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if (isset($total_pages) && $total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?route=admin/logs&page=<?php echo $page - 1; ?>&module=<?php echo urlencode($module ?? ''); ?>&action=<?php echo urlencode($action ?? ''); ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php 
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                for ($i = $start; $i <= $end; $i++): 
                ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?route=admin/logs&page=<?php echo $i; ?>&module=<?php echo urlencode($module ?? ''); ?>&action=<?php echo urlencode($action ?? ''); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?route=admin/logs&page=<?php echo $page + 1; ?>&module=<?php echo urlencode($module ?? ''); ?>&action=<?php echo urlencode($action ?? ''); ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
    
    <?php if (isset($total)): ?>
        <div class="text-center text-muted mt-3">
            Hiển thị <?php echo min($limit ?? 50, count($logs)); ?> / <?php echo $total; ?> log
        </div>
    <?php endif; ?>
</div>

<script>
// Đảm bảo Bootstrap collapse hoạt động
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo collapse cho tất cả các nút
    var collapseElements = document.querySelectorAll('[data-bs-toggle="collapse"]');
    collapseElements.forEach(function(element) {
        element.addEventListener('click', function(e) {
            var targetId = this.getAttribute('data-bs-target');
            var targetElement = document.querySelector(targetId);
            if (targetElement) {
                var bsCollapse = new bootstrap.Collapse(targetElement, {
                    toggle: true
                });
            }
        });
    });
});
</script>

