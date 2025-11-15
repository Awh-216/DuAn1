<?php
$current_page = 'movie';
$title = htmlspecialchars($movie['title']);
?>

<section class="watch-section">
    <div class="container">
        <div class="watch-container">
            <div class="video-wrapper">
                <?php if ($movie['video_url']): ?>
                    <video id="videoPlayer" controls>
                        <source src="<?php echo htmlspecialchars($movie['video_url']); ?>" type="video/mp4">
                        Trình duyệt của bạn không hỗ trợ video.
                    </video>
                <?php elseif ($movie['trailer_url']): ?>
                    <video id="videoPlayer" controls>
                        <source src="<?php echo htmlspecialchars($movie['trailer_url']); ?>" type="video/mp4">
                        Trình duyệt của bạn không hỗ trợ video.
                    </video>
                <?php else: ?>
                    <div class="video-placeholder">
                        <i class="fas fa-video"></i>
                        <p>Video chưa có sẵn</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="movie-details">
                <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
                <div class="movie-meta-info">
                    <span><i class="fas fa-star"></i> <?php echo number_format($movie['rating'], 1); ?></span>
                    <span><i class="fas fa-clock"></i> <?php echo $movie['duration']; ?> phút</span>
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($movie['category_name'] ?? 'Chưa phân loại'); ?></span>
                    <span><i class="fas fa-layer-group"></i> <?php echo $movie['level']; ?></span>
                </div>
                
                <?php if ($movie['description']): ?>
                    <div class="movie-description-full">
                        <h3>Nội dung</h3>
                        <p><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($movie['director'] || $movie['actors']): ?>
                    <div class="movie-cast">
                        <?php if ($movie['director']): ?>
                            <p><strong>Đạo diễn:</strong> <?php echo htmlspecialchars($movie['director']); ?></p>
                        <?php endif; ?>
                        <?php if ($movie['actors']): ?>
                            <p><strong>Diễn viên:</strong> <?php echo htmlspecialchars($movie['actors']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="reviews-section" id="reviews">
                <h2><i class="fas fa-comments"></i> Đánh giá</h2>
                
                <?php if (isset($user) && $user): ?>
                    <form method="POST" action="?route=review/create" class="review-form" id="reviewForm">
                        <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                        <div class="rating-input">
                            <label>Đánh giá:</label>
                            <select name="rating" id="ratingSelect" required>
                                <option value="">Chọn điểm</option>
                                <option value="5">5 sao</option>
                                <option value="4">4 sao</option>
                                <option value="3">3 sao</option>
                                <option value="2">2 sao</option>
                                <option value="1">1 sao</option>
                            </select>
                        </div>
                        <textarea name="comment" id="commentText" placeholder="Viết đánh giá của bạn..." rows="3"></textarea>
                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </form>
                <?php endif; ?>
                
                <div class="reviews-list">
                    <?php if (empty($reviews)): ?>
                        <p class="no-reviews">Chưa có đánh giá nào.</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item <?php echo (isset($review['is_pinned']) && $review['is_pinned']) ? 'review-pinned' : ''; ?>">
                                <?php if (isset($review['is_pinned']) && $review['is_pinned']): ?>
                                    <span class="badge bg-warning mb-2">
                                        <i class="fas fa-thumbtack"></i> Đã ghim
                                    </span>
                                <?php endif; ?>
                                
                                <div class="review-header">
                                    <div class="d-flex justify-content-between align-items-start w-100">
                                        <div>
                                            <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                                            <span class="review-rating ms-2">
                                                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                                    <i class="fas fa-star text-warning"></i>
                                                <?php endfor; ?>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="review-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                                            
                                            <?php if (isset($isAdmin) && $isAdmin): ?>
                                                <div class="review-actions">
                                                    <a href="?route=review/pin&id=<?php echo $review['id']; ?>&movie_id=<?php echo $movie['id']; ?>&pin=<?php echo (isset($review['is_pinned']) && $review['is_pinned']) ? 0 : 1; ?>" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="<?php echo (isset($review['is_pinned']) && $review['is_pinned']) ? 'Bỏ ghim' : 'Ghim'; ?>"
                                                       onclick="return confirm('<?php echo (isset($review['is_pinned']) && $review['is_pinned']) ? 'Bỏ ghim' : 'Ghim'; ?> bình luận này?')">
                                                        <i class="fas fa-thumbtack"></i>
                                                    </a>
                                                    <a href="?route=review/delete&id=<?php echo $review['id']; ?>&movie_id=<?php echo $movie['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       title="Xóa"
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($review['comment']): ?>
                                    <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Scroll đến phần reviews nếu có hash trong URL
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#reviews') {
        setTimeout(function() {
            const reviewsSection = document.getElementById('reviews');
            if (reviewsSection) {
                reviewsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }
    
    // Scroll mượt đến reviews sau khi submit (fallback)
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function() {
            // Lưu vị trí scroll hiện tại vào sessionStorage
            sessionStorage.setItem('scrollToReviews', 'true');
        });
    }
    
    // Kiểm tra nếu cần scroll sau khi reload
    if (sessionStorage.getItem('scrollToReviews') === 'true') {
        sessionStorage.removeItem('scrollToReviews');
        setTimeout(function() {
            const reviewsSection = document.getElementById('reviews');
            if (reviewsSection) {
                reviewsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 300);
    }
});
</script>

