<?php
$current_page = 'home';
$title = 'Trang chủ';
?>

<!-- Hero Section -->
<section class="hero-section-featured">
    <?php if (!empty($hotMovies)): ?>
        <?php 
        $featuredMovie = $hotMovies[0]; 
        $year = $featuredMovie['created_at'] ? date('Y', strtotime($featuredMovie['created_at'])) : date('Y');
        $duration = $featuredMovie['duration'] ? $featuredMovie['duration'] : 0;
        $hours = floor($duration / 60);
        $minutes = $duration % 60;
        $durationText = $hours > 0 ? "{$hours}h " : '';
        $durationText .= $minutes > 0 ? "{$minutes}m" : '';
        if (!$durationText) $durationText = 'N/A';
        $imdbRating = number_format($featuredMovie['rating'] * 1.1, 1); // Giả lập IMDb rating
        ?>
        <div class="hero-featured-container">
            <!-- Background Image -->
            <?php if ($featuredMovie['thumbnail']): ?>
                <div class="hero-featured-bg" style="background-image: url('<?php echo htmlspecialchars($featuredMovie['thumbnail']); ?>');"></div>
            <?php endif; ?>
            
            <!-- Content Overlay -->
            <div class="hero-featured-content">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <!-- Title Handwritten Style -->
                            <h1 class="hero-title-handwritten"><?php echo htmlspecialchars($featuredMovie['title']); ?></h1>
                            
                            <!-- Main Title -->
                            <h2 class="hero-title-main"><?php echo htmlspecialchars($featuredMovie['title']); ?></h2>
                            
                            <!-- Info Badges -->
                            <div class="hero-info-badges">
                                <span class="badge-imdb">IMDb <?php echo $imdbRating; ?></span>
                                <?php if (in_array($featuredMovie['level'], ['Gold', 'Premium'])): ?>
                                    <span class="badge-quality">4K</span>
                                <?php endif; ?>
                                <span class="badge-age">T18</span>
                                <span class="badge-year"><?php echo $year; ?></span>
                                <span class="badge-duration"><?php echo $durationText; ?></span>
                            </div>
                            
                            <!-- Categories -->
                            <?php if ($featuredMovie['category_name']): ?>
                                <div class="hero-categories">
                                    <span class="category-tag"><?php echo htmlspecialchars($featuredMovie['category_name']); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Description -->
                            <?php if ($featuredMovie['description']): ?>
                                <p class="hero-description">
                                    <?php 
                                    $desc = htmlspecialchars($featuredMovie['description']);
                                    echo strlen($desc) > 200 ? substr($desc, 0, 200) . '...' : $desc;
                                    ?>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Action Buttons -->
                            <div class="hero-actions">
                                <a href="http://localhost/DuAn1/?route=movie/watch&id=<?php echo $featuredMovie['id']; ?>" class="btn-play-large">
                                    <i class="fas fa-play"></i>
                                </a>
                                <button class="btn-action-icon" title="Yêu thích">
                                    <i class="fas fa-heart"></i>
                                </button>
                                <button class="btn-action-icon" title="Thông tin">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="hero-placeholder-large">
            <i class="fas fa-film"></i>
            <p>Chưa có phim nổi bật</p>
        </div>
    <?php endif; ?>
</section>

<!-- Featured Movies Section -->
<section class="featured-section">
    <div class="container">
        <h2 class="section-title-new">Danh sách phim nổi bật</h2>
        
        <div class="movies-row">
            <?php if (empty($hotMovies)): ?>
                <div class="empty-state">
                    <i class="fas fa-film"></i>
                    <p>Chưa có phim nào. Vui lòng thêm phim vào database.</p>
                </div>
            <?php else: ?>
                <?php 
                $displayMovies = array_slice($hotMovies, 0, 4);
                foreach ($displayMovies as $index => $movie): 
                ?>
                <div class="movie-card-new">
                    <a href="http://localhost/DuAn1/?route=movie/watch&id=<?php echo $movie['id']; ?>">
                        <div class="movie-thumbnail-new">
                            <?php if ($movie['thumbnail']): ?>
                                <img src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                            <?php else: ?>
                                <div class="movie-placeholder-new">
                                    <i class="fas fa-film"></i>
                                </div>
                            <?php endif; ?>
                            <div class="movie-badge">
                                <?php echo number_format($movie['rating'] * 10); ?>
                            </div>
                            <div class="movie-overlay-new">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <div class="movie-info-new">
                            <h3 class="movie-title-new"><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <p class="movie-tags">
                                <span class="movie-type">_phim bộ</span>
                                <?php if ($movie['category_name']): ?>
                                    <span class="movie-tag">#<?php echo strtolower(str_replace(' ', '', htmlspecialchars($movie['category_name']))); ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Latest Movies Section -->
<?php if (!empty($latestMovies)): ?>
<section class="latest-section">
    <div class="container">
        <h2 class="section-title-new">Phim mới nhất</h2>
        
        <div class="movies-grid-new">
            <?php foreach (array_slice($latestMovies, 0, 8) as $movie): ?>
            <div class="movie-card-new">
                <a href="http://localhost/DuAn1/?route=movie/watch&id=<?php echo $movie['id']; ?>">
                    <div class="movie-thumbnail-new">
                        <?php if ($movie['thumbnail']): ?>
                            <img src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <?php else: ?>
                            <div class="movie-placeholder-new">
                                <i class="fas fa-film"></i>
                            </div>
                        <?php endif; ?>
                        <div class="movie-badge">
                            <?php echo number_format($movie['rating'] * 10); ?>
                        </div>
                        <div class="movie-overlay-new">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="movie-info-new">
                        <h3 class="movie-title-new"><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <p class="movie-tags">
                            <span class="movie-type">_phim bộ</span>
                            <?php if ($movie['category_name']): ?>
                                <span class="movie-tag">#<?php echo strtolower(str_replace(' ', '', htmlspecialchars($movie['category_name']))); ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
