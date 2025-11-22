<?php
$current_page = 'booking';
$title = 'Chọn Ghế';
?>

<section class="section booking-section">
    <div class="container">
        <h1 class="page-title"><i class="fas fa-chair"></i> Chọn Ghế</h1>
        
        <div class="seat-selection">
            <div class="screen-info">
                <h3><?php echo htmlspecialchars($showtime['movie_title']); ?></h3>
                <p><?php echo htmlspecialchars($showtime['theater_name']); ?> - <?php echo htmlspecialchars($showtime['location']); ?></p>
                <p><?php echo date('d/m/Y', strtotime($showtime['show_date'])); ?> - <?php echo date('H:i', strtotime($showtime['show_time'])); ?></p>
                <p class="price-info">Giá vé: <?php echo number_format($showtime['price']); ?> đ/ghế</p>
            </div>
            
            <div class="screen-display">
                <div class="screen">MÀN HÌNH</div>
            </div>
            
            <form method="POST" action="http://localhost/DuAn1/?route=booking/process-booking" class="seat-form">
                <input type="hidden" name="showtime_id" value="<?php echo $showtime['id']; ?>">
                
                <div class="seat-map">
                    <?php
                    $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                    $cols = range(1, 10);
                    
                    foreach ($rows as $row) {
                        echo '<div class="seat-row">';
                        echo '<span class="row-label">' . $row . '</span>';
                        foreach ($cols as $col) {
                            $seat = $row . $col;
                            $isBooked = in_array($seat, $bookedSeats);
                            $seatClass = $isBooked ? 'booked' : 'available';
                            
                            echo '<label class="seat ' . $seatClass . '">';
                            if (!$isBooked) {
                                echo '<input type="checkbox" name="seats[]" value="' . $seat . '">';
                            }
                            echo '<span>' . $col . '</span>';
                            echo '</label>';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
                
                <div class="seat-legend">
                    <div class="legend-item">
                        <span class="seat available"></span>
                        <span>Ghế trống</span>
                    </div>
                    <div class="legend-item">
                        <span class="seat booked"></span>
                        <span>Ghế đã đặt</span>
                    </div>
                    <div class="legend-item">
                        <span class="seat selected"></span>
                        <span>Ghế đã chọn</span>
                    </div>
                </div>
                
                <div class="selected-seats-info">
                    <p>Ghế đã chọn: <span id="selected-seats">Chưa chọn</span></p>
                    <p>Tổng tiền: <span id="total-price">0</span> đ</p>
                </div>
                
                <div class="email-input-container" id="email-container" style="display: none;">
                    <div class="form-group">
                        <label for="customer_email" class="form-label">
                            <i class="fas fa-envelope"></i> Email nhận vé <span class="required">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="customer_email" 
                            name="customer_email" 
                            class="form-control" 
                            placeholder="Nhập email của bạn để nhận vé"
                            required
                        >
                        <small class="form-text">Vé và QR code sẽ được gửi đến email này sau khi thanh toán</small>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-large" id="submit-btn" disabled>Thanh toán</button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="seats[]"]');
    const selectedSeatsSpan = document.getElementById('selected-seats');
    const totalPriceSpan = document.getElementById('total-price');
    const submitBtn = document.getElementById('submit-btn');
    const pricePerSeat = <?php echo $showtime['price']; ?>;
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelection();
        });
    });
    
    function updateSelection() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        const emailContainer = document.getElementById('email-container');
        const emailInput = document.getElementById('customer_email');
        
        if (selected.length > 0) {
            selectedSeatsSpan.textContent = selected.join(', ');
            totalPriceSpan.textContent = (selected.length * pricePerSeat).toLocaleString('vi-VN');
            submitBtn.disabled = false;
            emailContainer.style.display = 'block';
            
            // Update visual
            checkboxes.forEach(cb => {
                const label = cb.closest('label');
                if (cb.checked) {
                    label.classList.add('selected');
                } else {
                    label.classList.remove('selected');
                }
            });
        } else {
            selectedSeatsSpan.textContent = 'Chưa chọn';
            totalPriceSpan.textContent = '0';
            submitBtn.disabled = true;
            emailContainer.style.display = 'none';
            emailInput.value = '';
            
            checkboxes.forEach(cb => {
                cb.closest('label').classList.remove('selected');
            });
        }
    }
});
</script>

