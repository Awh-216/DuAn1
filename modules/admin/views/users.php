<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý người dùng</h5>
    <a href="?route=admin/users/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm người dùng
    </a>
</div>

<!-- Search -->
<form method="GET" class="mb-3">
    <input type="hidden" name="route" value="admin/users">
    <div class="row">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên hoặc email..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="fas fa-search"></i> Tìm
            </button>
        </div>
    </div>
</form>

<!-- Users Table -->
<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Gói đăng ký</th>
                    <th>Điểm</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">Không có người dùng nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($u['avatar']): ?>
                                        <img src="<?php echo htmlspecialchars($u['avatar']); ?>" alt="" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($u['name']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $u['role'] === 'admin' ? 'danger' : 'secondary'; ?>">
                                    <?php echo htmlspecialchars($u['role'] ?? 'user'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($u['subscription_name'] ?? 'Chưa có'); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo number_format($u['points'] ?? 0); ?> điểm</span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo ($u['is_active'] ?? true) ? 'success' : 'danger'; ?>">
                                    <?php echo ($u['is_active'] ?? true) ? 'Hoạt động' : 'Bị chặn'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="?route=admin/users/edit&id=<?php echo $u['id']; ?>" class="btn btn-outline-primary" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?route=admin/users/view&id=<?php echo $u['id']; ?>" class="btn btn-outline-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="openPointsModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['name']); ?>', <?php echo $u['points'] ?? 0; ?>)" class="btn btn-outline-success" title="Quản lý điểm">
                                        <i class="fas fa-coins"></i>
                                    </button>
                                    <?php if ($u['id'] != $user['id']): ?>
                                        <a href="?route=admin/users/block&id=<?php echo $u['id']; ?>" class="btn btn-outline-warning" title="Chặn/Mở khóa">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php endif; ?>
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
                        <a class="page-link" href="?route=admin/users&page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Points Management Modal -->
<div class="modal fade" id="pointsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quản lý điểm - <span id="pointsUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pointsForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="route" value="admin/users/updatePoints">
                    <input type="hidden" id="pointsUserId" name="user_id">
                    <div class="mb-3">
                        <label class="form-label">Điểm hiện tại</label>
                        <input type="text" id="currentPoints" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thao tác</label>
                        <select name="action" id="pointsAction" class="form-select" required>
                            <option value="set">Đặt số điểm</option>
                            <option value="add">Thêm điểm</option>
                            <option value="subtract">Trừ điểm</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điểm</label>
                        <input type="number" name="points" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPointsModal(userId, userName, currentPoints) {
    document.getElementById('pointsUserId').value = userId;
    document.getElementById('pointsUserName').textContent = userName;
    document.getElementById('currentPoints').value = currentPoints.toLocaleString('vi-VN');
    document.getElementById('pointsForm').querySelector('input[name="points"]').value = '';
    document.getElementById('pointsAction').value = 'set';
    
    const modal = new bootstrap.Modal(document.getElementById('pointsModal'));
    modal.show();
}
</script>

