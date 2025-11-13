<?php
$current_page = 'booking';
$title = 'Đặt Vé';
?>

<section class="booking-page-section">
    <div class="container-fluid px-4">
        <div class="row g-4">
            <!-- Left Column: Movie Info -->
            <div class="col-lg-5">
                <?php if ($movie): ?>
                    <div class="booking-movie-info">
                        <!-- Movie Poster -->
                        <div class="movie-poster-large mb-4">
                            <?php if ($movie['thumbnail']): ?>
                                <img src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" class="img-fluid rounded">
                            <?php else: ?>
                                <div class="poster-placeholder">
                                    <i class="fas fa-film fa-5x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Movie Title -->
                        <h1 class="movie-title-booking mb-3"><?php echo htmlspecialchars($movie['title']); ?></h1>
                        
                        <!-- IMDb Rating -->
                        <div class="movie-rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <span class="imdb-rating">IMDb <?php echo number_format($movie['rating'] * 1.1, 1); ?></span>
                        </div>
                        
                        <!-- Categories -->
                        <?php if ($movie['category_name']): ?>
                            <div class="movie-categories mb-3">
                                <span class="category-badge"><?php echo htmlspecialchars($movie['category_name']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Description -->
                        <?php if ($movie['description']): ?>
                            <p class="movie-description mb-4">
                                <?php echo htmlspecialchars($movie['description']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <!-- Trailer Button -->
                        <?php if ($movie['trailer_url']): ?>
                            <button class="btn-trailer" onclick="window.open('<?php echo htmlspecialchars($movie['trailer_url']); ?>', '_blank')">
                                <i class="fas fa-play me-2"></i> Xem Trailer
                            </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="booking-movie-info">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Vui lòng chọn phim để đặt vé.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Right Column: Booking Form -->
            <div class="col-lg-7">
                <div class="booking-form-container">
                    <h2 class="booking-form-title mb-4">Đặt vé xem phim</h2>
                    
                    <!-- Movie Selection -->
                    <?php if (empty($selected_movie)): ?>
                        <div class="booking-step mb-4">
                            <label class="booking-label">Chọn phim</label>
                            <?php if (empty($movies)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có phim nào đang chiếu rạp. Vui lòng quay lại sau!
                                </div>
                            <?php else: ?>
                                <form method="GET" class="movie-select-form">
                                    <input type="hidden" name="route" value="booking/index">
                                    <select name="movie" class="form-select-booking" onchange="this.form.submit()" required>
                                        <option value="">-- Chọn phim --</option>
                                        <?php foreach ($movies as $m): ?>
                                            <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['title']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Date Selection -->
                        <div class="booking-step mb-4">
                            <label class="booking-label">Chọn ngày</label>
                            <div class="dates-scroll">
                                <?php foreach ($dates as $dateItem): ?>
                                    <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $selected_theater; ?>&date=<?php echo $dateItem['value']; ?>&time=<?php echo $selected_time; ?>&showtime_id=<?php echo $selected_showtime_id; ?>" 
                                       class="date-btn <?php echo $selected_date == $dateItem['value'] ? 'active' : ''; ?>">
                                        <?php echo $dateItem['day_name']; ?><br>
                                        <?php echo $dateItem['label']; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Time Selection -->
                        <?php if ($selected_date && $selected_theater): ?>
                            <div class="booking-step mb-4">
                                <label class="booking-label">Chọn giờ</label>
                                <div class="times-grid">
                                    <?php if (empty($showtimes)): ?>
                                        <p class="text-muted">Không có suất chiếu nào</p>
                                    <?php else: ?>
                                        <?php foreach ($showtimes as $showtime): ?>
                                            <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $selected_theater; ?>&date=<?php echo $selected_date; ?>&time=<?php echo date('H:i', strtotime($showtime['show_time'])); ?>&showtime_id=<?php echo $showtime['id']; ?>" 
                                               class="time-btn <?php echo $selected_showtime_id == $showtime['id'] ? 'active' : ''; ?>">
                                                <?php echo date('H:i', strtotime($showtime['show_time'])); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Theater Selection -->
                        <div class="booking-step mb-4">
                            <label class="booking-label">Chọn rạp</label>
                            <?php if (empty($theaters)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có rạp nào. Vui lòng liên hệ quản trị viên!
                                </div>
                            <?php else: ?>
                                <div class="theaters-list">
                                    <?php foreach ($theaters as $theater): ?>
                                        <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $theater['id']; ?>&date=<?php echo $selected_date; ?>&time=<?php echo $selected_time; ?>&showtime_id=<?php echo $selected_showtime_id; ?>" 
                                           class="theater-btn <?php echo $selected_theater == $theater['id'] ? 'active' : ''; ?>">
                                            <?php echo htmlspecialchars($theater['name']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
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
                                <label class="booking-label">Chọn ghế</label>
                                
                                <!-- Screen -->
                                <div class="cinema-screen mb-3">
                                    <div class="screen-text">MÀN HÌNH</div>
                                </div>
                                
                                <!-- Seat Map -->
                                <form method="POST" action="http://localhost/DuAn1/?route=booking/process-booking" id="booking-form">
                                    <input type="hidden" name="showtime_id" value="<?php echo $selected_showtime_id; ?>">
                                    
                                    <div class="seat-map-container">
                                        <?php
                                        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
                                        $cols = range(1, 12);
                                        
                                        foreach ($rows as $row) {
                                            echo '<div class="seat-row">';
                                            echo '<span class="row-label">' . $row . '</span>';
                                            echo '<div class="seats-in-row">';
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
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                    
                                    <!-- Seat Legend -->
                                    <div class="seat-legend mt-3 mb-3">
                                        <div class="legend-item">
                                            <span class="legend-seat available"></span>
                                            <span>Ghế trống</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat selected"></span>
                                            <span>Ghế đang chọn</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat booked"></span>
                                            <span>Ghế đã bán</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Total Price -->
                                    <div class="total-price-section mb-4">
                                        <span class="total-label">Tổng tiền:</span>
                                        <span class="total-amount" id="total-amount">0₫</span>
                                    </div>
                                    
                                    <!-- Submit Button -->
                                    <button type="submit" class="btn-booking-submit" id="submit-btn" disabled>
                                        Đặt vé
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.seat-checkbox');
    const totalAmountSpan = document.getElementById('total-amount');
    const submitBtn = document.getElementById('submit-btn');
    const pricePerSeat = <?php echo isset($showtime) && $showtime ? $showtime['price'] : 0; ?>;
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelection();
        });
    });
    
    function updateSelection() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        // Update visual
        checkboxes.forEach(cb => {
            const label = cb.closest('.seat-label');
            if (cb.checked) {
                label.classList.add('selected');
                label.classList.remove('available');
            } else {
                label.classList.remove('selected');
                if (!label.classList.contains('booked')) {
                    label.classList.add('available');
                }
            }
        });
        
        if (selected.length > 0) {
            const total = selected.length * pricePerSeat;
            totalAmountSpan.textContent = total.toLocaleString('vi-VN') + '₫';
            submitBtn.disabled = false;
        } else {
            totalAmountSpan.textContent = '0₫';
            submitBtn.disabled = true;
        }
    }
});
</script>
