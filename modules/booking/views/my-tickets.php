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
                <a href="http://localhost/DuAn1/?route=booking/index" class="btn btn-primary">Đặt vé ngay</a>
            </div>
        <?php else: ?>
            <div class="tickets-list">
                <?php foreach ($tickets as $ticket): ?>
                    <?php 
                    $isPending = ($ticket['booking_type'] ?? 'completed') === 'pending';
                    $statusClass = $isPending ? 'pending' : strtolower(str_replace(' ', '-', $ticket['status']));
                    ?>
                    <div class="ticket-card <?php echo $isPending ? 'pending-booking' : ''; ?>">
                        <div class="ticket-header">
                            <h3><?php echo htmlspecialchars($ticket['movie_title']); ?></h3>
                            <span class="ticket-status <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($ticket['status']); ?>
                            </span>
                        </div>
                        <?php if ($isPending): ?>
                            <div class="pending-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Vé này sẽ tự động hủy sau 10 phút nếu không thanh toán</span>
                                <?php if (isset($ticket['expires_at'])): ?>
                                    <small class="expires-at">
                                        Hết hạn: <?php echo date('d/m/Y H:i', strtotime($ticket['expires_at'])); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="ticket-info">
                            <p><i class="fas fa-building"></i> <?php echo htmlspecialchars($ticket['theater_name']); ?></p>
                            <p><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($ticket['show_date'])); ?></p>
                            <p><i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($ticket['show_time'])); ?></p>
                            <p><i class="fas fa-chair"></i> Ghế: <?php echo htmlspecialchars($ticket['seat']); ?></p>
                            <p><i class="fas fa-money-bill"></i> Giá: <?php echo number_format($ticket['price']); ?> đ</p>
                            <?php if ($isPending && isset($ticket['food_items']) && !empty($ticket['food_items'])): ?>
                                <p><i class="fas fa-utensils"></i> Combo & Đồ ăn: 
                                    <?php 
                                    $foodNames = [];
                                    foreach ($ticket['food_items'] as $food) {
                                        if (is_array($food) && isset($food['name'])) {
                                            $foodNames[] = htmlspecialchars($food['name']) . " x" . $food['quantity'];
                                        } elseif (is_numeric($food)) {
                                            // Fallback: nếu là số (quantity)
                                            $foodNames[] = "x" . $food;
                                        }
                                    }
                                    echo !empty($foodNames) ? implode(', ', $foodNames) : 'Không có';
                                    ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!$isPending && $ticket['qr_code']): ?>
                                <p><i class="fas fa-qrcode"></i> Mã QR: <?php echo htmlspecialchars($ticket['qr_code']); ?></p>
                            <?php endif; ?>
                            <?php if ($isPending): ?>
                                <div class="pending-actions">
                                    <a href="?route=booking/payment&txn_ref=<?php echo urlencode($ticket['vnp_txn_ref']); ?>" 
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
</style>

