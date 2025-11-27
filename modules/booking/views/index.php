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
                        <div class="background_film_blur">
                                <img  src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="">
                        </div>
                        
<style>
.booking-movie-info {
  position: relative; /* tạo vùng z-index */
  z-index: 2;
}

.background_film_blur {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
}

.background_film_blur img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: blur(4px);
  opacity: 0.5;
}

.movie-poster-large {
  position: relative;
  z-index: 2;
}

.movie-poster-large img {
  width: 100%;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.movie-title-booking,
.movie-rating,
.movie-description,
.movie-categories,
.btn-trailer {
  position: relative;
  z-index: 3; /* cao nhất */
  color: white;
}


</style>
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
                    
                    <!-- Movies List - chỉ hiển thị khi chưa chọn phim -->
                    <?php if (empty($selected_movie)): ?>
                        <div class="booking-step mb-4">
                            <label class="booking-label">
                                <i class="fas fa-film me-2"></i>Danh sách phim đang chiếu
                            </label>
                            <?php if (empty($allMovies)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có phim nào đang chiếu rạp. Vui lòng quay lại sau!
                                </div>
                            <?php else: ?>
                                <div class="movies-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
                                    <?php foreach ($allMovies as $m): ?>
                                        <a href="?route=booking/index&movie=<?php echo $m['id']; ?>" 
                                           class="movie-card-booking"
                                           onclick="sessionStorage.setItem('bookingScrollPos', window.pageYOffset || document.documentElement.scrollTop);"
                                           style="display: block; text-decoration: none; border: 2px solid #ddd; border-radius: 8px; overflow: hidden; transition: all 0.3s; background: white; cursor: pointer;">
                                            <?php if ($m['thumbnail']): ?>
                                                <img src="<?php echo htmlspecialchars($m['thumbnail']); ?>" 
                                                     alt="<?php echo htmlspecialchars($m['title']); ?>" 
                                                     style="width: 100%; height: 200px; object-fit: cover;">
                                            <?php else: ?>
                                                <div style="width: 100%; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-film" style="font-size: 48px; color: #999;"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div style="padding: 10px;">
                                                <h4 style="margin: 0; font-size: 14px; color: #333; font-weight: bold; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?php echo htmlspecialchars($m['title']); ?>
                                                </h4>
                                                <?php if ($m['rating']): ?>
                                                    <div style="text-align: center; margin-top: 5px;">
                                                        <i class="fas fa-star text-warning" style="font-size: 12px;"></i>
                                                        <span style="font-size: 12px; color: #666;"><?php echo number_format($m['rating'], 1); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Theater Selection for selected movie -->
                        <?php if (empty($theaters)): ?>
                            <div class="alert alert-warning mb-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Hiện tại chưa có rạp nào có suất chiếu phim này. Vui lòng liên hệ quản trị viên!
                            </div>
                        <?php else: ?>
                            <div class="booking-step mb-4">
                                <label class="booking-label">
                                    <i class="fas fa-building me-2"></i>Chọn rạp cho phim này
                                </label>
                                <div class="theaters-list" role="group" aria-label="Danh sách rạp chiếu phim" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
                                    <?php foreach ($theaters as $theater): ?>
                                        <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $theater['id']; ?>" 
                                           class="theater-btn <?php echo $selected_theater == $theater['id'] ? 'active' : ''; ?>"
                                           onclick="sessionStorage.setItem('bookingScrollPos', window.pageYOffset || document.documentElement.scrollTop);"
                                           aria-pressed="<?php echo $selected_theater == $theater['id'] ? 'true' : 'false'; ?>"
                                           style="padding: 12px 20px; border: 2px solid <?php echo $selected_theater == $theater['id'] ? '#e50914' : '#ddd'; ?>; border-radius: 8px; text-decoration: none; color: <?php echo $selected_theater == $theater['id'] ? '#e50914' : '#333'; ?>; background: <?php echo $selected_theater == $theater['id'] ? '#fff5f5' : 'white'; ?>; transition: all 0.3s; font-weight: <?php echo $selected_theater == $theater['id'] ? 'bold' : 'normal'; ?>;">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <?php echo htmlspecialchars($theater['name']); ?>
                                            <?php if (!empty($theater['location'])): ?>
                                                <span style="font-size: 12px; color: #666;"> - <?php echo htmlspecialchars($theater['location']); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
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
                        
                        <!-- Thông tin rạp và phòng chiếu - Hiển thị khi chọn showtime -->
                        <?php if ($selected_showtime_id && ($screenInfo || $theaterInfo)): ?>
                            <div class="theater-screen-info mb-3" style="background: #1a1a1a; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                                <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                                    <?php if ($theaterInfo): ?>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-building" style="color: #e50914; font-size: 18px;"></i>
                                            <span style="color: #fff; font-weight: 600; font-size: 16px;">
                                                <?php echo htmlspecialchars($theaterInfo['name']); ?>
                                                <?php if ($theaterInfo['location']): ?>
                                                    <span style="color: #999; font-weight: normal; font-size: 14px;">- <?php echo htmlspecialchars($theaterInfo['location']); ?></span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($screenInfo): ?>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-door-open" style="color: #28a745; font-size: 18px;"></i>
                                            <span style="color: #fff; font-weight: 600; font-size: 16px;">
                                                <?php echo htmlspecialchars($screenInfo['screen_name']); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Reservation Timer - Hiển thị ngay khi chọn showtime -->
                        <?php if ($selected_showtime_id): ?>
                            <div id="reservation-timer" class="reservation-timer mb-3" style="display: block;">
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Thời gian giữ ghế:</strong> 
                                    <span id="timer-countdown">10:00</span>
                                    <small class="ms-2">(Bạn có 10 phút để hoàn tất thanh toán)</small>
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
                                      action="?route=booking/process-booking" 
                                      id="booking-form"
                                      aria-label="Form đặt vé xem phim"
                                      onsubmit="return validateBookingForm(event);">
                                    <input type="hidden" name="showtime_id" value="<?php echo $selected_showtime_id; ?>">
                                    
                                    <div class="seat-map-container" role="group" aria-label="Bản đồ ghế ngồi trong rạp"<?php 
                                    // Tính số ghế tối đa để set data attribute
                                    $maxSeats = 0;
                                    if (isset($seat_groups) && is_array($seat_groups)) {
                                        $tempRowColsMap = [];
                                        foreach ($seat_groups as $group) {
                                            $groupRows = $group['rows'] ?? [];
                                            $groupCols = $group['cols'] ?? [];
                                            foreach ($groupRows as $row) {
                                                if (!isset($tempRowColsMap[$row])) {
                                                    $tempRowColsMap[$row] = [];
                                                }
                                                foreach ($groupCols as $col) {
                                                    if (!in_array($col, $tempRowColsMap[$row])) {
                                                        $tempRowColsMap[$row][] = $col;
                                                    }
                                                }
                                            }
                                        }
                                        foreach ($tempRowColsMap as $row => $cols) {
                                            $count = count($cols);
                                            if ($count > $maxSeats) {
                                                $maxSeats = $count;
                                            }
                                        }
                                    } elseif (isset($cols)) {
                                        $maxSeats = count($cols);
                                    }
                                    if ($maxSeats > 0) {
                                        echo ' data-seats-per-row="' . $maxSeats . '" style="--seats-count: ' . $maxSeats . ';"';
                                    }
                                    ?>>
                                        <?php
                                        // Lấy seat layout từ config hoặc dùng default
                                        $layout = $seatLayout ?? [
                                            'rows' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'],
                                            'cols' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                                            'vip_rows' => ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'], // Từ hàng D (hàng 4) trở xuống là VIP
                                            'couple_rows' => ['L'], // Hàng cuối là ghế đôi
                                            'normal_price' => 120000,
                                            'vip_price' => 180000,
                                            'couple_price' => 240000
                                        ];
                                        
                                        $rows = $layout['rows'] ?? ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
                                        $cols = $layout['cols'] ?? range(1, 12);
                                        $vip_rows = $layout['vip_rows'] ?? [];
                                        $couple_rows = $layout['couple_rows'] ?? [];
                                        
                                        // Nếu không có config, tự động phân loại: 3 hàng đầu = thường, từ hàng 4 (D) trở xuống = VIP, cuối = ghế đôi
                                        if (empty($vip_rows) && empty($couple_rows) && count($rows) > 3) {
                                            // 3 hàng đầu (A-C) = thường
                                            // Từ hàng 4 (D) đến hàng trước cuối = VIP
                                            // Hàng cuối = ghế đôi
                                            $normalRows = array_slice($rows, 0, 3); // A, B, C
                                            $vip_rows = array_slice($rows, 3, -1); // D, E, F, G, H, I, J, K (từ hàng 4 trở xuống)
                                            $couple_rows = [end($rows)]; // L
                                        }
                                        
                                        require_once __DIR__ . '/../BookingModel.php';
                                        $bookingModel = new BookingModel();
                                        
                                        // Kiểm tra xem có layout phức tạp không (có seat_groups)
                                        $layout_type = $layout['layout_type'] ?? 'standard';
                                        $seat_groups = $layout['seat_groups'] ?? null;
                                        
                                        // Nếu có seat_groups, render theo layout phức tạp
                                        if ($seat_groups && is_array($seat_groups)) {
                                            // Tạo map để lưu các cột của mỗi hàng từ các nhóm
                                            $rowColsMap = [];
                                            $maxSeatsPerRow = 0;
                                            foreach ($seat_groups as $group) {
                                                $groupRows = $group['rows'] ?? [];
                                                $groupCols = $group['cols'] ?? [];
                                                
                                                foreach ($groupRows as $row) {
                                                    if (!isset($rowColsMap[$row])) {
                                                        $rowColsMap[$row] = [];
                                                    }
                                                    // Thêm các cột từ nhóm này vào hàng
                                                    foreach ($groupCols as $col) {
                                                        if (!in_array($col, $rowColsMap[$row])) {
                                                            $rowColsMap[$row][] = $col;
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            // Tính số ghế tối đa trong một hàng (bao gồm cả separators)
                                            foreach ($rowColsMap as $row => $cols) {
                                                $seatCount = count($cols);
                                                // Đếm số nhóm trong hàng này để tính separators
                                                $groupCount = 0;
                                                $prevGroupIndex = null;
                                                foreach ($seat_groups as $groupIndex => $group) {
                                                    $groupRows = $group['rows'] ?? [];
                                                    $groupCols = $group['cols'] ?? [];
                                                    if (in_array($row, $groupRows)) {
                                                        $hasSeatsInRow = false;
                                                        foreach ($groupCols as $col) {
                                                            if (in_array($col, $cols)) {
                                                                $hasSeatsInRow = true;
                                                                break;
                                                            }
                                                        }
                                                        if ($hasSeatsInRow && $prevGroupIndex !== $groupIndex) {
                                                            $groupCount++;
                                                            $prevGroupIndex = $groupIndex;
                                                        }
                                                    }
                                                }
                                                // Mỗi separator chiếm khoảng 1.5rem (tương đương ~1.5 ghế)
                                                $totalWidth = $seatCount + ($groupCount > 1 ? ($groupCount - 1) * 1.5 : 0);
                                                if ($totalWidth > $maxSeatsPerRow) {
                                                    $maxSeatsPerRow = ceil($totalWidth);
                                                }
                                            }
                                            
                                            // Sắp xếp các hàng và cột
                                            ksort($rowColsMap);
                                            foreach ($rowColsMap as $row => $cols) {
                                                // Sắp xếp cột theo thứ tự trong seat_groups (giữ nguyên thứ tự từ trái sang phải)
                                                $sortedCols = [];
                                                foreach ($seat_groups as $group) {
                                                    $groupRows = $group['rows'] ?? [];
                                                    $groupCols = $group['cols'] ?? [];
                                                    if (in_array($row, $groupRows)) {
                                                        foreach ($groupCols as $col) {
                                                            if (in_array($col, $cols) && !in_array($col, $sortedCols)) {
                                                                $sortedCols[] = $col;
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                $isVipRow = in_array($row, $vip_rows);
                                                $isCoupleRow = in_array($row, $couple_rows);
                                                
                                                echo '<div class="seat-row ' . ($isCoupleRow ? 'couple-seat-row' : '') . ($isVipRow ? ' vip-row' : '') . '">';
                                                echo '<span class="row-label">' . $row . '</span>';
                                                echo '<div class="seats-in-row">';
                                                
                                                // Tạo map để xác định nhóm của mỗi cột
                                                $colToGroupMap = [];
                                                foreach ($seat_groups as $groupIndex => $group) {
                                                    $groupRows = $group['rows'] ?? [];
                                                    $groupCols = $group['cols'] ?? [];
                                                    if (in_array($row, $groupRows)) {
                                                        foreach ($groupCols as $col) {
                                                            $colToGroupMap[$col] = $groupIndex;
                                                        }
                                                    }
                                                }
                                                
                                                // Render các cột đã sắp xếp với khoảng cách giữa các nhóm
                                                $prevGroupIndex = null;
                                                foreach ($sortedCols as $index => $col) {
                                                    $currentGroupIndex = $colToGroupMap[$col] ?? null;
                                                    
                                                    // Thêm khoảng cách nếu chuyển sang nhóm mới
                                                    if ($prevGroupIndex !== null && $currentGroupIndex !== $prevGroupIndex) {
                                                        echo '<span class="seat-group-separator" style="width: 1.5rem; display: inline-block;"></span>';
                                                    }
                                                    
                                                    $seat = $row . $col;
                                                    $isBooked = in_array($seat, $bookedSeats ?? []);
                                                    $isReserved = in_array($seat, $reservedSeats ?? []);
                                                    
                                                    $seatClass = 'available';
                                                    if ($isBooked) {
                                                        $seatClass = 'booked';
                                                    } elseif ($isReserved) {
                                                        $seatClass = 'reserved';
                                                    }
                                                    
                                                    if ($isVipRow) {
                                                        $seatClass .= ' vip-seat';
                                                    }
                                                    
                                                    echo '<label class="seat-label ' . $seatClass . '" data-seat="' . $seat . '" data-seat-type="' . ($isVipRow ? 'vip' : 'normal') . '">';
                                                    if (!$isBooked && !$isReserved) {
                                                        echo '<input type="checkbox" name="seats[]" value="' . $seat . '" class="seat-checkbox" data-seat-type="' . ($isVipRow ? 'vip' : 'normal') . '">';
                                                    }
                                                    echo '<span class="seat-number">' . $col . '</span>';
                                                    if ($isVipRow) {
                                                        echo '<span class="seat-icon vip-icon" title="Ghế VIP"><i class="fas fa-crown"></i></span>';
                                                    } else {
                                                        echo '<span class="seat-icon normal-icon" title="Ghế thường"><i class="fas fa-chair"></i></span>';
                                                    }
                                                    echo '</label>';
                                                    
                                                    $prevGroupIndex = $currentGroupIndex;
                                                }
                                                
                                                echo '</div>';
                                                echo '</div>';
                                            }
                                        } else {
                                            // Layout tiêu chuẩn
                                            foreach ($rows as $row) {
                                            $isVipRow = in_array($row, $vip_rows);
                                            $isCoupleRow = in_array($row, $couple_rows);
                                            
                                            echo '<div class="seat-row ' . ($isCoupleRow ? 'couple-seat-row' : '') . ($isVipRow ? ' vip-row' : '') . '">';
                                            echo '<span class="row-label">' . $row . '</span>';
                                            echo '<div class="seats-in-row">';
                                            
                                            // Nếu là hàng ghế đôi
                                            if ($isCoupleRow) {
                                                // Tạo ghế đôi từ các ghế liên tiếp
                                                for ($i = 0; $i < count($cols); $i += 2) {
                                                    if ($i + 1 < count($cols)) {
                                                        $col1 = $cols[$i];
                                                        $col2 = $cols[$i + 1];
                                                        $seat1 = $row . $col1;
                                                        $seat2 = $row . $col2;
                                                        $isBooked1 = in_array($seat1, $bookedSeats ?? []);
                                                        $isBooked2 = in_array($seat2, $bookedSeats ?? []);
                                                        $isReserved1 = in_array($seat1, $reservedSeats ?? []);
                                                        $isReserved2 = in_array($seat2, $reservedSeats ?? []);
                                                        $isBooked = $isBooked1 || $isBooked2;
                                                        $isReserved = $isReserved1 || $isReserved2;
                                                        
                                                        $seatClass = 'available';
                                                        if ($isBooked) {
                                                            $seatClass = 'booked';
                                                        } elseif ($isReserved) {
                                                            $seatClass = 'reserved';
                                                        }
                                                        
                                                        echo '<label class="seat-label couple-seat ' . $seatClass . '" data-seat="' . $seat1 . '" title="Ghế đôi ' . $col1 . '-' . $col2 . '">';
                                                        if (!$isBooked && !$isReserved) {
                                                            echo '<input type="checkbox" name="seats[]" value="' . $seat1 . '" class="seat-checkbox couple-seat-checkbox" data-couple-seat="' . $seat2 . '">';
                                                            echo '<input type="checkbox" name="seats[]" value="' . $seat2 . '" class="seat-checkbox couple-seat-checkbox" data-couple-seat="' . $seat1 . '">';
                                                        }
                                                        echo '<span class="seat-number">' . $col1 . '-' . $col2 . '</span>';
                                                        echo '<span class="couple-icon"><i class="fas fa-heart"></i></span>';
                                                        echo '</label>';
                                                    }
                                                }
                                            } else {
                                                // Các hàng ghế đơn
                                                foreach ($cols as $col) {
                                                    $seat = $row . $col;
                                                    $isBooked = in_array($seat, $bookedSeats ?? []);
                                                    $isReserved = in_array($seat, $reservedSeats ?? []);
                                                    
                                                    $seatClass = 'available';
                                                    if ($isBooked) {
                                                        $seatClass = 'booked';
                                                    } elseif ($isReserved) {
                                                        $seatClass = 'reserved';
                                                    }
                                                    
                                                    if ($isVipRow) {
                                                        $seatClass .= ' vip-seat';
                                                    }
                                                    
                                                    echo '<label class="seat-label ' . $seatClass . '" data-seat="' . $seat . '" data-seat-type="' . ($isVipRow ? 'vip' : 'normal') . '">';
                                                    if (!$isBooked && !$isReserved) {
                                                        echo '<input type="checkbox" name="seats[]" value="' . $seat . '" class="seat-checkbox" data-seat-type="' . ($isVipRow ? 'vip' : 'normal') . '">';
                                                    }
                                                    echo '<span class="seat-number">' . $col . '</span>';
                                                    if ($isVipRow) {
                                                        echo '<span class="seat-icon vip-icon" title="Ghế VIP"><i class="fas fa-crown"></i></span>';
                                                    } else {
                                                        echo '<span class="seat-icon normal-icon" title="Ghế thường"><i class="fas fa-chair"></i></span>';
                                                    }
                                                    echo '</label>';
                                                }
                                            }
                                            
                                            echo '</div>';
                                            echo '</div>';
                                            }
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
                                            <span class="legend-seat vip-seat" aria-label="Ghế VIP"></span>
                                            <span>Ghế VIP <i class="fas fa-crown" style="color: #ffd700; margin-left: 5px;"></i></span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat available" aria-label="Ghế thường"></span>
                                            <span>Ghế thường <i class="fas fa-chair" style="color: #666; margin-left: 5px;"></i></span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat couple-seat" aria-label="Ghế đôi"></span>
                                            <span>Ghế đôi</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Food Items / Combo Section -->
                                    <?php if (!empty($foodItems)): ?>
                                    <div class="food-items-section mb-4">
                                        <button type="button" class="btn-food-toggle booking-label" id="foodToggleBtn" style="background: none; border: none; padding: 0; cursor: pointer; text-align: left; width: 100%;">
                                            <i class="fas fa-utensils me-2"></i>Combo & Đồ ăn <i class="fas fa-chevron-down ms-2" id="foodToggleIcon"></i>
                                        </button>
                                        <div class="food-items-grid" id="foodItemsGrid" style="display: none; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                                            <?php foreach ($foodItems as $item): ?>
                                            <div class="food-item-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: white;">
                                                <div class="food-item-header" style="margin-bottom: 10px;">
                                                    <h5 style="margin: 0; font-size: 16px; color: #333;"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                    <?php if ($item['description']): ?>
                                                    <small class="text-muted" style="font-size: 12px;"><?php echo htmlspecialchars($item['description']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="food-item-price" style="font-size: 18px; font-weight: bold; color: #e50914; margin-bottom: 10px;">
                                                    <?php echo number_format($item['price']); ?>₫
                                                </div>
                                                <div class="food-item-quantity">
                                                    <label style="font-size: 14px; margin-bottom: 5px; display: block;">Số lượng:</label>
                                                    <input type="number" 
                                                           name="food_items[<?php echo $item['id']; ?>]" 
                                                           value="0" 
                                                           min="0" 
                                                           max="10"
                                                           class="food-quantity-input form-control"
                                                           data-price="<?php echo $item['price']; ?>"
                                                           data-item-id="<?php echo $item['id']; ?>"
                                                           style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 4px;">
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
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
    // Tự động điều chỉnh kích thước ghế dựa trên số lượng
    function adjustSeatSize() {
        const seatMapContainer = document.querySelector('.seat-map-container');
        if (!seatMapContainer) return;
        
        const seatsPerRow = parseInt(seatMapContainer.getAttribute('data-seats-per-row')) || 0;
        if (seatsPerRow === 0) return;
        
        // Lấy chiều rộng container (trừ padding và row-label)
        const containerWidth = seatMapContainer.offsetWidth - 60; // 60px cho padding và row-label
        const gap = 0.4; // 0.4rem gap giữa các ghế
        const separatorWidth = 1.5; // 1.5rem cho mỗi separator
        
        // Đếm số separators (ước tính dựa trên số nhóm)
        const firstRow = seatMapContainer.querySelector('.seat-row');
        if (firstRow) {
            const separators = firstRow.querySelectorAll('.seat-group-separator');
            const separatorCount = separators.length;
            const seatCount = firstRow.querySelectorAll('.seat-label').length;
            
            // Tính toán kích thước ghế
            const totalGapWidth = (seatCount - 1) * gap * 16; // Convert rem to px (1rem = 16px)
            const totalSeparatorWidth = separatorCount * separatorWidth * 16;
            const availableWidth = containerWidth - totalGapWidth - totalSeparatorWidth;
            const seatSize = Math.max(14, Math.min(26, availableWidth / seatCount));
            
            // Áp dụng kích thước
            const seatLabels = seatMapContainer.querySelectorAll('.seat-label');
            seatLabels.forEach(label => {
                label.style.width = seatSize + 'px';
                label.style.height = seatSize + 'px';
                label.style.minWidth = Math.max(14, seatSize * 0.7) + 'px';
                label.style.minHeight = Math.max(14, seatSize * 0.7) + 'px';
            });
            
            // Điều chỉnh font size của số ghế
            const seatNumbers = seatMapContainer.querySelectorAll('.seat-number');
            const fontSize = Math.max(8, Math.min(11, seatSize * 0.4));
            seatNumbers.forEach(num => {
                num.style.fontSize = fontSize + 'px';
            });
            
            // Điều chỉnh icon size
            const seatIcons = seatMapContainer.querySelectorAll('.seat-icon');
            const iconSize = Math.max(6, Math.min(9, seatSize * 0.3));
            seatIcons.forEach(icon => {
                icon.style.fontSize = iconSize + 'px';
            });
        }
    }
    
    // Gọi hàm điều chỉnh khi DOM sẵn sàng và khi resize
    setTimeout(adjustSeatSize, 100);
    window.addEventListener('resize', adjustSeatSize);
    
    // Khôi phục scroll position sau khi reload
    const savedScrollPos = sessionStorage.getItem('bookingScrollPos');
    if (savedScrollPos) {
        window.scrollTo(0, parseInt(savedScrollPos));
        sessionStorage.removeItem('bookingScrollPos');
    }
    
    // Timer system - Global để có thể sử dụng ở mọi nơi
    // Chỉ có 1 timer duy nhất khi chọn showtime, không reset khi chọn ghế
    let showtimeTimer = null;
    let showtimeStartTime = Date.now();
    const SHOWTIME_DURATION = 10 * 60 * 1000; // 10 minutes
    
    // Hàm attachCheckboxListeners để đảm bảo checkbox có thể click
    function attachCheckboxListeners() {
        const allCheckboxes = document.querySelectorAll('.seat-checkbox');
        allCheckboxes.forEach(function(checkbox) {
            // Đảm bảo checkbox có thể click được
            checkbox.style.pointerEvents = 'auto';
            checkbox.style.cursor = 'pointer';
            
            // Thêm click listener trực tiếp
            checkbox.addEventListener('click', function(e) {
                // Cho phép checkbox tự xử lý click
                e.stopPropagation();
            }, true);
        });
    }
    
    // Gọi attachCheckboxListeners để đảm bảo checkbox có thể click
    attachCheckboxListeners();
    
    // Kiểm tra xem có showtime được chọn từ URL không
    const urlParams = new URLSearchParams(window.location.search);
    const showtimeIdFromUrl = urlParams.get('showtime_id');
    console.log('Showtime ID from URL:', showtimeIdFromUrl);
    
    if (showtimeIdFromUrl) {
        // Lưu showtime_id vào sessionStorage
        sessionStorage.setItem('selectedShowtimeId', showtimeIdFromUrl);
        
        // Kiểm tra xem có thời gian bắt đầu đã lưu không
        const savedStartTime = sessionStorage.getItem('showtimeStartTime');
        const savedShowtimeId = sessionStorage.getItem('selectedShowtimeId');
        
        // Nếu showtime_id thay đổi hoặc chưa có timer, tạo mới
        if (!savedStartTime || savedShowtimeId !== showtimeIdFromUrl) {
            showtimeStartTime = Date.now();
            sessionStorage.setItem('showtimeStartTime', showtimeStartTime.toString());
            console.log('Created new timer start time for showtime:', showtimeIdFromUrl);
        } else {
            showtimeStartTime = parseInt(savedStartTime);
            console.log('Using existing timer for showtime:', showtimeIdFromUrl);
        }
        
        // Đợi DOM load xong rồi khởi động timer NGAY
        setTimeout(function() {
            const timerElement = document.getElementById('reservation-timer');
            if (timerElement) {
                console.log('Timer element found, starting timer...');
                startShowtimeTimer();
            } else {
                console.error('Timer element not found in DOM! Retrying...');
                // Thử lại sau 500ms
                setTimeout(function() {
                    startShowtimeTimer();
                }, 500);
            }
        }, 100);
    }
    
    const checkboxes = document.querySelectorAll('.seat-checkbox');
    const totalAmountSpan = document.getElementById('total-amount');
    const totalSeatsSpan = document.getElementById('total-seats');
    const submitBtn = document.getElementById('submit-btn');
    const pricePerSeat = <?php echo (isset($showtime) && $showtime && isset($showtime['price'])) ? (int)$showtime['price'] : 0; ?>;
    
    // Seat layout config from PHP
    const seatLayout = <?php 
        if (isset($seatLayout) && $seatLayout !== null) {
            echo json_encode($seatLayout, JSON_HEX_APOS | JSON_HEX_QUOT);
        } else {
            echo 'null';
        }
    ?>;
    const normalPrice = (seatLayout && seatLayout.normal_price) ? seatLayout.normal_price : pricePerSeat;
    const vipPrice = (seatLayout && seatLayout.vip_price) ? seatLayout.vip_price : (pricePerSeat * 1.5);
    const couplePrice = (seatLayout && seatLayout.couple_price) ? seatLayout.couple_price : (pricePerSeat * 2);
    
    // Timer variables đã được khai báo ở trên
    
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
    
    // Sử dụng event delegation để xử lý tất cả checkbox (kể cả được thêm sau)
    const seatMapContainer = document.querySelector('.seat-map-container');
    if (seatMapContainer) {
        console.log('Seat map container found, attaching event listeners...');
        
        // Xử lý click vào label để trigger checkbox
        seatMapContainer.addEventListener('click', function(e) {
            // Nếu click trực tiếp vào checkbox, để nó tự xử lý
            if (e.target.type === 'checkbox' && e.target.classList.contains('seat-checkbox')) {
                return; // Checkbox tự xử lý
            }
            
            // Tìm label gần nhất
            const label = e.target.closest('.seat-label');
            if (!label) return;
            
            // Bỏ qua nếu ghế đã được đặt
            if (label.classList.contains('booked') || label.classList.contains('reserved')) {
                return;
            }
            
            // Tìm checkbox trong label
            const checkbox = label.querySelector('.seat-checkbox');
            if (!checkbox || checkbox.disabled) return;
            
            // Ngăn chặn default behavior
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle checkbox
            checkbox.checked = !checkbox.checked;
            
            // Trigger change event
            const changeEvent = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvent);
        });
        
        // Xử lý change event để validate sau khi checkbox được checked/unchecked
        seatMapContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('seat-checkbox')) {
                const checkbox = e.target;
                console.log('Checkbox changed:', checkbox.value, 'checked:', checkbox.checked);
                
                // Nếu đang check
                if (checkbox.checked) {
                    const selectedSeats = getSelectedSeats();
                    console.log('Selected seats:', selectedSeats);
                    
                    // Kiểm tra giới hạn 8 vé
                    if (selectedSeats.length > 8) {
                        checkbox.checked = false;
                        alert('Bạn chỉ có thể đặt tối đa 8 vé một lần!');
                        updateSelection();
                        return;
                    }
                    
                    // Validate seat selection rules
                    const validationError = validateSeatSelection(selectedSeats);
                    if (validationError) {
                        checkbox.checked = false;
                        alert(validationError);
                        updateSelection();
                        return;
                    }
                }
                
                // Xử lý ghế đôi: khi chọn 1 ghế trong cặp thì tự động chọn ghế còn lại
                if (checkbox.classList.contains('couple-seat-checkbox')) {
                    const coupleSeatId = checkbox.getAttribute('data-couple-seat');
                    const coupleCheckbox = document.querySelector('input[value="' + coupleSeatId + '"].couple-seat-checkbox');
                    if (coupleCheckbox && checkbox.checked) {
                        coupleCheckbox.checked = true;
                        // Trigger change event cho ghế đôi
                        const changeEvent = new Event('change', { bubbles: true });
                        coupleCheckbox.dispatchEvent(changeEvent);
                    } else if (coupleCheckbox && !checkbox.checked) {
                        coupleCheckbox.checked = false;
                        const changeEvent = new Event('change', { bubbles: true });
                        coupleCheckbox.dispatchEvent(changeEvent);
                    }
                }
                
                updateSelection();
            }
        });
        
        // Thêm event listener trực tiếp cho checkbox để đảm bảo change event được trigger
        seatMapContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('seat-checkbox')) {
                // Change event đã được trigger tự động, không cần làm gì thêm
            }
        });
        
        // Gọi lại sau khi DOM được cập nhật
        setTimeout(attachCheckboxListeners, 100);
        setTimeout(attachCheckboxListeners, 500);
        setTimeout(attachCheckboxListeners, 1000);
        
        // Debug: Log số lượng checkbox
        setTimeout(function() {
            const allCheckboxes = document.querySelectorAll('.seat-checkbox');
            console.log('Total checkboxes found:', allCheckboxes.length);
            var availableCheckboxes = document.querySelectorAll('.seat-checkbox:not(:disabled)');
            console.log('Available checkboxes:', availableCheckboxes.length);
            
            // Kiểm tra pointer-events của checkbox
            allCheckboxes.forEach(function(cb) {
                const style = window.getComputedStyle(cb);
                console.log('Checkbox pointer-events:', style.pointerEvents, 'z-index:', style.zIndex);
            });
        }, 1000);
        
    } else {
        console.error('Seat map container not found!');
    }
    
    // Food items quantity change handler
    document.querySelectorAll('.food-quantity-input').forEach(function(input) {
        input.addEventListener('change', function() {
            updateSelection();
        });
    });
    
    // Helper functions
    function getSelectedSeats() {
        const allCheckboxes = document.querySelectorAll('.seat-checkbox:checked');
        var values = Array.from(allCheckboxes).map(function(cb) { return cb.value; });
        var uniqueValues = [];
        for (var i = 0; i < values.length; i++) {
            if (uniqueValues.indexOf(values[i]) === -1) {
                uniqueValues.push(values[i]);
            }
        }
        return uniqueValues;
    }
    
    function getSeatPrice(seat) {
        const label = document.querySelector('.seat-label[data-seat="' + seat + '"]');
        if (!label) return normalPrice;
        
        if (label.classList.contains('couple-seat')) {
            return couplePrice;
        } else if (label.classList.contains('vip-seat')) {
            return vipPrice;
        }
        return normalPrice;
    }
    
    function validateSeatSelection(seats) {
        if (seats.length <= 1) return null;
        
        // Group seats by row
        const seatsByRow = {};
        seats.forEach(function(seat) {
            const row = seat.charAt(0);
            const col = parseInt(seat.substring(1));
            if (!seatsByRow[row]) {
                seatsByRow[row] = [];
            }
            seatsByRow[row].push(col);
        });
        
        // Validate each row
        for (const row in seatsByRow) {
            const cols = seatsByRow[row].sort((a, b) => a - b);
            
            // Nếu chỉ có 1 ghế trong hàng, không cần validate
            if (cols.length <= 1) continue;
            
            // Khi chọn từ 2 ghế trở lên, các ghế phải liền kề nhau
            // Không được bỏ trống ghế ở giữa, đặc biệt là ghế ngoài cùng bên trái
            for (let i = 0; i < cols.length - 1; i++) {
                const gap = cols[i + 1] - cols[i];
                if (gap > 1) {
                    // Tìm ghế bị bỏ trống
                    const missingSeats = [];
                    for (let j = cols[i] + 1; j < cols[i + 1]; j++) {
                        missingSeats.push(row + j);
                    }
                    
                    // Kiểm tra xem có phải ghế ngoài cùng bên trái bị bỏ trống không
                    if (i === 0 && cols[0] > 1) {
                        // Có ghế bên trái ghế đầu tiên bị bỏ trống
                        return 'Khi chọn nhiều ghế, không được bỏ trống ghế ngoài cùng bên trái! Các ghế phải liền kề nhau. Vui lòng chọn các ghế liền kề từ đầu hàng.';
                    }
                    
                    // Có gap ở giữa các ghế
                    return 'Không được bỏ trống ghế ở giữa! Các ghế phải liền kề nhau. Vui lòng chọn các ghế liền kề.';
                }
            }
            
            // Kiểm tra thêm: Nếu chọn nhiều ghế, không được bỏ trống ghế ngoài cùng bên trái
            // (Nghĩa là nếu chọn ghế 2, 3, 4 thì OK, nhưng nếu chọn 1, 3, 4 thì không OK vì bỏ trống ghế 2)
            // Logic này đã được xử lý ở trên khi kiểm tra gap, nhưng cần làm rõ thông báo
        }
        
        return null;
    }
    
    // Attach events cho checkbox hiện có (cho keyboard support)
    checkboxes.forEach(function(checkbox) {
        const label = checkbox.closest('.seat-label');
        if (label && !label.classList.contains('booked')) {
            label.setAttribute('tabindex', '0');
            label.setAttribute('role', 'checkbox');
            label.setAttribute('aria-checked', checkbox.checked);
            
            label.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }
    });
    
    // Gọi updateSelection lần đầu để cập nhật trạng thái ban đầu
    updateSelection();
    
    function updateSelection() {
        // Lấy lại tất cả checkbox (có thể có checkbox mới được thêm vào)
        const allCheckboxes = document.querySelectorAll('.seat-checkbox');
        var checkedBoxes = Array.from(allCheckboxes).filter(function(cb) { return cb.checked; });
        var selected = checkedBoxes.map(function(cb) { return cb.value; });
        
        // Loại bỏ duplicate (cho ghế đôi có thể có 2 checkbox cùng value)
        var uniqueSelected = [];
        for (var i = 0; i < selected.length; i++) {
            if (uniqueSelected.indexOf(selected[i]) === -1) {
                uniqueSelected.push(selected[i]);
            }
        }
        
        const emailContainer = document.getElementById('email-container');
        const emailInput = document.getElementById('customer_email');
        
        // Update visual
        allCheckboxes.forEach(function(cb) {
            const label = cb.closest('.seat-label');
            if (label) {
                if (cb.checked) {
                    label.classList.add('selected');
                    label.classList.remove('available');
                    label.setAttribute('aria-checked', 'true');
                    // Đảm bảo giữ class vip-seat nếu có
                    if (label.classList.contains('vip-seat')) {
                        // vip-seat class đã có, không cần làm gì
                    }
                } else {
                    // Chỉ xóa selected nếu cả 2 ghế trong cặp đều không được chọn (cho ghế đôi)
                    if (label.classList.contains('couple-seat')) {
                        const coupleSeatId = cb.getAttribute('data-couple-seat');
                        const coupleCheckbox = document.querySelector('input[value="' + coupleSeatId + '"].couple-seat-checkbox');
                        if (coupleCheckbox && !coupleCheckbox.checked) {
                            label.classList.remove('selected');
                            if (!label.classList.contains('booked') && !label.classList.contains('reserved')) {
                                label.classList.add('available');
                            }
                            label.setAttribute('aria-checked', 'false');
                        }
                    } else {
                        label.classList.remove('selected');
                        if (!label.classList.contains('booked') && !label.classList.contains('reserved')) {
                            label.classList.add('available');
                            // Đảm bảo giữ class vip-seat nếu có
                            if (label.classList.contains('vip-seat')) {
                                // vip-seat class đã có, không cần làm gì
                            }
                        }
                        label.setAttribute('aria-checked', 'false');
                    }
                }
            }
        });
        
        // Calculate total price
        let seatTotal = 0;
        uniqueSelected.forEach(function(seat) {
            seatTotal += getSeatPrice(seat);
        });
        
        // Calculate food items total
        let foodTotal = 0;
        document.querySelectorAll('.food-quantity-input').forEach(function(input) {
            const quantity = parseInt(input.value) || 0;
            const price = parseFloat(input.getAttribute('data-price')) || 0;
            foodTotal += quantity * price;
        });
        
        const grandTotal = seatTotal + foodTotal;
        
        if (uniqueSelected.length > 0) {
            totalAmountSpan.textContent = grandTotal.toLocaleString('vi-VN') + '₫';
            totalAmountSpan.setAttribute('aria-label', 'Tổng tiền ' + grandTotal.toLocaleString('vi-VN') + ' đồng');
            totalSeatsSpan.textContent = uniqueSelected.length + ' ghế' + (foodTotal > 0 ? ' + đồ ăn' : '');
            submitBtn.disabled = false;
            submitBtn.setAttribute('aria-label', 'Xác nhận đặt ' + uniqueSelected.length + ' vé');
            
            // Hiển thị trường email
            if (emailContainer) {
                emailContainer.style.display = 'block';
            }
            
            // Không start timer mới khi chọn ghế, chỉ dùng timer từ khi chọn showtime
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
        
        // Return selected seats for use in override function
        return uniqueSelected;
    }
    
    function startShowtimeTimer() {
        const timerElement = document.getElementById('reservation-timer');
        const countdownElement = document.getElementById('timer-countdown');
        
        console.log('=== Starting showtime timer ===');
        console.log('Timer element:', timerElement);
        console.log('Countdown element:', countdownElement);
        
        if (!timerElement || !countdownElement) {
            console.error('Timer elements not found!');
            console.log('Looking for #reservation-timer and #timer-countdown');
            return;
        }
        
        // Lấy showtime_id hiện tại từ URL
        const urlParams = new URLSearchParams(window.location.search);
        const showtimeIdFromUrl = urlParams.get('showtime_id');
        const currentShowtimeId = showtimeIdFromUrl || '';
        
        // Kiểm tra xem có thời gian bắt đầu đã lưu trong sessionStorage không
        const savedStartTime = sessionStorage.getItem('showtimeStartTime');
        const savedShowtimeId = sessionStorage.getItem('selectedShowtimeId');
        
        // Chỉ tạo timer mới nếu:
        // 1. Chưa có timer nào đang chạy
        // 2. Hoặc showtime_id đã thay đổi (chọn showtime mới)
        if (savedStartTime && savedShowtimeId === currentShowtimeId && showtimeTimer) {
            // Timer đã tồn tại và đang chạy cho cùng showtime, không reset
            showtimeStartTime = parseInt(savedStartTime);
            console.log('Using existing timer for showtime:', currentShowtimeId);
        } else {
            // Tạo timer mới hoặc reset khi chọn showtime mới
            showtimeStartTime = Date.now();
            sessionStorage.setItem('showtimeStartTime', showtimeStartTime.toString());
            sessionStorage.setItem('selectedShowtimeId', currentShowtimeId || '');
            console.log('Starting new timer for showtime:', currentShowtimeId);
        }
        
        // Hiển thị timer element
        timerElement.style.display = 'block';
        console.log('Timer element displayed');
        
        // Clear timer cũ nếu có
        if (showtimeTimer) {
            clearInterval(showtimeTimer);
        }
        
        // Cập nhật ngay lập tức
        const elapsed = Date.now() - showtimeStartTime;
        const remaining = SHOWTIME_DURATION - elapsed;
        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);
        countdownElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        console.log('Initial countdown:', minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
        
        showtimeTimer = setInterval(function() {
            const elapsed = Date.now() - showtimeStartTime;
            const remaining = SHOWTIME_DURATION - elapsed;
            
            if (remaining <= 0) {
                clearInterval(showtimeTimer);
                showtimeTimer = null;
                alert('Thời gian đặt vé đã hết! Vui lòng chọn lại suất chiếu.');
                // Xóa sessionStorage
                sessionStorage.removeItem('selectedShowtimeId');
                sessionStorage.removeItem('showtimeStartTime');
                window.location.reload();
                return;
            }
            
            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);
            countdownElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        }, 1000);
        
        console.log('Timer started successfully');
    }
    
    // Thêm event listener cho các time button để bắt đầu timer khi click
    document.querySelectorAll('.time-btn').forEach(function(timeBtn) {
        timeBtn.addEventListener('click', function(e) {
            // Lưu scroll position trước khi chuyển trang
            sessionStorage.setItem('bookingScrollPos', window.pageYOffset || document.documentElement.scrollTop);
            
            // Lấy showtime_id từ URL
            const url = new URL(this.href);
            const showtimeIdParam = url.searchParams.get('showtime_id');
            
            if (showtimeIdParam) {
                // Kiểm tra xem có phải showtime mới không
                const savedShowtimeId = sessionStorage.getItem('selectedShowtimeId');
                if (savedShowtimeId && savedShowtimeId != showtimeIdParam) {
                    // Showtime đã thay đổi, reset timer
                    console.log('Showtime changed from', savedShowtimeId, 'to', showtimeIdParam, '- Resetting timer');
                    sessionStorage.removeItem('showtimeStartTime');
                }
                
                // Lưu showtime_id và thời gian bắt đầu mới vào sessionStorage
                sessionStorage.setItem('selectedShowtimeId', showtimeIdParam);
                const startTime = Date.now();
                sessionStorage.setItem('showtimeStartTime', startTime.toString());
                console.log('Timer will start for showtime:', showtimeIdParam);
                
                // Hiển thị timer ngay lập tức (không cần đợi reload)
                const timerElement = document.getElementById('reservation-timer');
                const countdownElement = document.getElementById('timer-countdown');
                
                if (timerElement && countdownElement) {
                    // Hiển thị timer element ngay
                    timerElement.style.display = 'block';
                    
                    // Khởi động timer ngay lập tức với thời gian mới
                    showtimeStartTime = startTime;
                    
                    // Clear timer cũ nếu có
                    if (showtimeTimer) {
                        clearInterval(showtimeTimer);
                        showtimeTimer = null;
                    }
                    
                    // Cập nhật ngay lập tức
                    const elapsed = Date.now() - showtimeStartTime;
                    const remaining = SHOWTIME_DURATION - elapsed;
                    const minutes = Math.floor(remaining / 60000);
                    const seconds = Math.floor((remaining % 60000) / 1000);
                    countdownElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                    
                    // Bắt đầu đếm ngược
                    showtimeTimer = setInterval(function() {
                        const elapsed = Date.now() - showtimeStartTime;
                        const remaining = SHOWTIME_DURATION - elapsed;
                        
                        if (remaining <= 0) {
                            clearInterval(showtimeTimer);
                            showtimeTimer = null;
                            alert('Thời gian đặt vé đã hết! Vui lòng chọn lại suất chiếu.');
                            sessionStorage.removeItem('selectedShowtimeId');
                            sessionStorage.removeItem('showtimeStartTime');
                            window.location.reload();
                            return;
                        }
                        
                        const minutes = Math.floor(remaining / 60000);
                        const seconds = Math.floor((remaining % 60000) / 1000);
                        countdownElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                    }, 1000);
                    
                    console.log('Timer started immediately for showtime:', showtimeIdParam);
                } else {
                    console.log('Timer elements not found, will start after page reload');
                }
            }
        });
    });
    
    // Real-time seat reservation system
    <?php if ($selected_showtime_id): ?>
    const showtimeId = <?php echo $selected_showtime_id; ?>;
    let selectedSeats = [];
    let pollingInterval = null;
    let reservationTimeout = null;
    
    // Lưu showtime_id hiện tại để so sánh
    let currentShowtimeId = null;
    
    // Start timer when page loads with showtime selected
    console.log('=== Showtime ID from PHP:', showtimeId, '===');
    if (showtimeId) {
        // Kiểm tra xem có phải showtime mới không
        const savedShowtimeId = sessionStorage.getItem('selectedShowtimeId');
        if (savedShowtimeId && savedShowtimeId != showtimeId) {
            // Showtime đã thay đổi, reset timer
            console.log('Showtime changed from', savedShowtimeId, 'to', showtimeId);
            sessionStorage.removeItem('showtimeStartTime');
            sessionStorage.setItem('showtimeStartTime', Date.now().toString());
        }
        
        currentShowtimeId = showtimeId;
        sessionStorage.setItem('selectedShowtimeId', showtimeId.toString());
        
        // Đợi DOM load xong rồi khởi động timer
        setTimeout(function() {
            console.log('Attempting to start timer for showtime:', showtimeId);
            const timerEl = document.getElementById('reservation-timer');
            const countdownEl = document.getElementById('timer-countdown');
            console.log('Timer element exists:', !!timerEl);
            console.log('Countdown element exists:', !!countdownEl);
            
            if (timerEl && countdownEl) {
                startShowtimeTimer();
            } else {
                console.error('Timer elements not found! Retrying...');
                // Thử lại sau 500ms
                setTimeout(function() {
                    startShowtimeTimer();
                }, 500);
            }
        }, 300);
    }
    
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
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                // Extend reservation every 9 minutes (before 10 minutes expire)
                reservationTimeout = setInterval(function() {
                    extendReservations(seats);
                }, 9 * 60 * 1000);
            }
        })
        .catch(function(error) { console.error('Error reserving seats:', error); });
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
        .catch(function(error) { console.error('Error releasing seats:', error); });
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
        .catch(function(error) { console.error('Error extending reservations:', error); });
    }
    
    // Check seat status real-time
    function checkSeatStatus() {
        fetch('?route=booking/get-seat-status-api&showtime_id=' + showtimeId)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    updateSeatStatus(data.booked_seats, data.reserved_seats);
                }
            })
            .catch(function(error) { console.error('Error checking seat status:', error); });
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
            // Gọi function gốc để cập nhật UI và lấy danh sách ghế đã chọn
            const newSelected = originalUpdateSelection();
            
            // Release seats that are no longer selected
            var toRelease = selectedSeats.filter(function(seat) { return newSelected.indexOf(seat) === -1; });
            if (toRelease.length > 0) {
                releaseSeats(toRelease);
            }
            
            // Reserve newly selected seats
            var toReserve = newSelected.filter(function(seat) { return selectedSeats.indexOf(seat) === -1; });
            if (toReserve.length > 0) {
                reserveSeats(toReserve);
            }
            
            selectedSeats = newSelected;
        };
    }
    <?php endif; ?>
    
    // Function validate và submit form
    function validateBookingForm(e) {
        // Lấy các ghế đã chọn (loại bỏ duplicate)
        const selectedCheckboxes = Array.from(document.querySelectorAll('.seat-checkbox:checked'));
        var seatValues = selectedCheckboxes.map(function(cb) { return cb.value; });
        var selectedSeatValues = [];
        for (var i = 0; i < seatValues.length; i++) {
            if (selectedSeatValues.indexOf(seatValues[i]) === -1) {
                selectedSeatValues.push(seatValues[i]);
            }
        }
        
        // Debug log
        console.log('Form submit - Selected seats:', selectedSeatValues);
        console.log('Form submit - Checkboxes found:', selectedCheckboxes.length);
        var allFormCheckboxes = document.querySelectorAll('#booking-form input[name="seats[]"]');
        console.log('Form submit - All checkboxes in form:', allFormCheckboxes ? allFormCheckboxes.length : 0);
        
        // Validate: phải có ít nhất 1 ghế được chọn
        if (selectedSeatValues.length === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một ghế!');
            console.error('Validation failed: No seats selected');
            return false;
        }
        
        // Validate: Giới hạn 8 vé
        if (selectedSeatValues.length > 8) {
            e.preventDefault();
            alert('Bạn chỉ có thể đặt tối đa 8 vé một lần!');
            return false;
        }
        
        // Validate seat selection rules
        const validationError = validateSeatSelection(selectedSeatValues);
        if (validationError) {
            e.preventDefault();
            alert(validationError);
            return false;
        }
        
        // Validate: phải có email
        const emailInput = document.getElementById('customer_email');
        if (emailInput && !emailInput.value.trim()) {
            e.preventDefault();
            alert('Vui lòng nhập email để nhận vé!');
            emailInput.focus();
            console.error('Validation failed: No email');
            return false;
        }
        
        // Đảm bảo tất cả checkbox được checked và có name="seats[]"
        selectedCheckboxes.forEach(function(checkbox) {
            if (!checkbox.checked) {
                checkbox.checked = true;
            }
            if (!checkbox.name || checkbox.name !== 'seats[]') {
                checkbox.name = 'seats[]';
            }
            // Đảm bảo checkbox không bị disabled
            checkbox.disabled = false;
        });
        
        // Verify lại trước khi submit - lấy từ form
        const form = document.getElementById('booking-form');
        const formSeats = Array.from(form.querySelectorAll('input[name="seats[]"]:checked'));
        var seatValues = formSeats.map(function(cb) { return cb.value; });
        var finalSeats = [];
        for (var i = 0; i < seatValues.length; i++) {
            if (finalSeats.indexOf(seatValues[i]) === -1) {
                finalSeats.push(seatValues[i]);
            }
        }
        console.log('Final seats to submit (from form):', finalSeats);
        var formDataObj = new FormData(form);
        console.log('Form data:', formDataObj.getAll('seats[]'));
        
        if (finalSeats.length === 0) {
            e.preventDefault();
            alert('Lỗi: Không thể xác định ghế đã chọn. Vui lòng thử lại!');
            console.error('Final validation failed: No seats found in form');
            return false;
        }
        
        // Disable submit button để tránh double submit
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
        }
        
        // Update total info để hiển thị "Đang xử lý"
        const totalSeatsSpan = document.getElementById('total-seats');
        if (totalSeatsSpan) {
            totalSeatsSpan.textContent = finalSeats.length + ' ghế - Đang xử lý...';
        }
        
        // Release reservations của ghế đã chọn (vì đã được đặt rồi)
        <?php if ($selected_showtime_id): ?>
        if (typeof releaseSeats === 'function') {
            releaseSeats(selectedSeatValues);
        }
        if (typeof reservationTimeout !== 'undefined' && reservationTimeout) {
            clearInterval(reservationTimeout);
            reservationTimeout = null;
        }
        // Stop polling vì đã đặt vé rồi
        if (typeof pollingInterval !== 'undefined' && pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
        <?php endif; ?>
        
        // KHÔNG remove checkbox hoặc thay đổi UI ở đây - để form submit với dữ liệu đúng
        // Form sẽ tự động submit và redirect, sau đó server sẽ xử lý
        
        console.log('Form submitting with seats:', finalSeats);
        console.log('Form action:', form.action);
        return true; // Cho phép form submit
    }
});

