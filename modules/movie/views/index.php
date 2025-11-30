<?php
$current_page = 'movie';
$title = 'Xem Phim';
?>

<section class="section">
    <br>
    <div class="container">
        <div class="filter-bar">
            <form method="GET" class="search-form" action="?route=movie/index">
                <input type="hidden" name="route" value="movie/index">
                <?php if (isset($search) && $search): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
                
                <div class="filter-options">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">Thể loại</label>
                            <select name="category" class="form-select form-select-sm">
                                <option value="">Tất cả thể loại</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($category_id) && $category_id == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small">Trạng thái</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Chiếu online" <?php echo (isset($status) && $status === 'Chiếu online') ? 'selected' : ''; ?>>Chiếu online</option>
                                <option value="Sắp chiếu" <?php echo (isset($status) && $status === 'Sắp chiếu') ? 'selected' : ''; ?>>Sắp chiếu</option>
                                <option value="Chiếu rạp" <?php echo (isset($status) && $status === 'Chiếu rạp') ? 'selected' : ''; ?>>Chiếu rạp</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small">Loại phim</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">Tất cả</option>
                                <option value="phimle" <?php echo (isset($type) && $type === 'phimle') ? 'selected' : ''; ?>>Phim lẻ</option>
                                <option value="phimbo" <?php echo (isset($type) && $type === 'phimbo') ? 'selected' : ''; ?>>Phim bộ</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small">Quốc gia</label>
                            <select name="country" class="form-select form-select-sm">
                                <option value="">Tất cả quốc gia</option>
                                <?php if (isset($countries)): ?>
                                    <?php foreach ($countries as $c): ?>
                                        <option value="<?php echo htmlspecialchars($c['country']); ?>" <?php echo (isset($country) && $country === $c['country']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($c['country']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small">Đánh giá tối thiểu</label>
                            <select name="min_rating" class="form-select form-select-sm">
                                <option value="">Tất cả</option>
                                <option value="9" <?php echo (isset($min_rating) && $min_rating == 9) ? 'selected' : ''; ?>>9.0+ ⭐</option>
                                <option value="8" <?php echo (isset($min_rating) && $min_rating == 8) ? 'selected' : ''; ?>>8.0+ ⭐</option>
                                <option value="7" <?php echo (isset($min_rating) && $min_rating == 7) ? 'selected' : ''; ?>>7.0+ ⭐</option>
                                <option value="6" <?php echo (isset($min_rating) && $min_rating == 6) ? 'selected' : ''; ?>>6.0+ ⭐</option>
                                <option value="5" <?php echo (isset($min_rating) && $min_rating == 5) ? 'selected' : ''; ?>>5.0+ ⭐</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-filter"></i> Áp dụng bộ lọc
                        </button>
                        <a href="?route=movie/index" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-redo"></i> Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>
            
            <div class="category-filter mt-3">
                <div class="category-tags">
                    <a href="?route=movie/index" class="category-tag <?php echo !isset($category_id) || !$category_id ? 'active' : ''; ?>">
                        <i class="fas fa-th"></i> Tất cả
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="?route=movie/index&category=<?php echo $cat['id']; ?>" 
                           class="category-tag <?php echo (isset($category_id) && $category_id == $cat['id']) ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">
            <i class="fas fa-video"></i> 
            <?php 
            if ($search) {
                echo 'Kết quả tìm kiếm: "' . htmlspecialchars($search) . '"';
                if (!empty($movies)) {
                    echo ' <span class="badge bg-primary">' . count($movies) . ' phim</span>';
                }
            } elseif (isset($category_id) && $category_id) {
                $cat = array_filter($categories, fn($c) => $c['id'] == $category_id);
                echo 'Phim ' . htmlspecialchars(reset($cat)['name'] ?? '');
                if (!empty($movies)) {
                    echo ' <span class="badge bg-primary">' . count($movies) . ' phim</span>';
                }
            } else {
                echo 'Tất cả phim';
                if (!empty($movies)) {
                    echo ' <span class="badge bg-primary">' . count($movies) . ' phim</span>';
                }
            }
            ?>
        </h2>
        
        <?php if ($search && empty($movies)): ?>
            <div class="empty-state text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>Không tìm thấy phim nào</h4>
                <p class="text-muted">Không có kết quả phù hợp với từ khóa: "<strong><?php echo htmlspecialchars($search); ?></strong>"</p>
                <p class="text-muted">Thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc</p>
                <a href="?route=movie/index" class="btn btn-primary mt-3">
                    <i class="fas fa-redo"></i> Xem tất cả phim
                </a>
            </div>
        <?php elseif (empty($movies)): ?>
            <div class="empty-state text-center py-5">
                <i class="fas fa-film fa-3x text-muted mb-3"></i>
                <h4>Chưa có phim nào</h4>
                <p class="text-muted">Hiện tại chưa có phim phù hợp với bộ lọc của bạn</p>
            </div>
        <?php else: ?>
        <div class="movie-grid">
                <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <a href="http://localhost/DuAn1/?route=movie/watch&id=<?php echo $movie['id']; ?>">
                        <div class="movie-thumbnail">
                            <?php if ($movie['thumbnail']): ?>
                                <img src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                            <?php else: ?>
                                <div class="movie-placeholder">
                                    <i class="fas fa-film"></i>
                                </div>
                            <?php endif; ?>
                            <div class="movie-overlay">
                                <i class="fas fa-play"></i>
                            </div>
                            <?php if (($movie['type'] ?? 'phimle') === 'phimbo'): ?>
                            <div class="movie-badge">
                                <?php echo number_format($movie['rating'] * 10); ?>
                            </div>
                            <?php else: ?>
                            <span class="movie-level"><?php echo $movie['level']; ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="movie-info">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                            <a href="http://localhost/DuAn1/?route=movie/watch&id=<?php echo $movie['id']; ?>" style="flex: 1; text-decoration: none; color: inherit;">
                                <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                            </a>
                            <?php if (isset($user) && $user): ?>
                            <button class="favorite-btn-inline <?php echo (isset($favorites) && in_array($movie['id'], $favorites)) ? 'active' : ''; ?>" 
                                    data-movie-id="<?php echo $movie['id']; ?>"
                                    onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite(this, <?php echo $movie['id']; ?>);">
                                <i class="fas fa-heart"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                            <p class="movie-meta">
                                <span><i class="fas fa-star"></i> <?php echo number_format($movie['rating'], 1); ?></span>
                                <?php if ($movie['type'] === 'phimbo'): ?>
                                    <span><i class="fas fa-tv"></i> Phim bộ</span>
                                <?php else: ?>
                                    <span><i class="fas fa-clock"></i> <?php echo $movie['duration']; ?> phút</span>
                                <?php endif; ?>
                            </p>
                            <p class="movie-category">
                                <span class="movie-type-badge"><?php echo ($movie['type'] ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ'; ?></span>
                                <?php if ($movie['category_name']): ?>
                                    <span> • <?php echo htmlspecialchars($movie['category_name'] ?? 'Chưa phân loại'); ?></span>
                                <?php endif; ?>
                            </p>
                            <?php if ($movie['description']): ?>
                                <p class="movie-description"><?php echo htmlspecialchars(mb_substr($movie['description'], 0, 100)) . '...'; ?></p>
                            <?php endif; ?>
                            <?php if (isset($movie['status']) && $movie['status'] === 'Chiếu rạp'): ?>
                                <div class="mt-2">
                                    <a href="?route=booking/index&movie=<?php echo $movie['id']; ?>" 
                                       class="btn btn-primary btn-sm w-100" 
                                       style="background: #e50914; border: none; padding: 8px 16px; border-radius: 6px; text-decoration: none; display: inline-block; text-align: center; color: white; font-weight: 500;">
                                        <i class="fas fa-ticket-alt"></i> Đặt vé xem phim
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                </div>
                <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <nav aria-label="Phân trang danh sách phim" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php
                    // Tạo URL với các tham số hiện tại
                    $queryParams = $_GET;
                    unset($queryParams['page']);
                    $baseUrl = '?route=movie/index';
                    if (!empty($queryParams)) {
                        $baseUrl .= '&' . http_build_query($queryParams);
                    }
                    ?>
                    
                    <!-- Previous Button -->
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo $page > 1 ? $baseUrl . '&page=' . ($page - 1) : '#'; ?>" aria-label="Trang trước">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                    </li>
                    
                    <?php
                    // Hiển thị các trang
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    // Trang đầu
                    if ($startPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $baseUrl . '&page=1'; ?>">1</a>
                        </li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo $baseUrl . '&page=' . $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php
                    // Trang cuối
                    if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $baseUrl . '&page=' . $totalPages; ?>">
                                <?php echo $totalPages; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Next Button -->
                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo $page < $totalPages ? $baseUrl . '&page=' . ($page + 1) : '#'; ?>" aria-label="Trang sau">
                            Sau <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
                
                <!-- Pagination Info -->
                <div class="text-center mt-3 text-muted">
                    <small>
                        Hiển thị <?php echo $offset + 1; ?> - <?php echo min($offset + $perPage, $total); ?> 
                        trong tổng số <?php echo number_format($total); ?> phim
                        (Trang <?php echo $page; ?>/<?php echo $totalPages; ?>)
                    </small>
                </div>
            </nav>
        <?php endif; ?>
        
<script>
function toggleFavorite(btn, movieId) {
    <?php if (!isset($user) || !$user): ?>
    // Nếu chưa đăng nhập, yêu cầu đăng nhập
    if (confirm('Vui lòng đăng nhập để thêm vào yêu thích!')) {
        window.location.href = '?route=auth/login';
    }
    return;
    <?php endif; ?>
    
    // Disable button while processing
    btn.disabled = true;
    const icon = btn.querySelector('i');
    
    fetch('?route=movie/toggleFavorite', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'movie_id=' + movieId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Toggle active class
            btn.classList.toggle('active', data.favorite);
            
            // Show message
            if (data.message) {
                // Có thể thêm toast notification ở đây
                console.log(data.message);
            }
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
        btn.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thêm vào yêu thích');
        btn.disabled = false;
    });
}
</script>
        <?php endif; ?>
    </div>
</section>

<script>
// Auto submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('.filter-options select');
    const searchForm = document.querySelector('.search-form');
    
    // Auto submit when filter changes (but not on page load)
    filterSelects.forEach(select => {
        let isFirstLoad = true;
        select.addEventListener('change', function() {
            if (!isFirstLoad && searchForm) {
                // Search value đã được giữ trong hidden input nếu có
                searchForm.submit();
            }
            isFirstLoad = false;
        });
    });
});
</script>

