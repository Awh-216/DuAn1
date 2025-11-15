<?php
$current_page = 'booking';
$title = 'Đặt Vé Xem Phim';
$meta_description = $movie ? 'Đặt vé xem phim ' . htmlspecialchars($movie['title']) . ' tại CineHub. Chọn rạp, ngày, giờ và ghế ngồi phù hợp cho bạn.' : 'Đặt vé xem phim tại CineHub. Xem phim tại rạp với giá cả hợp lý và dịch vụ chất lượng.';
$meta_keywords = 'đặt vé xem phim, vé xem phim online, mua vé xem phim, CineHub' . ($movie ? ', ' . htmlspecialchars($movie['title']) : '');
$meta_og_title = $title . ' - CineHub';
$meta_og_description = $meta_description;
$meta_og_image = ($movie && $movie['thumbnail']) ? $movie['thumbnail'] : null;
?>

<section class="booking-page-section">
    <div class="container-fluid px-4">
        <div class="row g-4">
            <!-- Left Column: Movie Info -->
            <div class="col-lg-5">
                <?php if ($movie): ?>
                    <article class="booking-movie-info" itemscope itemtype="https://schema.org/Movie">
                        <!-- Movie Poster -->
                        <div class="movie-poster-large mb-4">
                            <?php if ($movie['thumbnail']): ?>
                                <img src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" 
                                     alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                                     class="img-fluid rounded"
                                     itemprop="image">
                            <?php else: ?>
                                <div class="poster-placeholder">
                                    <i class="fas fa-film fa-5x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Movie Title -->
                        <h1 class="movie-title-booking mb-3" itemprop="name"><?php echo htmlspecialchars($movie['title']); ?></h1>
                        
                        <!-- IMDb Rating -->
                        <div class="movie-rating mb-3" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                            <i class="fas fa-star text-warning"></i>
                            <span class="imdb-rating">
                                IMDb <span itemprop="ratingValue"><?php echo number_format($movie['rating'] * 1.1, 1); ?></span>
                            </span>
                        </div>
                        
                        <!-- Categories -->
                        <?php if ($movie['category_name']): ?>
                            <div class="movie-categories mb-3">
                                <span class="category-badge" itemprop="genre"><?php echo htmlspecialchars($movie['category_name']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Description -->
                        <?php if ($movie['description']): ?>
                            <p class="movie-description mb-4" itemprop="description">
                                <?php echo htmlspecialchars($movie['description']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <!-- Trailer Button -->
                        <?php if ($movie['trailer_url']): ?>
                            <a href="<?php echo htmlspecialchars($movie['trailer_url']); ?>" 
                               target="_blank" 
                               class="btn-trailer"
                               rel="noopener noreferrer"
                               aria-label="Xem trailer phim <?php echo htmlspecialchars($movie['title']); ?>">
                                <i class="fas fa-play me-2"></i> Xem Trailer
                            </a>
                        <?php endif; ?>
                    </article>
                <?php else: ?>
                    <div class="booking-movie-info booking-movie-empty">
                        <div class="empty-movie-state">
                            <div class="empty-icon-wrapper">
                                <i class="fas fa-film"></i>
                            </div>
                            <h3 class="empty-title">Vui lòng chọn phim để đặt vé</h3>
                            <p class="empty-description">
                                Chọn một bộ phim từ danh sách bên phải để xem thông tin chi tiết và đặt vé xem phim tại rạp.
                            </p>
                            <div class="empty-features">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Chọn rạp và suất chiếu</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Chọn ghế ngồi ưa thích</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Thanh toán nhanh chóng</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Right Column: Booking Form -->
            <div class="col-lg-7">
                <div class="booking-form-container">
                    <header class="booking-header">
                        <h2 class="booking-form-title">Đặt vé xem phim</h2>
                        <p class="booking-subtitle">Chọn phim, rạp, ngày giờ và ghế ngồi của bạn</p>
                    </header>
                    
                    <!-- Movie Selection -->
                    <?php if (empty($selected_movie)): ?>
                        <div class="booking-step mb-4">
                            <label class="booking-label" for="movie-select">
                                <i class="fas fa-film me-2"></i>Chọn phim
                            </label>
                            <?php if (empty($movies)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có phim nào đang chiếu rạp. Vui lòng quay lại sau!
                                </div>
                            <?php else: ?>
                                <form method="GET" class="movie-select-form" aria-label="Chọn phim để đặt vé">
                                    <input type="hidden" name="route" value="booking/index">
                                    <select name="movie" 
                                            id="movie-select"
                                            class="form-select-booking" 
                                            onchange="this.form.submit()" 
                                            required
                                            aria-label="Chọn phim từ danh sách">
                                        <option value="">-- Chọn phim --</option>
                                        <?php foreach ($movies as $m): ?>
                                            <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['title']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Theater Selection -->
                        <div class="booking-step mb-4">
                            <label class="booking-label">
                                <i class="fas fa-building me-2"></i>Chọn rạp
                            </label>
                            <?php if (empty($theaters)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có rạp nào. Vui lòng liên hệ quản trị viên!
                                </div>
                            <?php else: ?>
                                <div class="theaters-list" role="group" aria-label="Danh sách rạp chiếu phim">
                                    <?php foreach ($theaters as $theater): ?>
                                        <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $theater['id']; ?>&date=<?php echo $selected_date; ?>&time=<?php echo $selected_time; ?>&showtime_id=<?php echo $selected_showtime_id; ?>" 
                                           class="theater-btn <?php echo $selected_theater == $theater['id'] ? 'active' : ''; ?>"
                                           aria-pressed="<?php echo $selected_theater == $theater['id'] ? 'true' : 'false'; ?>">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <?php echo htmlspecialchars($theater['name']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Date Selection -->
                        <div class="booking-step mb-4">
                            <label class="booking-label">
                                <i class="fas fa-calendar-alt me-2"></i>Chọn ngày
                            </label>
                            <div class="dates-scroll" role="group" aria-label="Chọn ngày chiếu">
                                <?php foreach ($dates as $dateItem): ?>
                                    <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $selected_theater; ?>&date=<?php echo $dateItem['value']; ?>&time=<?php echo $selected_time; ?>&showtime_id=<?php echo $selected_showtime_id; ?>" 
                                       class="date-btn <?php echo $selected_date == $dateItem['value'] ? 'active' : ''; ?>"
                                       aria-pressed="<?php echo $selected_date == $dateItem['value'] ? 'true' : 'false'; ?>"
                                       aria-label="Chọn ngày <?php echo $dateItem['label']; ?>">
                                        <span class="date-day"><?php echo $dateItem['day_name']; ?></span>
                                        <span class="date-number"><?php echo $dateItem['label']; ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Time Selection -->
                        <?php if ($selected_date && $selected_theater): ?>
                            <div class="booking-step mb-4">
                                <label class="booking-label">
                                    <i class="fas fa-clock me-2"></i>Chọn giờ chiếu
                                </label>
                                <div class="times-grid" role="group" aria-label="Chọn giờ chiếu phim">
                                    <?php if (empty($showtimes)): ?>
                                        <div class="no-showtimes">
                                            <i class="fas fa-clock"></i>
                                            <p>Không có suất chiếu nào trong ngày này</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($showtimes as $showtime): ?>
                                            <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $selected_theater; ?>&date=<?php echo $selected_date; ?>&time=<?php echo date('H:i', strtotime($showtime['show_time'])); ?>&showtime_id=<?php echo $showtime['id']; ?>" 
                                               class="time-btn <?php echo $selected_showtime_id == $showtime['id'] ? 'active' : ''; ?>"
                                               aria-pressed="<?php echo $selected_showtime_id == $showtime['id'] ? 'true' : 'false'; ?>"
                                               aria-label="Chọn suất chiếu lúc <?php echo date('H:i', strtotime($showtime['show_time'])); ?>">
                                                <?php echo date('H:i', strtotime($showtime['show_time'])); ?>
                                                <span class="time-price"><?php echo number_format($showtime['price']); ?>₫</span>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Seat Selection -->
                        <?php if ($selected_showtime_id): ?>
                            <?php 
                            $showtime = null;
                            foreach ($showtimes as $st) {
                                if ($st['id'] == $selected_showtime_id) {
                                    $showtime = $st;
                                    break;
                                }
                            }
                            ?>
                            <div class="booking-step mb-4">
                                <label class="booking-label">
                                    <i class="fas fa-chair me-2"></i>Chọn ghế ngồi
                                </label>
                                
                                <!-- Screen -->
                                <div class="cinema-screen mb-3" aria-label="Màn hình rạp chiếu phim">
                                    <div class="screen-text">MÀN HÌNH</div>
                                </div>
                                
                                <!-- Seat Map -->
                                <form method="POST" 
                                      action="http://localhost/DuAn1/?route=booking/process-booking" 
                                      id="booking-form"
                                      aria-label="Form đặt vé xem phim">
                                    <input type="hidden" name="showtime_id" value="<?php echo $selected_showtime_id; ?>">
                                    
                                    <div class="seat-map-container" role="group" aria-label="Bản đồ ghế ngồi trong rạp">
                                        <?php
                                        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
                                        $cols = range(1, 12);
                                        $lastRow = end($rows); // Hàng cuối (L)
                                        
                                        foreach ($rows as $row) {
                                            echo '<div class="seat-row ' . ($row === $lastRow ? 'couple-seat-row' : '') . '">';
                                            echo '<span class="row-label">' . $row . '</span>';
                                            echo '<div class="seats-in-row">';
                                            
                                            // Nếu là hàng cuối (L), tạo ghế đôi
                                            if ($row === $lastRow) {
                                                // Tạo 6 ghế đôi từ 12 ghế (mỗi ghế đôi gồm 2 ghế liên tiếp)
                                                for ($i = 1; $i <= 12; $i += 2) {
                                                    $seat1 = $row . $i;
                                                    $seat2 = $row . ($i + 1);
                                                    $isBooked1 = in_array($seat1, $bookedSeats);
                                                    $isBooked2 = in_array($seat2, $bookedSeats);
                                                    $isBooked = $isBooked1 || $isBooked2;
                                                    
                                                    echo '<label class="seat-label couple-seat ' . ($isBooked ? 'booked' : 'available') . '" title="Ghế đôi ' . $i . '-' . ($i + 1) . '">';
                                                    if (!$isBooked) {
                                                        // Tạo checkbox cho cả 2 ghế trong ghế đôi
                                                        echo '<input type="checkbox" name="seats[]" value="' . $seat1 . '" class="seat-checkbox couple-seat-checkbox" data-couple-seat="' . $seat2 . '">';
                                                        echo '<input type="checkbox" name="seats[]" value="' . $seat2 . '" class="seat-checkbox couple-seat-checkbox" data-couple-seat="' . $seat1 . '" style="display:none;">';
                                                    }
                                                    echo '<span class="seat-number">' . $i . '-' . ($i + 1) . '</span>';
                                                    echo '<span class="couple-icon"><i class="fas fa-heart"></i></span>';
                                                    echo '</label>';
                                                }
                                            } else {
                                                // Các hàng khác vẫn là ghế đơn
                                                foreach ($cols as $col) {
                                                    $seat = $row . $col;
                                                    $isBooked = in_array($seat, $bookedSeats);
                                                    
                                                    echo '<label class="seat-label ' . ($isBooked ? 'booked' : 'available') . '">';
                                                    if (!$isBooked) {
                                                        echo '<input type="checkbox" name="seats[]" value="' . $seat . '" class="seat-checkbox">';
                                                    }
                                                    echo '<span class="seat-number">' . $col . '</span>';
                                                    echo '</label>';
                                                }
                                            }
                                            
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                    
                                    <!-- Seat Legend -->
                                    <div class="seat-legend mt-3 mb-3" role="group" aria-label="Chú thích trạng thái ghế">
                                        <div class="legend-item">
                                            <span class="legend-seat available" aria-label="Ghế trống"></span>
                                            <span>Ghế trống</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat selected" aria-label="Ghế đang chọn"></span>
                                            <span>Ghế đang chọn</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat booked" aria-label="Ghế đã bán"></span>
                                            <span>Ghế đã bán</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat couple-seat" aria-label="Ghế đôi"></span>
                                            <span>Ghế đôi (hàng L)</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Total Price -->
                                    <div class="total-price-section mb-4" role="status" aria-live="polite">
                                        <div class="total-info">
                                            <span class="total-label">Tổng tiền:</span>
                                            <span class="total-seats" id="total-seats">0 ghế</span>
                                        </div>
                                        <span class="total-amount" id="total-amount" aria-label="Tổng số tiền phải thanh toán">0₫</span>
                                    </div>
                                    
                                    <!-- Submit Button -->
                                    <button type="submit" 
                                            class="btn-booking-submit" 
                                            id="submit-btn" 
                                            disabled
                                            aria-label="Xác nhận đặt vé">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        Đặt vé ngay
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Structured Data for SEO -->
<?php if ($movie): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Movie",
    "name": "<?php echo htmlspecialchars($movie['title']); ?>",
    <?php if ($movie['thumbnail']): ?>
    "image": "<?php echo htmlspecialchars($movie['thumbnail']); ?>",
    <?php endif; ?>
    <?php if ($movie['description']): ?>
    "description": "<?php echo htmlspecialchars(substr($movie['description'], 0, 200)); ?>",
    <?php endif; ?>
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?php echo number_format($movie['rating'] * 1.1, 1); ?>",
        "bestRating": "10"
    },
    <?php if ($movie['category_name']): ?>
    "genre": "<?php echo htmlspecialchars($movie['category_name']); ?>",
    <?php endif; ?>
    "offers": {
        "@type": "Offer",
        "availability": "https://schema.org/InStock",
        "priceCurrency": "VND",
        "category": "Movie Tickets"
    }
}
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.seat-checkbox');
    const totalAmountSpan = document.getElementById('total-amount');
    const totalSeatsSpan = document.getElementById('total-seats');
    const submitBtn = document.getElementById('submit-btn');
    const pricePerSeat = <?php echo isset($showtime) && $showtime ? $showtime['price'] : 0; ?>;
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Xử lý ghế đôi: khi chọn 1 ghế trong cặp thì tự động chọn ghế còn lại
            if (checkbox.classList.contains('couple-seat-checkbox')) {
                const coupleSeatId = checkbox.getAttribute('data-couple-seat');
                const coupleCheckbox = document.querySelector(`input[value="${coupleSeatId}"].couple-seat-checkbox`);
                if (coupleCheckbox && checkbox.checked) {
                    coupleCheckbox.checked = true;
                } else if (coupleCheckbox && !checkbox.checked) {
                    coupleCheckbox.checked = false;
                }
            }
            
            updateSelection();
        });
        
        // Add keyboard support
        const label = checkbox.closest('.seat-label');
        if (label && !label.classList.contains('booked')) {
            label.setAttribute('tabindex', '0');
            label.setAttribute('role', 'checkbox');
            label.setAttribute('aria-checked', checkbox.checked);
            
            label.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
        }
    });
    
    function updateSelection() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        // Update visual
        checkboxes.forEach(cb => {
            const label = cb.closest('.seat-label');
            if (label) {
                if (cb.checked) {
                    label.classList.add('selected');
                    label.classList.remove('available');
                    label.setAttribute('aria-checked', 'true');
                } else {
                    // Chỉ xóa selected nếu cả 2 ghế trong cặp đều không được chọn (cho ghế đôi)
                    if (label.classList.contains('couple-seat')) {
                        const coupleSeatId = cb.getAttribute('data-couple-seat');
                        const coupleCheckbox = document.querySelector(`input[value="${coupleSeatId}"].couple-seat-checkbox`);
                        if (coupleCheckbox && !coupleCheckbox.checked) {
                            label.classList.remove('selected');
                            if (!label.classList.contains('booked')) {
                                label.classList.add('available');
                            }
                        }
                    } else {
                        label.classList.remove('selected');
                        if (!label.classList.contains('booked')) {
                            label.classList.add('available');
                        }
                    }
                    label.setAttribute('aria-checked', 'false');
                }
            }
        });
        
        if (selected.length > 0) {
            const total = selected.length * pricePerSeat;
            totalAmountSpan.textContent = total.toLocaleString('vi-VN') + '₫';
            totalAmountSpan.setAttribute('aria-label', 'Tổng tiền ' + total.toLocaleString('vi-VN') + ' đồng');
            totalSeatsSpan.textContent = selected.length + ' ghế';
            submitBtn.disabled = false;
            submitBtn.setAttribute('aria-label', 'Xác nhận đặt ' + selected.length + ' vé');
        } else {
            totalAmountSpan.textContent = '0₫';
            totalAmountSpan.setAttribute('aria-label', 'Chưa chọn ghế nào');
            totalSeatsSpan.textContent = '0 ghế';
            submitBtn.disabled = true;
        }
    }
});
</script>