// Support Form Toggle
// Toggle Food Items Section
function toggleFoodItems() {
    const grid = document.getElementById('foodItemsGrid');
    const icon = document.getElementById('foodToggleIcon');
    
    if (grid && icon) {
        if (grid.style.display === 'none' || !grid.style.display) {
            grid.style.display = 'grid';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            grid.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }
}

// Attach event listener for food toggle
document.addEventListener('DOMContentLoaded', function() {
    const foodToggleBtn = document.getElementById('foodToggleBtn');
    if (foodToggleBtn) {
        foodToggleBtn.addEventListener('click', toggleFoodItems);
    }
});

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
            locationText.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>' + errorMessage + '</span>';
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
    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1')
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data && data.address) {
                const address = data.address;
                var addressString = '';
                
                if (address.road) addressString += address.road + ', ';
                if (address.suburb || address.village) addressString += (address.suburb || address.village) + ', ';
                if (address.city || address.town || address.county) addressString += (address.city || address.town || address.county) + ', ';
                if (address.state) addressString += address.state;
                
                if (addressString) {
                    const locationText = document.getElementById('location-text');
                    var cleanAddress = addressString.trim().replace(/,\s*$/, '');
                    locationText.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-2"></i>Vị trí: ' + cleanAddress + '</span>';
                }
            }
        })
        .catch(function(error) {
            console.log('Reverse geocoding failed:', error);
        });
}

// Sắp xếp rạp theo khoảng cách (nếu có tọa độ rạp)
function sortTheatersByDistance(userLat, userLng) {
    const theaters = document.querySelectorAll('.theater-btn');
    const theatersArray = Array.from(theaters);
    
    // Tính khoảng cách và sắp xếp
    theatersArray.forEach(function(theater) {
        const locationSpan = theater.querySelector('.theater-location');
        // Có thể thêm logic tính khoảng cách nếu có tọa độ rạp trong database
        // Hiện tại chỉ hiển thị thông tin
    });
}

</script>
