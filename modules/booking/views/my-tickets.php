<?php
$current_page = 'booking';
$title = 'Vé Của Tôi';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title"><i class="fas fa-ticket-alt"></i> Vé Của Tôi</h1>
        
        <?php if (empty($tickets)): ?>
            <div class="empty-state">
                <i class="fas fa-ticket-alt"></i>
                <p>Bạn chưa có vé nào.</p>
                <a href="?route=booking/index" class="btn btn-primary">Đặt vé ngay</a>
            </div>
        <?php else: ?>
            <?php
            // Nhóm vé theo booking_pending_id
            $groupedTickets = [];
            foreach ($tickets as $ticket) {
                $bookingId = $ticket['booking_pending_id'] ?? 'single_' . $ticket['id'];
                if (!isset($groupedTickets[$bookingId])) {
                    $groupedTickets[$bookingId] = [];
                }
                $groupedTickets[$bookingId][] = $ticket;
            }
            ?>
            <div class="tickets-list">
                <?php foreach ($groupedTickets as $bookingId => $bookingTickets): ?>
                    <?php 
                    $firstTicket = $bookingTickets[0];
                    $isPending = ($firstTicket['booking_type'] ?? 'completed') === 'pending';
                    $statusClass = $isPending ? 'pending' : strtolower(str_replace(' ', '-', $firstTicket['status']));
                    // Kiểm tra xem có booking_qr_code và booking_pending_id không
                    $hasBookingQR = !$isPending && 
                                    !empty($firstTicket['booking_qr_code']) && 
                                    !empty($firstTicket['booking_pending_id']) &&
                                    strpos($bookingId, 'single_') !== 0; // Không phải vé đơn lẻ
                    ?>
                    <div class="ticket-card <?php echo $isPending ? 'pending-booking' : ''; ?>" style="margin-bottom: 30px;">
                        <div class="ticket-header">
                            <h3><?php echo htmlspecialchars($firstTicket['movie_title']); ?></h3>
                            <span class="ticket-status <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($firstTicket['status']); ?>
                            </span>
                        </div>
                        <?php if ($isPending): ?>
                            <div class="pending-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Vé này sẽ tự động hủy sau 10 phút nếu không thanh toán</span>
                                <?php if (isset($firstTicket['expires_at'])): ?>
                                    <small class="expires-at">
                                        Hết hạn: <?php echo date('d/m/Y H:i', strtotime($firstTicket['expires_at'])); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="ticket-info">
                            <div class="ticket-info-grid">
                                <div class="info-item">
                                    <i class="fas fa-building"></i>
                                    <div class="info-content">
                                        <span class="info-label">Rạp</span>
                                        <span class="info-value"><?php echo htmlspecialchars($firstTicket['theater_name']); ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <div class="info-content">
                                        <span class="info-label">Ngày</span>
                                        <span class="info-value"><?php echo date('d/m/Y', strtotime($firstTicket['show_date'])); ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <div class="info-content">
                                        <span class="info-label">Giờ</span>
                                        <span class="info-value"><?php echo date('H:i', strtotime($firstTicket['show_time'])); ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-chair"></i>
                                    <div class="info-content">
                                        <span class="info-label">Ghế</span>
                                        <span class="info-value">
                                            <?php 
                                            $seats = array_map(function($t) { return $t['seat']; }, $bookingTickets);
                                            echo htmlspecialchars(implode(', ', $seats));
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="info-item highlight">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <div class="info-content">
                                        <span class="info-label">Tổng giá</span>
                                        <span class="info-value price"><?php 
                                            $totalPrice = array_sum(array_map(function($t) { return $t['price']; }, $bookingTickets));
                                            echo number_format($totalPrice); 
                                        ?> đ</span>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($isPending && isset($firstTicket['food_items']) && !empty($firstTicket['food_items'])): ?>
                                <p><i class="fas fa-utensils"></i> Combo & Đồ ăn: 
                                    <?php 
                                    $foodNames = [];
                                    foreach ($firstTicket['food_items'] as $food) {
                                        if (is_array($food) && isset($food['name'])) {
                                            $foodNames[] = htmlspecialchars($food['name']) . " x" . $food['quantity'];
                                        } elseif (is_numeric($food)) {
                                            $foodNames[] = "x" . $food;
                                        }
                                    }
                                    echo !empty($foodNames) ? implode(', ', $foodNames) : 'Không có';
                                    ?>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Hiển thị QR code cho booking (chỉ 1 lần) -->
                            <?php if ($hasBookingQR): ?>
                                <div class="qr-code-section">
                                    <div class="qr-code-wrapper">
                                        <div class="qr-code-header">
                                            <i class="fas fa-qrcode"></i>
                                            <span>Mã QR Code</span>
                                        </div>
                                        <div class="qr-code-container">
                                            <img src="?route=booking/showQRCode&booking_id=<?php echo $firstTicket['booking_pending_id']; ?>" 
                                                 alt="QR Code" 
                                                 class="qr-code-image">
                                        </div>
                                        <div class="qr-code-info">
                                            <p class="qr-code-label">Mã booking:</p>
                                            <p class="qr-code-value"><?php echo htmlspecialchars($firstTicket['booking_qr_code']); ?></p>
                                            <p class="qr-code-hint">
                                                <i class="fas fa-info-circle"></i>
                                                Quét mã QR để xem/tải tất cả vé trong booking
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($isPending): ?>
                                <div class="pending-actions">
                                    <a href="?route=booking/payment&txn_ref=<?php echo urlencode($firstTicket['vnp_txn_ref']); ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-credit-card"></i> Tiếp tục thanh toán
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.ticket-card.pending-booking {
    border: 2px solid #ffc107;
    background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
    position: relative;
}

