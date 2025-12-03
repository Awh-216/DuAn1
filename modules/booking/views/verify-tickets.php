<?php
// Lấy base URL
if (!class_exists('UrlHelper')) {
    require_once __DIR__ . '/../../../core/UrlHelper.php';
}
$baseUrl = UrlHelper::getBaseUrl();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực vé - CineHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            color: #000;
        }
        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            color: #000;
        }
        .ticket-container h3,
        .ticket-container h4,
        .ticket-container h5,
        .ticket-container p,
        .ticket-container div,
        .ticket-container strong {
            color: #000;
        }
        .ticket-header {
            background: linear-gradient(135deg, #e50914 0%, #b20710 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 30px -30px;
            text-align: center;
        }
        .ticket-header h1 {
            margin: 0;
            font-size: 28px;
        }
        .ticket-item {
            border: 2px solid #e50914;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
            color: #000;
        }
        .ticket-item h5 {
            color: #000;
        }
        .ticket-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #000;
        }
        .info-item div {
            color: #000;
        }
        .info-item i {
            color: #e50914;
            font-size: 20px;
            width: 24px;
        }
        .btn-download {
            background: #e50914;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-download:hover {
            background: #b20710;
            color: white;
        }
        .error-message {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1><i class="fas fa-ticket-alt"></i> Vé xem phim</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">CineHub</p>
        </div>
        
        <?php 
        // Debug: Kiểm tra các biến
        $hasData = isset($movie) && isset($theater) && isset($showtime) && isset($user) && isset($tickets);
        if (!$hasData) {
            error_log("Missing data in view - movie: " . (isset($movie) ? 'yes' : 'no') . 
                     ", theater: " . (isset($theater) ? 'yes' : 'no') . 
                     ", showtime: " . (isset($showtime) ? 'yes' : 'no') . 
                     ", user: " . (isset($user) ? 'yes' : 'no') . 
                     ", tickets: " . (isset($tickets) ? 'yes' : 'no'));
        }
        ?>
        
        <?php if (isset($error) && $error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($hasData && !empty($tickets)): ?>
        
        <!-- Thông tin rạp và booking -->
        <div class="mb-4" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0;">
            <h4 style="color: #e50914; margin-bottom: 15px; font-size: 18px;">
                <i class="fas fa-building"></i> <?php echo htmlspecialchars($theater['name'] ?? 'N/A'); ?>
            </h4>
            <?php if (isset($theater['location']) && $theater['location']): ?>
                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($theater['location']); ?>
                </p>
            <?php endif; ?>
            <p style="color: #666; font-size: 13px; margin-bottom: 5px;">
                <strong>Mã booking:</strong> <span style="font-family: monospace; color: #000;"><?php echo htmlspecialchars($booking['qr_code'] ?? $booking['vnp_txn_ref'] ?? 'N/A'); ?></span>
            </p>
            <p style="color: #666; font-size: 13px;">
                <strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($booking['created_at'] ?? 'now')); ?>
            </p>
        </div>
        
        <!-- Danh sách vé -->
        <h4 style="color: #000; margin-bottom: 15px; font-size: 18px;">
            <i class="fas fa-ticket-alt"></i> Danh sách vé (<?php echo count($tickets ?? []); ?> vé)
        </h4>
        <?php if (!empty($tickets)): ?>
            <?php foreach ($tickets as $index => $ticket): ?>
                <div class="ticket-item" style="margin-bottom: 20px;">
                    <div style="border-bottom: 2px solid #e50914; padding-bottom: 10px; margin-bottom: 15px;">
                        <h5 style="color: #e50914; margin: 0; font-size: 16px;">VÉ XEM PHIM #<?php echo $index + 1; ?></h5>
                    </div>
                    
                    <!-- Thông tin phim -->
                    <div style="margin-bottom: 15px;">
                        <h6 style="color: #000; font-size: 16px; font-weight: bold; margin-bottom: 10px;">
                            <?php echo htmlspecialchars($movie['title'] ?? 'N/A'); ?>
                        </h6>
                        <p style="color: #666; font-size: 13px; margin: 5px 0;">
                            <strong>Loại:</strong> <?php echo htmlspecialchars($movie['format'] ?? '2D'); ?> 
                            <?php if (isset($movie['category_name'])): ?>
                                - <?php echo htmlspecialchars($movie['category_name']); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <!-- Thông tin chiếu -->
                    <div class="ticket-info" style="margin-bottom: 15px;">
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <strong>Ngày chiếu:</strong><br>
                                <span style="color: #000;"><?php echo isset($showtime['show_date']) ? date('d/m/Y', strtotime($showtime['show_date'])) : 'N/A'; ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Giờ chiếu:</strong><br>
                                <span style="color: #000;"><?php echo isset($showtime['show_time']) ? date('H:i', strtotime($showtime['show_time'])) : 'N/A'; ?></span>
                            </div>
                        </div>
                        <?php if (isset($screenInfo) && $screenInfo): ?>
                        <div class="info-item">
                            <i class="fas fa-door-open"></i>
                            <div>
                                <strong>Phòng chiếu:</strong><br>
                                <span style="color: #000;"><?php echo htmlspecialchars($screenInfo['screen_name'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Thông tin vé -->
                    <div class="ticket-info" style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                        <div class="info-item">
                            <i class="fas fa-chair"></i>
                            <div>
                                <strong>Ghế/Seat:</strong><br>
                                <span style="color: #e50914; font-weight: bold; font-size: 16px;"><?php echo htmlspecialchars($ticket['seat'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-money-bill-wave"></i>
                            <div>
                                <strong>Giá vé:</strong><br>
                                <span style="color: #000; font-weight: bold;"><?php echo number_format($ticket['price'] ?? 0, 0, ',', '.'); ?> đ</span><br>
                                <small style="color: #666; font-size: 11px;">(Đã gồm VAT)</small>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-barcode"></i>
                            <div>
                                <strong>Mã vé:</strong><br>
                                <small style="font-family: monospace; color: #000; font-size: 12px;"><?php echo htmlspecialchars($ticket['qr_code'] ?? 'N/A'); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Thông tin khách hàng cho từng vé -->
                    <div style="background: #f0f0f0; padding: 10px; border-radius: 5px; font-size: 13px;">
                        <p style="margin: 5px 0; color: #000;">
                            <strong>Tên khách hàng:</strong> <?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?>
                        </p>
                        <p style="margin: 5px 0; color: #000;">
                            <strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> Không có vé nào trong booking này.
            </div>
        <?php endif; ?>
        
        <!-- Combo & Đồ ăn (Bên ngoài phần vé) -->
        <?php if (isset($foodItems) && !empty($foodItems)): ?>
            <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 10px; padding: 20px; margin-top: 30px; margin-bottom: 20px;">
                <h4 style="color: #856404; margin-bottom: 15px; font-size: 18px;">
                    <i class="fas fa-utensils"></i> Combo & Đồ ăn
                </h4>
                <div style="display: grid; gap: 15px;">
                    <?php 
                    $totalFoodPrice = 0;
                    foreach ($foodItems as $food): 
                        $foodTotal = ($food['price'] ?? 0) * ($food['quantity'] ?? 0);
                        $totalFoodPrice += $foodTotal;
                    ?>
                        <div style="background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #ffc107; display: flex; justify-content: space-between; align-items: center;">
                            <div style="flex: 1;">
                                <p style="margin: 0; font-weight: bold; color: #000; font-size: 15px;">
                                    <?php echo htmlspecialchars($food['name'] ?? 'N/A'); ?>
                                    <?php if (isset($food['type']) && $food['type'] === 'combo'): ?>
                                        <span style="background: #ffc107; color: #000; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 5px;">COMBO</span>
                                    <?php endif; ?>
                                </p>
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">
                                    Số lượng: <strong><?php echo $food['quantity'] ?? 0; ?></strong> x 
                                    <?php echo number_format($food['price'] ?? 0, 0, ',', '.'); ?> đ
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <p style="margin: 0; font-weight: bold; color: #e50914; font-size: 16px;">
                                    <?php echo number_format($foodTotal, 0, ',', '.'); ?> đ
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div style="background: #fff; padding: 15px; border-radius: 8px; border: 2px solid #ffc107; margin-top: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <strong style="color: #000; font-size: 16px;">Tổng tiền đồ ăn:</strong>
                            <strong style="color: #e50914; font-size: 18px;"><?php echo number_format($totalFoodPrice, 0, ',', '.'); ?> đ</strong>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Tổng tiền booking -->
        <div style="background: #e50914; color: white; padding: 20px; border-radius: 10px; margin-top: 20px; text-align: center;">
            <p style="margin: 0; font-size: 18px; font-weight: bold;">
                Tổng thanh toán: <?php echo number_format($booking['total_amount'] ?? 0, 0, ',', '.'); ?> đ
            </p>
        </div>
        
        <!-- Lưu ý -->
        <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin-top: 20px; border-radius: 5px;">
            <p style="margin: 0; color: #000; font-size: 13px;">
                <strong><i class="fas fa-info-circle"></i> Lưu ý:</strong> Phim được phổ biến đến người xem dưới 13 tuổi phải có người lớn đi kèm. 
                Vui lòng đến rạp trước 15 phút để làm thủ tục vào rạp. Cảm ơn quý khách đã sử dụng dịch vụ của CineHub!
            </p>
        </div>
        
        <!-- Thông tin liên hệ -->
        <div style="text-align: center; margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 8px;">
            <p style="margin: 5px 0; color: #000; font-size: 14px; font-weight: bold;">
                CINEHUB - RẠP PHIM HIỆN ĐẠI
            </p>
            <p style="margin: 5px 0; color: #666; font-size: 12px;">
                Hotline: 1900 1234 | Email: support@cinehub.vn
            </p>
            <p style="margin: 5px 0; color: #666; font-size: 12px;">
                Website: www.cinehub.vn | Facebook: facebook.com/cinehub
            </p>
        </div>
        
        <!-- Nút PDF -->
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
            <?php if (isset($pdfUrl) && $pdfUrl): ?>
                <a href="<?php echo htmlspecialchars($pdfUrl); ?>" class="btn-download" style="margin-right: 10px; background: #28a745;">
                    <i class="fas fa-file-pdf"></i> Xem PDF
                </a>
                <a href="<?php echo htmlspecialchars(str_replace('pdf=1', 'pdf=1&download=1', $pdfUrl)); ?>" class="btn-download">
                    <i class="fas fa-download"></i> Tải PDF
                </a>
            <?php else: ?>
                <p style="color: #333; font-style: italic;">
                    <i class="fas fa-info-circle"></i> PDF không khả dụng. Vui lòng liên hệ hỗ trợ nếu cần.
                </p>
            <?php endif; ?>
        </div>
        <?php else: ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> Không tìm thấy thông tin vé. Vui lòng kiểm tra lại mã QR code.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

