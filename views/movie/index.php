<?php
$current_page = 'movie';
$title = 'Xem Phim';
?>

<section class="section">
    <div class="container">
        <div class="filter-bar">
            <form method="GET" class="search-form">
                <input type="hidden" name="route" value="movie/index">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Tìm kiếm phim..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    <button type="submit" class="btn btn-primary">Tìm</button>
                </div>
            </form>
            
            <div class="category-filter">
                <a href="http://localhost/DuAn1/?route=movie/index" class="category-tag <?php echo !isset($category_id) || !$category_id ? 'active' : ''; ?>">Tất cả</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="http://localhost/DuAn1/?route=movie/index&category=<?php echo $cat['id']; ?>" 
                       class="category-tag <?php echo (isset($category_id) && $category_id == $cat['id']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">
            <i class="fas fa-video"></i> 
            <?php 
            if (isset($category_id) && $category_id) {
                $cat = array_filter($categories, fn($c) => $c['id'] == $category_id);
                echo 'Phim ' . htmlspecialchars(reset($cat)['name'] ?? '');
            } else {
                echo 'Tất cả phim';
            }
            ?>
        </h2>
        
        <div class="movie-grid">
            <?php if (empty($movies)): ?>
                <div class="empty-state">
                    <i class="fas fa-film"></i>
                    <p>Không tìm thấy phim nào.</p>
                </div>
            <?php else: ?>
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
                            <span class="movie-level"><?php echo $movie['level']; ?></span>
                        </div>
                        <div class="movie-info">
                            <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <p class="movie-meta">
                                <span><i class="fas fa-star"></i> <?php echo number_format($movie['rating'], 1); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo $movie['duration']; ?> phút</span>
                            </p>
                            <p class="movie-category"><?php echo htmlspecialchars($movie['category_name'] ?? 'Chưa phân loại'); ?></p>
                            <?php if ($movie['description']): ?>
                                <p class="movie-description"><?php echo htmlspecialchars(mb_substr($movie['description'], 0, 100)) . '...'; ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

