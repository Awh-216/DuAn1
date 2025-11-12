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
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <h3><?php echo htmlspecialchars($ticket['movie_title']); ?></h3>
                            <span class="ticket-status <?php echo strtolower($ticket['status']); ?>"><?php echo $ticket['status']; ?></span>
                        </div>
                        <div class="ticket-info">
                            <p><i class="fas fa-building"></i> <?php echo htmlspecialchars($ticket['theater_name']); ?></p>
                            <p><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($ticket['show_date'])); ?></p>
                            <p><i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($ticket['show_time'])); ?></p>
                            <p><i class="fas fa-chair"></i> Ghế: <?php echo htmlspecialchars($ticket['seat']); ?></p>
                            <p><i class="fas fa-money-bill"></i> Giá: <?php echo number_format($ticket['price']); ?> đ</p>
                            <?php if ($ticket['qr_code']): ?>
                                <p><i class="fas fa-qrcode"></i> Mã QR: <?php echo htmlspecialchars($ticket['qr_code']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

