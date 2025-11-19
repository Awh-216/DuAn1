<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý phim</h5>
    <div>
        <a href="?route=admin/movies/scanEpisodes" class="btn btn-info me-2">
            <i class="fas fa-folder-open"></i> Import tập từ folder
        </a>
        <a href="?route=admin/movies/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm phim mới
        </a>
    </div>
</div>

<!-- Filters -->
<form method="GET" class="mb-3">
    <input type="hidden" name="route" value="admin/movies">
    <div class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm phim..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select" id="statusFilter" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="Chiếu online" <?php echo (isset($status) && $status === 'Chiếu online') ? 'selected' : ''; ?>>Phim online</option>
                <option value="Sắp chiếu" <?php echo (isset($status) && $status === 'Sắp chiếu') ? 'selected' : ''; ?>>Phim sắp chiếu</option>
                <option value="Chiếu rạp" <?php echo (isset($status) && $status === 'Chiếu rạp') ? 'selected' : ''; ?>>Phim chiếu rạp</option>
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
                    <th>Loại</th>
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
                        <td colspan="9" class="text-center text-muted">Không có phim nào</td>
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
                            <td>
                                <?php 
                                $movieType = $m['type'] ?? 'phimle';
                                if ($movieType === 'phimbo'): 
                                ?>
                                    <span class="badge bg-primary">Phim bộ</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Phim lẻ</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($m['category_name'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    $movieStatus = $m['status'] ?? 'Sắp chiếu';
                                    echo match($movieStatus) {
                                        'Chiếu online' => 'success',
                                        'Chiếu rạp' => 'info',
                                        'Sắp chiếu' => 'warning',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo htmlspecialchars($movieStatus); ?>
                                </span>
                                <?php if ($m['status_admin']): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($m['status_admin']); ?></small>
                                <?php endif; ?>
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

