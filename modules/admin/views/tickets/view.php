<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Chi tiết vé #<?php echo $ticket['id']; ?></h5>
    <a href="?route=admin/tickets" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="stat-card mb-4">
            <h6 class="mb-3"><i class="fas fa-ticket-alt me-2"></i>Thông tin vé</h6>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-muted">Mã vé</label>
                    <div class="form-control-plaintext fw-bold">#<?php echo $ticket['id']; ?></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Trạng thái</label>
                    <div class="form-control-plaintext">
                        <span class="badge bg-<?php echo $ticket['status'] === 'Đã đặt' ? 'success' : 'danger'; ?>">
                            <?php echo htmlspecialchars($ticket['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-muted">Ghế ngồi</label>
                    <div class="form-control-plaintext">
                        <span class="badge bg-info fs-6"><?php echo htmlspecialchars($ticket['seat']); ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Giá vé</label>
                    <div class="form-control-plaintext fw-bold text-success">
                        <?php echo number_format($ticket['price']); ?>₫
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-muted">QR Code</label>
                <div class="form-control-plaintext">
                    <?php if ($ticket['qr_code']): ?>
                        <div class="d-flex align-items-center">
                            <code class="bg-light p-2 rounded me-2"><?php echo htmlspecialchars($ticket['qr_code']); ?></code>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Đã có QR code
                            </span>
                        </div>
                    <?php else: ?>
                        <span class="badge bg-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>Chưa có QR code
                        </span>
                        <small class="text-muted d-block mt-1">Khách hàng chưa nhận được QR code. Vui lòng hoàn thành vé thủ công.</small>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-muted">Thời gian tạo</label>
                <div class="form-control-plaintext">
                    <i class="fas fa-calendar me-1"></i>
                    <?php echo date('d/m/Y H:i:s', strtotime($ticket['created_at'])); ?>
                </div>
            </div>
        </div>
        
        <div class="stat-card mb-4">
            <h6 class="mb-3"><i class="fas fa-film me-2"></i>Thông tin suất chiếu</h6>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-muted">Phim</label>
                    <div class="form-control-plaintext fw-bold"><?php echo htmlspecialchars($ticket['movie_title']); ?></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Rạp</label>
                    <div class="form-control-plaintext"><?php echo htmlspecialchars($ticket['theater_name']); ?></div>
                </div>
            </div>
            
            <?php if ($ticket['theater_location']): ?>
            <div class="mb-3">
                <label class="form-label text-muted">Địa điểm</label>
                <div class="form-control-plaintext">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    <?php echo htmlspecialchars($ticket['theater_location']); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($ticket['theater_address']): ?>
            <div class="mb-3">
                <label class="form-label text-muted">Địa chỉ</label>
                <div class="form-control-plaintext">
                    <?php echo htmlspecialchars($ticket['theater_address']); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label text-muted">Ngày chiếu</label>
                    <div class="form-control-plaintext">
                        <i class="fas fa-calendar-day me-1"></i>
                        <?php echo date('d/m/Y', strtotime($ticket['show_date'])); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted">Giờ chiếu</label>
                    <div class="form-control-plaintext">
                        <i class="fas fa-clock me-1"></i>
                        <?php echo date('H:i', strtotime($ticket['show_time'])); ?>
                    </div>
                </div>
                <?php if ($ticket['screen_name']): ?>
                <div class="col-md-4">
                    <label class="form-label text-muted">Phòng chiếu</label>
                    <div class="form-control-plaintext">
                        <?php echo htmlspecialchars($ticket['screen_name']); ?>
                        <?php if ($ticket['screen_type']): ?>
                            <span class="badge bg-secondary ms-1"><?php echo htmlspecialchars($ticket['screen_type']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Báo cáo của người dùng -->
        <div class="stat-card mb-4">
            <h6 class="mb-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                Báo cáo của người dùng
                <?php if (!empty($supportTickets)): ?>
                    <span class="badge bg-info"><?php echo count($supportTickets); ?></span>
                <?php endif; ?>
            </h6>
            
            <?php if (empty($supportTickets)): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>Không có báo cáo nào từ người dùng này về vé.</p>
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($supportTickets as $st): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">
                                        <a href="?route=admin/support/view&id=<?php echo $st['id']; ?>" class="text-decoration-none">
                                            #<?php echo $st['id']; ?> - <?php echo htmlspecialchars($st['subject']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($st['created_at'])); ?>
                                    </small>
                                </div>
                                <div>
                                    <span class="badge bg-<?php 
                                        echo match($st['status']) {
                                            'Mới' => 'primary',
                                            'Đang xử lý' => 'warning',
                                            'Đã giải quyết' => 'success',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php echo htmlspecialchars($st['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">Nội dung:</small>
                                <div class="bg-light p-2 rounded" style="max-height: 100px; overflow-y: auto; font-size: 0.9rem;">
                                    <?php echo nl2br(htmlspecialchars(mb_substr($st['message'], 0, 200))); ?>
                                    <?php if (mb_strlen($st['message']) > 200): ?>
                                        <span class="text-muted">...</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($st['tags']): ?>
                                <div class="mb-2">
                                    <?php 
                                    // Extract issue từ tags
                                    $issue = 'N/A';
                                    if (!empty($st['tags'])) {
                                        $parts = explode(' - ', $st['tags']);
                                        if (count($parts) > 1) {
                                            $issue = $parts[1];
                                        } else {
                                            $issue = $st['tags'];
                                        }
                                    }
                                    
                                    // Xác định category
                                    $category = 'N/A';
                                    $categoryClass = 'secondary';
                                    if (!empty($st['tags'])) {
                                        if ((stripos($st['tags'], 'Đặt vé') !== false || stripos($st['tags'], 'Mua bán vé') !== false) && 
                                            (stripos($st['tags'], 'Không nhận được vé') !== false || stripos($st['tags'], 'Vấn đề về ghế ngồi') !== false)) {
                                            $category = 'Lỗi mua bán vé';
                                            $categoryClass = 'danger';
                                        } elseif (stripos($st['tags'], 'Đặt vé') !== false || stripos($st['tags'], 'Mua bán vé') !== false) {
                                            $category = 'Mua bán vé';
                                            $categoryClass = 'warning';
                                        }
                                    }
                                    ?>
                                    <small class="text-muted d-block mb-1">Vấn đề:</small>
                                    <span class="badge bg-<?php echo $categoryClass; ?>"><?php echo htmlspecialchars($issue); ?></span>
                                    <?php if ($category !== 'N/A'): ?>
                                        <span class="badge bg-<?php echo $categoryClass; ?> ms-1"><?php echo htmlspecialchars($category); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo htmlspecialchars($st['user_name']); ?>
                                    <?php if ($st['subscription_name']): ?>
                                        <span class="badge bg-secondary ms-1"><?php echo htmlspecialchars($st['subscription_name']); ?></span>
                                    <?php endif; ?>
                                </small>
                                <a href="?route=admin/support/view&id=<?php echo $st['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Xem chi tiết
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card mb-4">
            <h6 class="mb-3"><i class="fas fa-user me-2"></i>Thông tin khách hàng</h6>
            
            <div class="mb-3">
                <label class="form-label text-muted">Tên</label>
                <div class="form-control-plaintext"><?php echo htmlspecialchars($ticket['user_name']); ?></div>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-muted">Email</label>
                <div class="form-control-plaintext">
                    <i class="fas fa-envelope me-1"></i>
                    <?php echo htmlspecialchars($ticket['user_email']); ?>
                </div>
            </div>
            
            <?php if ($ticket['user_phone']): ?>
            <div class="mb-3">
                <label class="form-label text-muted">Số điện thoại</label>
                <div class="form-control-plaintext">
                    <i class="fas fa-phone me-1"></i>
                    <?php echo htmlspecialchars($ticket['user_phone']); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="stat-card">
            <h6 class="mb-3"><i class="fas fa-tasks me-2"></i>Thao tác</h6>
            
            <?php if ($ticket['status'] === 'Đã đặt'): ?>
                <?php if (empty($ticket['qr_code'])): ?>
                    <!-- Nút hoàn thành vé thủ công -->
                    <form method="POST" action="?route=admin/tickets/complete" class="mb-2" onsubmit="return confirm('Bạn chắc chắn muốn hoàn thành vé thủ công cho khách hàng này? Hệ thống sẽ tạo QR code cho vé.');">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check-circle me-2"></i>Hoàn thành vé thủ công
                        </button>
                    </form>
                    <small class="text-muted d-block mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Sử dụng khi khách hàng đã thanh toán nhưng chưa nhận được vé.
                    </small>
                <?php else: ?>
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle me-2"></i>
                        Vé đã có QR code. Khách hàng có thể sử dụng vé này.
                    </div>
                <?php endif; ?>
                
                <a href="?route=admin/tickets/cancel&id=<?php echo $ticket['id']; ?>" class="btn btn-outline-warning w-100 mb-2" onclick="return confirm('Bạn chắc chắn muốn hủy vé?')">
                    <i class="fas fa-times me-2"></i>Hủy vé
                </a>
                
                <a href="?route=admin/tickets/refund&id=<?php echo $ticket['id']; ?>" class="btn btn-outline-danger w-100" onclick="return confirm('Bạn chắc chắn muốn hoàn tiền?')">
                    <i class="fas fa-undo me-2"></i>Hoàn tiền
                </a>
            <?php else: ?>
                <div class="alert alert-secondary">
                    <i class="fas fa-info-circle me-2"></i>
                    Vé đã bị hủy. Không thể thực hiện thao tác.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

