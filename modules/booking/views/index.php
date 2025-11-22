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
                            <div class="row align-items-center mb-3">
                                <div class="col-md-6 col-12">
                                    <label class="booking-label mb-0">
                                        <i class="fas fa-building me-2"></i>Chọn rạp
                                    </label>
                                </div>
                                <div class="col-md-6 col-12 text-md-end text-start mt-md-0 mt-2">
                                    <button type="button" 
                                            class="btn btn-location-detect" 
                                            id="location-detect-btn"
                                            onclick="detectUserLocation()"
                                            aria-label="Xác định vị trí của bạn">
                                        <i class="fas fa-crosshairs me-2"></i>
                                        <span id="location-btn-text">Xác định vị trí</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Location Info -->
                            <div class="location-info" id="location-info" style="display: none;">
                                <div class="location-display">
                                    <!-- <i class="fas fa-map-marker-alt text-primary me-2"></i> -->
                                    <span id="location-text">Đang xác định vị trí...</span>
                                </div>
                            </div>
                            
                            <?php if (empty($theaters)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có rạp nào có suất chiếu phim này. Vui lòng liên hệ quản trị viên!
                                </div>
                            <?php else: ?>
                                <div class="theaters-list" role="group" aria-label="Danh sách rạp chiếu phim">
                                    <?php foreach ($theaters as $theater): ?>
                                        <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $theater['id']; ?>" 
                                           class="theater-btn <?php echo $selected_theater == $theater['id'] ? 'active' : ''; ?>"
                                           aria-pressed="<?php echo $selected_theater == $theater['id'] ? 'true' : 'false'; ?>">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <?php echo htmlspecialchars($theater['name']); ?>
                                            <?php if (!empty($theater['location'])): ?>
                                                <span class="theater-location"> - <?php echo htmlspecialchars($theater['location']); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Date Selection - chỉ hiển thị khi đã chọn rạp -->
                        <?php if ($selected_theater): ?>
                        <div class="booking-step mb-4">
                            <label class="booking-label">
                                <i class="fas fa-calendar-alt me-2"></i>Chọn ngày
                            </label>
                            <div class="dates-scroll" role="group" aria-label="Chọn ngày chiếu">
                                <?php foreach ($dates as $dateItem): ?>
                                    <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $selected_theater; ?>&date=<?php echo $dateItem['value']; ?>" 
                                       class="date-btn <?php echo $selected_date == $dateItem['value'] ? 'active' : ''; ?>"
                                       aria-pressed="<?php echo $selected_date == $dateItem['value'] ? 'true' : 'false'; ?>"
                                       aria-label="Chọn ngày <?php echo $dateItem['label']; ?>">
                                        <span class="date-day"><?php echo $dateItem['day_name']; ?></span>
                                        <span class="date-number"><?php echo $dateItem['label']; ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Time Selection - chỉ hiển thị khi đã chọn ngày và rạp -->
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
                                            <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $selected_theater; ?>&date=<?php echo $selected_date; ?>&showtime_id=<?php echo $showtime['id']; ?>" 
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
                            // Debug: Log booked seats passed to view
                            error_log("View - bookedSeats passed to view: " . print_r($bookedSeats ?? [], true));
                            error_log("View - reservedSeats passed to view: " . print_r($reservedSeats ?? [], true));
                            error_log("View - showtime_id: $selected_showtime_id");
                            
                            // Đảm bảo bookedSeats là array
                            if (!is_array($bookedSeats)) {
                                $bookedSeats = [];
                            }
                            if (!is_array($reservedSeats)) {
                                $reservedSeats = [];
                            }
                            
                            // Double check: Query lại từ BookingModel để đảm bảo lấy dữ liệu mới nhất
                            try {
                                require_once __DIR__ . '/../BookingModel.php';
                                $bookingModel = new BookingModel();
                                $directQuery = $bookingModel->getBookedSeats($selected_showtime_id);
                                $directBookedSeats = array_column($directQuery, 'seat');
                                error_log("View - Direct database query for showtime $selected_showtime_id - seats: " . implode(', ', $directBookedSeats));
                                
                                // Ưu tiên dữ liệu từ database (mới nhất) thay vì merge
                                // Vì có thể controller chưa cập nhật kịp
                                if (!empty($directBookedSeats)) {
                                    $bookedSeats = $directBookedSeats;
                                    error_log("View - Using direct query result as bookedSeats: " . implode(', ', $bookedSeats));
                                } else {
                                    // Nếu direct query rỗng nhưng controller có dữ liệu, vẫn dùng controller
                                    error_log("View - Direct query empty, using controller data: " . implode(', ', $bookedSeats));
                                }
                            } catch (Exception $e) {
                                error_log("Error in direct query: " . $e->getMessage());
                                // Nếu có lỗi, vẫn dùng dữ liệu từ controller
                            }
                            
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
                                                    $isBooked1 = in_array($seat1, $bookedSeats ?? []);
                                                    $isBooked2 = in_array($seat2, $bookedSeats ?? []);
                                                    $isBooked = $isBooked1 || $isBooked2;
                                                    
                                                    // Debug: Log couple seat status
                                                    if ($isBooked) {
                                                        error_log("Couple seat $seat1-$seat2 is BOOKED - showtime: $selected_showtime_id");
                                                    }
                                                    
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
                                                    $isBooked = in_array($seat, $bookedSeats ?? []);
                                                    $isReserved = in_array($seat, $reservedSeats ?? []);
                                                    
                                                    // Debug: Log seat status
                                                    if ($isBooked) {
                                                        error_log("Seat $seat is BOOKED - showtime: $selected_showtime_id");
                                                    }
                                                    
                                                    $seatClass = 'available';
                                                    if ($isBooked) {
                                                        $seatClass = 'booked';
                                                    } elseif ($isReserved) {
                                                        $seatClass = 'reserved';
                                                    }
                                                    
                                                    echo '<label class="seat-label ' . $seatClass . '" data-seat="' . $seat . '">';
                                                    if (!$isBooked && !$isReserved) {
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
                                            <span class="legend-seat reserved" aria-label="Ghế đang chọn (người khác)"></span>
                                            <span>Ghế đang chọn (người khác)</span>
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
                                    
                                    <!-- Email Input -->
                                    <div class="email-input-container-booking mb-4" id="email-container" style="display: none;">
                                        <div class="form-group">
                                            <label for="customer_email" class="form-label-booking">
                                                <i class="fas fa-envelope me-2"></i> Email nhận vé <span class="required">*</span>
                                            </label>
                                            <input 
                                                type="email" 
                                                id="customer_email" 
                                                name="customer_email" 
                                                class="form-control-booking" 
                                                placeholder="Nhập email của bạn để nhận vé"
                                                required
                                            >
                                            <small class="form-text-booking">Vé và QR code sẽ được gửi đến email này sau khi thanh toán</small>
                                        </div>
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
                    
                    <!-- Support Section -->
                    <div class="booking-step mb-4 mt-4">
                        <div class="support-section">
                            <button type="button" class="btn-support-toggle" id="supportToggleBtn" onclick="toggleSupportForm()">
                                <i class="fas fa-headset me-2"></i>
                                <span>Cần hỗ trợ?</span>
                            </button>
                            
                            <div class="support-form-container" id="supportFormContainer" style="display: none;">
                                <div class="support-form-header">
                                    <h5><i class="fas fa-headset me-2"></i>Gửi yêu cầu hỗ trợ</h5>
                                    <button type="button" class="btn-close-support" onclick="toggleSupportForm()" aria-label="Đóng form hỗ trợ">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <form method="POST" action="?route=booking/submit-support" class="support-form" id="supportForm">
                                    <div class="mb-3">
                                        <label for="support-issue" class="form-label">Mục vấn đề <span class="text-danger">*</span></label>
                                        <select class="form-select" id="support-issue" name="issue" required>
                                            <option value="">-- Chọn mục vấn đề --</option>
                                            <option value="Lỗi thanh toán">Lỗi thanh toán</option>
                                            <option value="Không nhận được vé">Không nhận được vé</option>
                                            <option value="Vấn đề về ghế ngồi">Vấn đề về ghế ngồi</option>
                                            <option value="Hủy/Đổi vé">Hủy/Đổi vé</option>
                                            <option value="Lỗi hệ thống">Lỗi hệ thống</option>
                                            <option value="Thông tin rạp chiếu">Thông tin rạp chiếu</option>
                                            <option value="Khác">Khác</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="support-message" class="form-label">Nội dung <span class="text-danger">*</span></label>
                                        <textarea class="form-control" 
                                                  id="support-message" 
                                                  name="message" 
                                                  rows="5" 
                                                  placeholder="Mô tả chi tiết vấn đề bạn gặp phải..." 
                                                  required></textarea>
                                        <small class="text-muted">Vui lòng mô tả chi tiết để chúng tôi có thể hỗ trợ bạn tốt nhất.</small>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="toggleSupportForm()">
                                            Hủy
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
    
    // Kiểm tra vị trí đã lưu khi trang load
    const savedLocation = localStorage.getItem('userLocation');
    if (savedLocation) {
        try {
            const location = JSON.parse(savedLocation);
            const now = Date.now();
            // Nếu vị trí còn mới (dưới 1 giờ), hiển thị lại
            if (now - location.timestamp < 3600000) {
                const locationInfo = document.getElementById('location-info');
                const locationText = document.getElementById('location-text');
                if (locationInfo && locationText) {
                    locationInfo.style.display = 'block';
                    locationText.innerHTML = `
                        <span class="text-info">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Vị trí đã lưu: ${location.lat.toFixed(6)}, ${location.lng.toFixed(6)}
                        </span>
                    `;
                    getAddressFromCoordinates(location.lat, location.lng);
                }
            }
        } catch (e) {
            console.log('Error loading saved location:', e);
        }
    }
    
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
        
        const emailContainer = document.getElementById('email-container');
        const emailInput = document.getElementById('customer_email');
        
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
            
            // Hiển thị trường email
            if (emailContainer) {
                emailContainer.style.display = 'block';
            }
        } else {
            totalAmountSpan.textContent = '0₫';
            totalAmountSpan.setAttribute('aria-label', 'Chưa chọn ghế nào');
            totalSeatsSpan.textContent = '0 ghế';
            submitBtn.disabled = true;
            
            // Ẩn trường email và xóa giá trị
            if (emailContainer) {
                emailContainer.style.display = 'none';
            }
            if (emailInput) {
                emailInput.value = '';
            }
        }
    }
    
    // Real-time seat reservation system
    <?php if ($selected_showtime_id): ?>
    const showtimeId = <?php echo $selected_showtime_id; ?>;
    let selectedSeats = [];
    let pollingInterval = null;
    let reservationTimeout = null;
    
    // Reserve seats when selected
    function reserveSeats(seats) {
        if (seats.length === 0) return;
        
        fetch('?route=booking/reserve-seats-api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                showtime_id: showtimeId,
                seats: seats
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Extend reservation every 4 minutes (before 5 minutes expire)
                reservationTimeout = setInterval(() => {
                    extendReservations(seats);
                }, 4 * 60 * 1000);
            }
        })
        .catch(error => console.error('Error reserving seats:', error));
    }
    
    // Release seats when deselected
    function releaseSeats(seats) {
        if (seats.length === 0) return;
        
        if (reservationTimeout) {
            clearInterval(reservationTimeout);
            reservationTimeout = null;
        }
        
        fetch('?route=booking/release-seats-api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                showtime_id: showtimeId,
                seats: seats
            })
        })
        .catch(error => console.error('Error releasing seats:', error));
    }
    
    // Extend reservations
    function extendReservations(seats) {
        if (seats.length === 0) return;
        
        fetch('?route=booking/extend-reservation-api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                showtime_id: showtimeId,
                seats: seats
            })
        })
        .catch(error => console.error('Error extending reservations:', error));
    }
    
    // Check seat status real-time
    function checkSeatStatus() {
        fetch(`?route=booking/get-seat-status-api&showtime_id=${showtimeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateSeatStatus(data.booked_seats, data.reserved_seats);
                }
            })
            .catch(error => console.error('Error checking seat status:', error));
    }
    
    // Update seat visual status
    function updateSeatStatus(bookedSeats, reservedSeats) {
        document.querySelectorAll('.seat-label').forEach(label => {
            const seat = label.getAttribute('data-seat');
            if (!seat) return;
            
            // Skip if seat is currently selected by this user
            const checkbox = label.querySelector('.seat-checkbox');
            if (checkbox && checkbox.checked) return;
            
            // Remove all status classes
            label.classList.remove('booked', 'reserved', 'available');
            
            if (bookedSeats.includes(seat)) {
                label.classList.add('booked');
                // Remove checkbox if booked
                if (checkbox) checkbox.remove();
            } else if (reservedSeats[seat]) {
                label.classList.add('reserved');
                // Remove checkbox if reserved
                if (checkbox) checkbox.remove();
            } else {
                label.classList.add('available');
                // Re-add checkbox if available
                if (!checkbox) {
                    const seatNum = label.querySelector('.seat-number').textContent;
                    const row = seat.charAt(0);
                    const col = seat.substring(1);
                    const newCheckbox = document.createElement('input');
                    newCheckbox.type = 'checkbox';
                    newCheckbox.name = 'seats[]';
                    newCheckbox.value = seat;
                    newCheckbox.className = 'seat-checkbox';
                    if (label.classList.contains('couple-seat')) {
                        newCheckbox.classList.add('couple-seat-checkbox');
                    }
                    label.insertBefore(newCheckbox, label.firstChild);
                }
            }
        });
    }
    
    // Start polling for seat status updates (every 2 seconds)
    if (showtimeId) {
        checkSeatStatus(); // Check immediately
        pollingInterval = setInterval(checkSeatStatus, 2000);
        
        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (selectedSeats.length > 0) {
                releaseSeats(selectedSeats);
            }
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
        });
        
        // Override updateSelection to handle reservations
        const originalUpdateSelection = updateSelection;
        updateSelection = function() {
            const newSelected = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            // Release seats that are no longer selected
            const toRelease = selectedSeats.filter(seat => !newSelected.includes(seat));
            if (toRelease.length > 0) {
                releaseSeats(toRelease);
            }
            
            // Reserve newly selected seats
            const toReserve = newSelected.filter(seat => !selectedSeats.includes(seat));
            if (toReserve.length > 0) {
                reserveSeats(toReserve);
            }
            
            selectedSeats = newSelected;
            originalUpdateSelection();
        };
    }
    <?php endif; ?>
    
    // Handle form submit - Mark seats as booked before submitting
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            // Lấy các ghế đã chọn
            const selectedCheckboxes = Array.from(document.querySelectorAll('.seat-checkbox:checked'));
            const selectedSeatValues = selectedCheckboxes.map(cb => cb.value);
            
            if (selectedSeatValues.length > 0) {
                // Đánh dấu ghế đã chọn thành "đã bán" ngay lập tức
                selectedCheckboxes.forEach(checkbox => {
                    const label = checkbox.closest('.seat-label');
                    if (label) {
                        // Remove selected class
                        label.classList.remove('selected', 'available', 'reserved');
                        // Add booked class
                        label.classList.add('booked');
                        
                        // Remove checkbox
                        checkbox.remove();
                        
                        // Disable seat interaction
                        label.style.cursor = 'not-allowed';
                        label.style.opacity = '0.6';
                        
                        // Update aria-checked
                        label.setAttribute('aria-checked', 'false');
                        label.setAttribute('aria-disabled', 'true');
                    }
                });
                
                // Release reservations của ghế đã chọn (vì đã được đặt rồi)
                <?php if ($selected_showtime_id): ?>
                if (typeof releaseSeats === 'function') {
                    releaseSeats(selectedSeatValues);
                }
                if (reservationTimeout) {
                    clearInterval(reservationTimeout);
                    reservationTimeout = null;
                }
                // Stop polling vì đã đặt vé rồi
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
                <?php endif; ?>
                
                // Disable submit button để tránh double submit
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
                }
                
                // Update total info để hiển thị "Đã đặt"
                const totalSeatsSpan = document.getElementById('total-seats');
                if (totalSeatsSpan) {
                    totalSeatsSpan.textContent = selectedSeatValues.length + ' ghế - Đang xử lý...';
                }
                
                // Update selected seats display
                const selectedSeatsSpan = document.getElementById('selected-seats');
                if (selectedSeatsSpan) {
                    selectedSeatsSpan.textContent = selectedSeatValues.join(', ') + ' (Đã đặt)';
                    selectedSeatsSpan.style.color = '#dc3545';
                }
                
                // Hide email input
                const emailContainer = document.getElementById('email-container');
                if (emailContainer) {
                    emailContainer.style.display = 'none';
                }
            }
        });
    }
});

// Support Form Toggle
function toggleSupportForm() {
    const container = document.getElementById('supportFormContainer');
    const btn = document.getElementById('supportToggleBtn');
    
    if (container.style.display === 'none') {
        container.style.display = 'block';
        btn.style.display = 'none';
    } else {
        container.style.display = 'none';
        btn.style.display = 'block';
    }
}

// Location Detection
function detectUserLocation() {
    const locationInfo = document.getElementById('location-info');
    const locationText = document.getElementById('location-text');
    const locationBtn = document.getElementById('location-detect-btn');
    const locationBtnText = document.getElementById('location-btn-text');
    
    if (!navigator.geolocation) {
        locationText.innerHTML = '<span class="text-warning">Trình duyệt của bạn không hỗ trợ xác định vị trí</span>';
        locationInfo.style.display = 'block';
        return;
    }
    
    // Hiển thị trạng thái đang tải
    locationBtn.disabled = true;
    locationBtnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xác định...';
    locationInfo.style.display = 'block';
    locationText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xác định vị trí của bạn...';
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // Lưu vị trí vào localStorage
            localStorage.setItem('userLocation', JSON.stringify({
                lat: latitude,
                lng: longitude,
                timestamp: Date.now()
            }));
            
            // Hiển thị tọa độ
            locationText.innerHTML = `
                <span class="text-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Đã xác định vị trí: ${latitude.toFixed(6)}, ${longitude.toFixed(6)}
                </span>
            `;
            
            // Thử lấy địa chỉ từ reverse geocoding (nếu có thể)
            getAddressFromCoordinates(latitude, longitude);
            
            locationBtn.disabled = false;
            locationBtnText.innerHTML = '<i class="fas fa-redo me-2"></i>Cập nhật vị trí';
            
            // Sắp xếp rạp theo khoảng cách (nếu có thể)
            sortTheatersByDistance(latitude, longitude);
        },
        function(error) {
            let errorMessage = 'Không thể xác định vị trí. ';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage += 'Bạn đã từ chối quyền truy cập vị trí.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage += 'Thông tin vị trí không khả dụng.';
                    break;
                case error.TIMEOUT:
                    errorMessage += 'Yêu cầu xác định vị trí đã hết thời gian chờ.';
                    break;
                default:
                    errorMessage += 'Đã xảy ra lỗi không xác định.';
                    break;
            }
            locationText.innerHTML = `<span class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>${errorMessage}</span>`;
            locationBtn.disabled = false;
            locationBtnText.innerHTML = '<i class="fas fa-crosshairs me-2"></i>Xác định vị trí';
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

// Reverse Geocoding - Lấy địa chỉ từ tọa độ
function getAddressFromCoordinates(lat, lng) {
    // Sử dụng Nominatim API (OpenStreetMap) để reverse geocoding
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
        .then(response => response.json())
        .then(data => {
            if (data && data.address) {
                const address = data.address;
                let addressString = '';
                
                if (address.road) addressString += address.road + ', ';
                if (address.suburb || address.village) addressString += (address.suburb || address.village) + ', ';
                if (address.city || address.town || address.county) addressString += (address.city || address.town || address.county) + ', ';
                if (address.state) addressString += address.state;
                
                if (addressString) {
                    const locationText = document.getElementById('location-text');
                    locationText.innerHTML = `
                        <span class="text-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Vị trí: ${addressString.trim().replace(/,\s*$/, '')}
                        </span>
                    `;
                }
            }
        })
        .catch(error => {
            console.log('Reverse geocoding failed:', error);
        });
}

// Sắp xếp rạp theo khoảng cách (nếu có tọa độ rạp)
function sortTheatersByDistance(userLat, userLng) {
    const theaters = document.querySelectorAll('.theater-btn');
    const theatersArray = Array.from(theaters);
    
    // Tính khoảng cách và sắp xếp
    theatersArray.forEach(theater => {
        const locationSpan = theater.querySelector('.theater-location');
        // Có thể thêm logic tính khoảng cách nếu có tọa độ rạp trong database
        // Hiện tại chỉ hiển thị thông tin
    });
}

</script>
