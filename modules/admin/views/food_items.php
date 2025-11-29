<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý Combo & Đồ ăn</h5>
    <a href="?route=admin/foodItemsCreate" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm mới
    </a>
</div>

<!-- Filters -->
<div class="stat-card mb-3">
    <form method="GET" class="row g-2">
        <input type="hidden" name="route" value="admin/foodItems">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, mô tả..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="">Tất cả loại</option>
                <option value="combo" <?php echo (isset($type) && $type === 'combo') ? 'selected' : ''; ?>>Combo</option>
                <option value="snack" <?php echo (isset($type) && $type === 'snack') ? 'selected' : ''; ?>>Snack</option>
                <option value="drink" <?php echo (isset($type) && $type === 'drink') ? 'selected' : ''; ?>>Đồ uống</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="fas fa-search"></i> Tìm
            </button>
        </div>
        <div class="col-md-2">
            <a href="?route=admin/foodItems" class="btn btn-outline-secondary w-100">
                <i class="fas fa-redo"></i> Xóa lọc
            </a>
        </div>
    </form>
</div>

<!-- Food Items Table -->
<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên</th>
                    <th>Mô tả</th>
                    <th>Loại</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($foodItems)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Chưa có combo/đồ ăn nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($foodItems as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td>
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['description'] ?? ''); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $item['type'] === 'combo' ? 'primary' : 
                                        ($item['type'] === 'snack' ? 'warning' : 'info'); 
                                ?>">
                                    <?php 
                                    echo $item['type'] === 'combo' ? 'Combo' : 
                                        ($item['type'] === 'snack' ? 'Snack' : 'Đồ uống'); 
                                    ?>
                                </span>
                            </td>
                            <td><?php echo number_format($item['price']); ?>₫</td>
                            <td>
                                <span class="badge bg-<?php echo $item['is_active'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $item['is_active'] ? 'Hoạt động' : 'Ngừng bán'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="?route=admin/foodItemsEdit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="?route=admin/foodItemsDelete&id=<?php echo $item['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Bạn chắc chắn muốn xóa combo/đồ ăn này?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


