<?php
$current_page = 'movie';
$title = htmlspecialchars($movie['title']);
?>

<section class="watch-section">
    <div class="container">
        <div class="watch-container">
            <!-- Header với nút quay lại và tên phim -->
            <div class="watch-header">
                <a href="javascript:history.back()" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="watch-movie-title"><?php echo htmlspecialchars($movie['title']); ?></h1>
            </div>
            
            <div class="video-wrapper">
                <?php 
                // Xác định video URL để hiển thị
                $videoUrl = null;
                $noVideoMessage = null;
                $episodeNumber = null;
                $isPhimBo = ($movie['type'] ?? 'phimle') === 'phimbo';
                
                if ($isPhimBo) {
                    // Xử lý phim bộ
                    $folderPath = null;
                    
                    // Debug: Kiểm tra episodes từ database
                    error_log("Watch view - Episodes count from DB: " . (isset($episodes) ? count($episodes) : 0));
                    error_log("Watch view - Current episode: " . (isset($currentEpisode) && $currentEpisode ? "Yes (ID: " . $currentEpisode['id'] . ", Number: " . $currentEpisode['episode_number'] . ")" : "No"));
                    error_log("Watch view - Movie video_url: " . ($movie['video_url'] ?? 'N/A'));
                    
                    if (isset($currentEpisode) && $currentEpisode) {
                        // Có tập được chọn
                        $episodeNumber = $currentEpisode['episode_number'];
                        
                        if (!empty($currentEpisode['video_url'])) {
                            $videoPath = $currentEpisode['video_url'];
                            // Extract folder path từ video_url
                            if (preg_match('/\/tap\d+\.mp4$/i', $videoPath)) {
                                // Nếu video_url chứa tên file (tap1.mp4, tap2.mp4, etc), extract folder
                                $folderPath = dirname($videoPath);
                            } else {
                                // Nếu đã là folder path, dùng trực tiếp
                                $folderPath = rtrim($videoPath, '/');
                            }
                            // Set videoUrl ngay sau khi có folderPath
                            if ($folderPath) {
                                $videoUrl = $folderPath;
                            }
                        } else {
                            // Tập được chọn nhưng chưa có video_url, lấy từ tập khác hoặc phim
                            if (!empty($episodes)) {
                                foreach ($episodes as $ep) {
                                    if (!empty($ep['video_url'])) {
                                        $videoPath = $ep['video_url'];
                                        if (preg_match('/\/tap\d+\.mp4$/i', $videoPath)) {
                                            $folderPath = dirname($videoPath);
                                        } else {
                                            $folderPath = rtrim($videoPath, '/');
                                        }
                                        break;
                                    }
                                }
                            }
                            // Nếu vẫn không có, thử lấy từ movie video_url
                            if (!$folderPath && !empty($movie['video_url'])) {
                                $folderPath = rtrim($movie['video_url'], '/');
                            }
                            
                            if ($folderPath) {
                                $videoUrl = $folderPath;
                            } else {
                                $noVideoMessage = "Tập " . $currentEpisode['episode_number'] . " chưa có video. Vui lòng chọn tập khác hoặc đợi admin upload video.";
                            }
                        }
                    } elseif (!empty($episodes)) {
                        // Chưa chọn tập, tìm tập đầu tiên có video_url
                        $found = false;
                        foreach ($episodes as $ep) {
                            if (!empty($ep['video_url'])) {
                                $episodeNumber = $ep['episode_number'];
                                $videoPath = $ep['video_url'];
                                // Extract folder path từ video_url
                                if (preg_match('/\/tap\d+\.mp4$/i', $videoPath)) {
                                    $folderPath = dirname($videoPath);
                                } else {
                                    $folderPath = rtrim($videoPath, '/');
                                }
                                // Set videoUrl ngay sau khi có folderPath
                                if ($folderPath) {
                                    $videoUrl = $folderPath;
                                }
                                $found = true;
                                break;
                            }
                        }
                        
                        // Nếu không tìm thấy tập có video_url, mặc định dùng tập 1
                        if (!$found) {
                            $episodeNumber = 1;
                            // Lấy folder path từ bất kỳ episode nào hoặc từ phim
                            if (!empty($episodes[0]['video_url'])) {
                                $videoPath = $episodes[0]['video_url'];
                                if (preg_match('/\/tap\d+\.mp4$/i', $videoPath)) {
                                    $folderPath = dirname($videoPath);
                                } else {
                                    $folderPath = rtrim($videoPath, '/');
                                }
                            } elseif (!empty($movie['video_url'])) {
                                $folderPath = rtrim($movie['video_url'], '/');
                            }
                            // Set videoUrl nếu có folderPath
                            if ($folderPath) {
                                $videoUrl = $folderPath;
                            }
                        }
                    } else {
                        // Không có episodes trong database, mặc định dùng tập 1
                        $episodeNumber = 1;
                        if (!empty($movie['video_url'])) {
                            $folderPath = rtrim($movie['video_url'], '/');
                            $videoUrl = $folderPath;
                            error_log("Watch view - Using default episode 1 with folder: " . $folderPath);
                        } else {
                            $noVideoMessage = "Chưa có tập nào có video. Vui lòng đợi admin upload video.";
                        }
                    }
                    
                    // Nếu vẫn không có videoUrl và không có thông báo lỗi, mặc định dùng tập 1
                    if (!$videoUrl && !$noVideoMessage) {
                        $episodeNumber = 1;
                        if (!empty($movie['video_url'])) {
                            $videoUrl = rtrim($movie['video_url'], '/');
                        } else {
                            $noVideoMessage = "Chưa có tập nào có video. Vui lòng đợi admin upload video.";
                        }
                    }
                } else {
                    // Phim lẻ
                    $videoUrl = $movie['video_url'] ?? null;
                    if (!$videoUrl) {
                        $noVideoMessage = "Video chưa có sẵn.";
                    }
                }
                
                if ($videoUrl): ?>
                    <video id="videoPlayer" controls>
                        <?php if ($isPhimBo && $episodeNumber !== null): ?>
                            <!-- Phim bộ: thêm số tập động vào URL -->
                            <source src="<?php echo htmlspecialchars($videoUrl); ?>/tap<?php echo $episodeNumber; ?>.mp4" type="video/mp4">
                        <?php else: ?>
                            <!-- Phim lẻ: dùng URL trực tiếp -->
                            <source src="<?php echo htmlspecialchars($videoUrl); ?>" type="video/mp4">
                        <?php endif; ?>
                        Trình duyệt của bạn không hỗ trợ video.
                    </video>
                <?php elseif ($noVideoMessage): ?>
                    <div class="video-placeholder">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ff9800;"></i>
                        <p style="margin-top: 1rem; font-size: 1.1rem; color: var(--text-primary);"><?php echo htmlspecialchars($noVideoMessage); ?></p>
                        <?php if (($movie['type'] ?? 'phimle') === 'phimbo' && !empty($episodes)): ?>
                            <p style="margin-top: 0.5rem; color: var(--text-secondary); font-size: 0.9rem;">
                                <i class="fas fa-info-circle"></i> Vui lòng chọn tập khác từ danh sách bên dưới.
                            </p>
                        <?php endif; ?>
                    </div>
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
            
            <?php 
            // Luôn hiển thị phần episodes nếu phim có type là 'phimbo'
            $isPhimBo = ($movie['type'] ?? 'phimle') === 'phimbo';
            
            // Debug: Kiểm tra episodes
            error_log("Watch page - Movie ID: " . ($movie['id'] ?? 'N/A') . ", Type: " . ($movie['type'] ?? 'N/A') . ", Is Phim Bo: " . ($isPhimBo ? 'Yes' : 'No'));
            error_log("Watch page - Episodes count: " . (isset($episodes) ? count($episodes) : 0));
            if (isset($episodes) && !empty($episodes)) {
                error_log("Watch page - First episode: " . print_r($episodes[0], true));
            }
            
            if ($isPhimBo): 
            ?>
            <div class="episodes-section">
                <h3><i class="fas fa-list"></i> Danh sách tập 
                    <?php if (isset($episodes) && !empty($episodes)): ?>
                        <span class="badge bg-primary ms-2"><?php echo count($episodes); ?> tập</span>
                    <?php else: ?>
                        <span class="badge bg-warning ms-2">Chưa có tập</span>
                    <?php endif; ?>
                </h3>
                
                <?php if (isset($episodes) && !empty($episodes)): ?>
                    <div class="episodes-list">
                        <?php foreach ($episodes as $episode): ?>
                            <a href="?route=movie/watch&id=<?php echo $movie['id']; ?>&episode_id=<?php echo $episode['id']; ?>" 
                               class="episode-item <?php echo (isset($currentEpisode) && $currentEpisode && $currentEpisode['id'] == $episode['id']) ? 'active' : ''; ?> <?php echo empty($episode['video_url']) ? 'episode-no-video' : ''; ?>"
                               title="<?php echo htmlspecialchars($episode['title'] ?? 'Tập ' . $episode['episode_number']); ?>">
                                <div class="episode-number"><?php echo $episode['episode_number']; ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Chưa có tập nào được thêm vào phim này.</strong>
                        <p class="mb-0 mt-2">Để hiển thị danh sách tập, vui lòng thêm các tập cho phim bộ này trong phần quản trị.</p>
                        <?php if (isset($isAdmin) && $isAdmin): ?>
                            <a href="?route=admin/movies/edit&id=<?php echo $movie['id']; ?>" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> Thêm tập ngay
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="movie-details">
                <div class="movie-meta-info">
                    <span><i class="fas fa-star"></i> <?php echo number_format($movie['rating'], 1); ?></span>
                    <?php if (($movie['type'] ?? 'phimle') === 'phimbo'): ?>
                        <span><i class="fas fa-tv"></i> Phim bộ</span>
                    <?php else: ?>
                        <span><i class="fas fa-clock"></i> <?php echo $movie['duration']; ?> phút</span>
                    <?php endif; ?>
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($movie['category_name'] ?? 'Chưa phân loại'); ?></span>
                    <span class="movie-type-badge-inline"><?php echo ($movie['type'] ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ'; ?></span>
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

