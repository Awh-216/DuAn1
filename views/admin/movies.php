<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý phim</h5>
    <a href="?route=admin/movies/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm phim mới
    </a>
</div>

<!-- Filters -->
<form method="GET" class="mb-3">
    <input type="hidden" name="route" value="admin/movies">
    <div class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm phim..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="draft" <?php echo ($status ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                <option value="scheduled" <?php echo ($status ?? '') === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                <option value="published" <?php echo ($status ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                <option value="archived" <?php echo ($status ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="fas fa-search"></i> Tìm
            </button>
        </div>
    </div>
</form>

<!-- Movies Table -->
<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Tiêu đề</th>
                    <th>Thể loại</th>
                    <th>Trạng thái</th>
                    <th>Rating</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movies)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Không có phim nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($movies as $m): ?>
                        <tr>
                            <td><?php echo $m['id']; ?></td>
                            <td>
                                <?php if ($m['thumbnail']): ?>
                                    <img src="<?php echo htmlspecialchars($m['thumbnail']); ?>" alt="" style="width: 60px; height: 90px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <div class="bg-secondary d-flex align-items-center justify-content-center" style="width: 60px; height: 90px; border-radius: 5px;">
                                        <i class="fas fa-film text-white"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($m['title']); ?></strong>
                                <?php if ($m['director']): ?>
                                    <br><small class="text-muted">Đạo diễn: <?php echo htmlspecialchars($m['director']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($m['category_name'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($m['status_admin'] ?? 'draft') {
                                        'published' => 'success',
                                        'scheduled' => 'info',
                                        'archived' => 'secondary',
                                        default => 'warning'
                                    };
                                ?>">
                                    <?php echo htmlspecialchars($m['status_admin'] ?? 'draft'); ?>
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-star text-warning"></i> <?php echo number_format($m['rating'], 1); ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($m['created_at'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="?route=admin/movies/edit&id=<?php echo $m['id']; ?>" class="btn btn-outline-primary" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?route=movie/watch&id=<?php echo $m['id']; ?>" class="btn btn-outline-info" title="Xem" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?route=admin/movies/delete&id=<?php echo $m['id']; ?>" class="btn btn-outline-danger" title="Xóa" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                        <i class="fas fa-trash"></i>
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
                        <a class="page-link" href="?route=admin/movies&page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

