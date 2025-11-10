<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý rạp chiếu</h5>
    <a href="?route=admin/theaters/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm rạp mới
    </a>
</div>

<div class="row">
    <?php if (empty($theaters)): ?>
        <div class="col-12">
            <div class="stat-card text-center">
                <p class="text-muted">Chưa có rạp nào</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($theaters as $theater): ?>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <h6><?php echo htmlspecialchars($theater['name']); ?></h6>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($theater['location'] ?? 'N/A'); ?>
                    </p>
                    <?php if ($theater['phone']): ?>
                        <p class="text-muted mb-2">
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($theater['phone']); ?>
                        </p>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="?route=admin/theaters/edit&id=<?php echo $theater['id']; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="?route=admin/theaters/delete&id=<?php echo $theater['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                            <i class="fas fa-trash"></i> Xóa
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

