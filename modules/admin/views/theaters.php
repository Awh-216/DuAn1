<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý rạp chiếu</h5>
    <?php if (!isset($user['role']) || $user['role'] !== 'moderator'): ?>
        <a href="?route=admin/theaters/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm rạp mới
        </a>
    <?php endif; ?>
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
                    <?php if (!empty($theater['image'])): ?>
                        <div class="mb-3" style="width: 100%; height: 200px; overflow: hidden; border-radius: 8px; margin-bottom: 15px;">
                            <img src="<?php echo htmlspecialchars($theater['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($theater['name']); ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php else: ?>
                        <div class="mb-3" style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="fas fa-building" style="font-size: 3rem; color: rgba(255,255,255,0.5);"></i>
                        </div>
                    <?php endif; ?>
                    <h6 style="color: #000; font-weight: bold; margin-bottom: 10px;"><?php echo htmlspecialchars($theater['name']); ?></h6>
                    <p class="mb-2" style="color: #000;">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($theater['location'] ?? 'N/A'); ?>
                    </p>
                    <?php if ($theater['phone']): ?>
                        <p class="mb-2" style="color: #000;">
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($theater['phone']); ?>
                        </p>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="?route=admin/theaters/view&id=<?php echo $theater['id']; ?>" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-eye"></i> Xem thông tin rạp
                        </a>
                        <?php 
                        // Chỉ moderator của rạp này mới được sửa/xóa
                        $canEdit = false;
                        if (isset($user['role']) && $user['role'] === 'moderator' && isset($user['theater_id']) && $user['theater_id'] == $theater['id']) {
                            $canEdit = true;
                        }
                        ?>
                        <?php if ($canEdit): ?>
                            <div class="mt-2 d-flex gap-2">
                                <a href="?route=admin/theaters/edit&id=<?php echo $theater['id']; ?>" class="btn btn-sm btn-outline-warning flex-fill">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="?route=admin/theaters/delete&id=<?php echo $theater['id']; ?>" class="btn btn-sm btn-outline-danger flex-fill" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

