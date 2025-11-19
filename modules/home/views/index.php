<?php
$current_page = 'home';
$title = 'Trang chủ';
?>

<!-- Hero Section với Slider -->
<section class="hero-section-featured">
    <?php if (!empty($sliderMovies)): ?>
        <div class="hero-slider-container">
            <div class="hero-slider" id="heroSlider">
                <?php foreach ($sliderMovies as $index => $featuredMovie): ?>
                    <?php 
                    $year = $featuredMovie['created_at'] ? date('Y', strtotime($featuredMovie['created_at'])) : date('Y');
                    $duration = $featuredMovie['duration'] ? $featuredMovie['duration'] : 0;
                    $hours = floor($duration / 60);
                    $minutes = $duration % 60;
                    $durationText = $hours > 0 ? "{$hours}h " : '';
                    $durationText .= $minutes > 0 ? "{$minutes}m" : '';
                    if (!$durationText) $durationText = 'N/A';
                    if (($featuredMovie['type'] ?? 'phimle') === 'phimbo') {
                        $durationText = 'Phim bộ';
                    }
                    $imdbRating = number_format($featuredMovie['rating'] * 1.1, 1);
                    ?>
                    <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>">
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
                                            <span class="badge-type"><?php echo ($featuredMovie['type'] ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ'; ?></span>
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
                <?php endforeach; ?>
            </div>
            
            <!-- Slider Controls -->
            <button class="hero-slider-prev" onclick="changeSlide(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="hero-slider-next" onclick="changeSlide(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            <!-- Slider Dots -->
            <div class="hero-slider-dots">
                <?php foreach ($sliderMovies as $index => $movie): ?>
                    <span class="hero-slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $index; ?>)"></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="hero-placeholder-large">
            <img src="https://phongvu.vn/cong-nghe/wp-content/uploads/2025/08/hinh-nen-thanh-guom-diet-quy-2.jpg" alt="">
        </div>
    <?php endif; ?>
</section>

<!-- Phim Lẻ Section -->
<?php if (!empty($phimLe)): ?>
<section class="featured-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title-new">
                <i class="fas fa-film"></i> Phim Lẻ Nổi Bật
            </h2>
            <a href="?route=movie/index&type=phimle" class="section-view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="movies-row">
            <?php foreach (array_slice($phimLe, 0, 4) as $movie): ?>
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
                        <?php if (($movie['type'] ?? 'phimle') === 'phimbo'): ?>
                        <div class="movie-badge">
                            <?php echo number_format($movie['rating'] * 10); ?>
                        </div>
                        <?php endif; ?>
                        <div class="movie-overlay-new">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                </a>
                <div class="movie-info-new">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                        <a href="http://localhost/DuAn1/?route=movie/watch&id=<?php echo $movie['id']; ?>" style="flex: 1; text-decoration: none; color: inherit;">
                            <h3 class="movie-title-new"><?php echo htmlspecialchars($movie['title']); ?></h3>
                        </a>
                        <?php if (isset($user) && $user): ?>
                        <button class="favorite-btn-inline <?php echo (isset($favorites) && in_array($movie['id'], $favorites)) ? 'active' : ''; ?>" 
                                data-movie-id="<?php echo $movie['id']; ?>"
                                onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite(this, <?php echo $movie['id']; ?>);">
                            <i class="fas fa-heart"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <p class="movie-tags">
                        <span class="movie-type">_phim lẻ</span>
                        <?php if ($movie['category_name']): ?>
                            <span class="movie-tag">#<?php echo strtolower(str_replace(' ', '', htmlspecialchars($movie['category_name']))); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Phim Bộ Section -->
<?php if (!empty($phimBo)): ?>
<section class="latest-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title-new">
                <i class="fas fa-tv"></i> Phim Bộ Nổi Bật
            </h2>
            <a href="?route=movie/index&type=phimbo" class="section-view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="movies-grid-new">
            <?php foreach (array_slice($phimBo, 0, 8) as $movie): ?>
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
                </a>
                <div class="movie-info-new">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                        <a href="http://localhost/DuAn1/?route=movie/watch&id=<?php echo $movie['id']; ?>" style="flex: 1; text-decoration: none; color: inherit;">
                            <h3 class="movie-title-new"><?php echo htmlspecialchars($movie['title']); ?></h3>
                        </a>
                        <?php if (isset($user) && $user): ?>
                        <button class="favorite-btn-inline <?php echo (isset($favorites) && in_array($movie['id'], $favorites)) ? 'active' : ''; ?>" 
                                data-movie-id="<?php echo $movie['id']; ?>"
                                onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite(this, <?php echo $movie['id']; ?>);">
                            <i class="fas fa-heart"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <p class="movie-tags">
                        <span class="movie-type">_phim bộ</span>
                        <?php if ($movie['category_name']): ?>
                            <span class="movie-tag">#<?php echo strtolower(str_replace(' ', '', htmlspecialchars($movie['category_name']))); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Movies Section -->
<?php if (!empty($latestMovies)): ?>
<section class="featured-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title-new">
                <i class="fas fa-clock"></i> Phim Mới Nhất
            </h2>
            <a href="?route=movie/index" class="section-view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="movies-row">
            <?php foreach (array_slice($latestMovies, 0, 4) as $movie): ?>
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
                        <?php if (($movie['type'] ?? 'phimle') === 'phimbo'): ?>
                        <div class="movie-badge">
                            <?php echo number_format($movie['rating'] * 10); ?>
                        </div>
                        <?php endif; ?>
                        <div class="movie-overlay-new">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                </a>
                <div class="movie-info-new">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                        <a href="http://localhost/DuAn1/?route=movie/watch&id=<?php echo $movie['id']; ?>" style="flex: 1; text-decoration: none; color: inherit;">
                            <h3 class="movie-title-new"><?php echo htmlspecialchars($movie['title']); ?></h3>
                        </a>
                        <?php if (isset($user) && $user): ?>
                        <button class="favorite-btn-inline <?php echo (isset($favorites) && in_array($movie['id'], $favorites)) ? 'active' : ''; ?>" 
                                data-movie-id="<?php echo $movie['id']; ?>"
                                onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite(this, <?php echo $movie['id']; ?>);">
                            <i class="fas fa-heart"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <p class="movie-tags">
                        <span class="movie-type"><?php echo ($movie['type'] ?? 'phimle') === 'phimbo' ? '_phim bộ' : '_phim lẻ'; ?></span>
                        <?php if ($movie['category_name']): ?>
                            <span class="movie-tag">#<?php echo strtolower(str_replace(' ', '', htmlspecialchars($movie['category_name']))); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
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

// Hero Slider JavaScript
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots = document.querySelectorAll('.hero-slider-dot');
const totalSlides = slides.length;

function showSlide(index) {
    // Xử lý vòng lặp
    if (index >= totalSlides) {
        currentSlide = 0;
    } else if (index < 0) {
        currentSlide = totalSlides - 1;
    } else {
        currentSlide = index;
    }
    
    // Ẩn tất cả slides
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    // Hiển thị slide hiện tại
    if (slides[currentSlide]) {
        slides[currentSlide].classList.add('active');
    }
    if (dots[currentSlide]) {
        dots[currentSlide].classList.add('active');
    }
}

function changeSlide(direction) {
    showSlide(currentSlide + direction);
}

function goToSlide(index) {
    showSlide(index);
}

// Tự động chuyển slide mỗi 5 giây
let slideInterval = setInterval(() => {
    changeSlide(1);
}, 5000);

// Dừng auto slide khi hover
const sliderContainer = document.querySelector('.hero-slider-container');
if (sliderContainer) {
    sliderContainer.addEventListener('mouseenter', () => {
        clearInterval(slideInterval);
    });
    
    sliderContainer.addEventListener('mouseleave', () => {
        slideInterval = setInterval(() => {
            changeSlide(1);
        }, 5000);
    });
}

// Khởi tạo slide đầu tiên
document.addEventListener('DOMContentLoaded', function() {
    showSlide(0);
});
</script>
