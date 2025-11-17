<?php
$current_page = 'movie';
$title = 'Xem Phim';
?>

<section class="section">
    <div class="container">
        <div class="filter-bar">
            <form method="GET" class="search-form" action="?route=movie/index">
                <input type="hidden" name="route" value="movie/index">
                <div class="search-box-wrapper mb-3">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="movie-search" placeholder="Tìm kiếm phim theo tên, đạo diễn, diễn viên..." value="<?php echo htmlspecialchars($search ?? ''); ?>" autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                    </div>
                </div>
                
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
                        <div class="movie-info">
                            <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
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
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
        </div>
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
                // Preserve search value
                const searchInput = document.getElementById('movie-search');
                if (searchInput && searchInput.value) {
                    let hiddenInput = searchForm.querySelector('input[name="search"][type="hidden"]');
                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'search';
                        searchForm.appendChild(hiddenInput);
                    }
                    hiddenInput.value = searchInput.value;
                }
                searchForm.submit();
            }
            isFirstLoad = false;
        });
    });
});
</script>