.ticket-card.pending-booking::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ffc107, #ff9800);
}

.pending-warning {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 12px 15px;
    margin: 15px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #856404;
    flex-wrap: wrap;
}

.pending-warning i {
    color: #ffc107;
    font-size: 18px;
}

.pending-warning span {
    flex: 1;
    font-weight: 500;
}

.pending-warning .expires-at {
    color: #856404;
    font-size: 0.85rem;
    font-weight: 600;
    width: 100%;
    margin-top: 5px;
}

.ticket-status.pending {
    background: #ffc107 !important;
    color: #000 !important;
    font-weight: 600;
}

.pending-actions {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.pending-actions .btn {
    width: 100%;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.pending-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.ticket-card {
    transition: all 0.3s ease;
}

.ticket-card.pending-booking:hover {
    box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
    transform: translateY(-2px);
}

/* Ticket Info Grid Styles */
.ticket-info {
    margin-top: 1rem;
}

.ticket-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.info-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.info-item.highlight {
    background: linear-gradient(135deg, rgba(229, 9, 20, 0.2) 0%, rgba(229, 9, 20, 0.1) 100%);
    border-color: rgba(229, 9, 20, 0.3);
}

.info-item i {
    font-size: 20px;
    color: #e50914;
    margin-top: 2px;
    min-width: 24px;
}

.info-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.info-label {
    font-size: 11px;
    color: #b3b3b3;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.info-value {
    font-size: 14px;
    color: #ffffff;
    font-weight: 600;
}

.info-value.price {
    color: #e50914;
    font-size: 16px;
    font-weight: 700;
}

.ticket-card.pending-booking .info-item {
    background: rgba(255, 193, 7, 0.1);
    border-color: rgba(255, 193, 7, 0.2);
}

.ticket-card.pending-booking .info-item i {
    color: #ffc107;
}

.ticket-card.pending-booking .info-value {
    color: #333;
}

.ticket-card.pending-booking .info-label {
    color: #856404;
}

/* QR Code Section Styles */
.qr-code-section {
    margin-top: 25px;
    padding-top: 25px;
    border-top: 2px solid #e0e0e0;
}

.qr-code-wrapper {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.qr-code-wrapper:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.qr-code-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e50914;
}

.qr-code-header i {
    font-size: 24px;
    color: #e50914;
}

.qr-code-header span {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    letter-spacing: 0.5px;
}

.qr-code-container {
    display: flex;
    justify-content: center;
    align-items: center;
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 3px solid #e50914;
    position: relative;
}

.qr-code-container::before {
    content: '';
    position: absolute;
    top: -3px;
    left: -3px;
    right: -3px;
    bottom: -3px;
    background: linear-gradient(45deg, #e50914, #ff6b6b, #e50914);
    border-radius: 12px;
    z-index: -1;
    opacity: 0.1;
}

.qr-code-image {
    max-width: 220px;
    width: 100%;
    height: auto;
    display: block;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.qr-code-image:hover {
    transform: scale(1.05);
}

.qr-code-info {
    text-align: center;
}

.qr-code-label {
    font-size: 12px;
    color: #666;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.qr-code-value {
    font-size: 13px;
    color: #333;
    font-family: 'Courier New', monospace;
    word-break: break-all;
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 12px;
    border: 1px solid #e0e0e0;
    font-weight: 600;
}

.qr-code-hint {
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 10px;
    font-style: italic;
}

.qr-code-hint i {
    color: #e50914;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .qr-code-wrapper {
        padding: 20px;
    }
    
    .qr-code-header {
        font-size: 16px;
    }
    
    .qr-code-image {
        max-width: 180px;
    }
    
    .qr-code-value {
        font-size: 11px;
        padding: 8px 12px;
    }
}
</style>

