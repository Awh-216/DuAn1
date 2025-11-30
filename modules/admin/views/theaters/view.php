<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Thông tin rạp chiếu</h5>
    <a href="?route=admin/theaters" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="stat-card">
            <h6 class="mb-4" style="color: #000;">Thông tin cơ bản</h6>
            
            <?php if (!empty($theater['image'])): ?>
                <div class="mb-4" style="width: 100%; height: 400px; overflow: hidden; border-radius: 8px;">
                    <img src="<?php echo htmlspecialchars($theater['image']); ?>" 
                         alt="<?php echo htmlspecialchars($theater['name']); ?>" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            <?php endif; ?>
            
            <table class="table table-borderless">
                <tr>
                    <td style="width: 200px; color: #000; font-weight: bold;">Tên rạp:</td>
                    <td style="color: #000;"><?php echo htmlspecialchars($theater['name']); ?></td>
                </tr>
                <tr>
                    <td style="color: #000; font-weight: bold;">Địa điểm:</td>
                    <td style="color: #000;">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($theater['location'] ?? 'N/A'); ?>
                    </td>
                </tr>
                <?php if ($theater['address']): ?>
                <tr>
                    <td style="color: #000; font-weight: bold;">Địa chỉ:</td>
                    <td style="color: #000;"><?php echo htmlspecialchars($theater['address']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($theater['phone']): ?>
                <tr>
                    <td style="color: #000; font-weight: bold;">Số điện thoại:</td>
                    <td style="color: #000;">
                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($theater['phone']); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (isset($theater['latitude']) && isset($theater['longitude'])): ?>
                <tr>
                    <td style="color: #000; font-weight: bold;">Tọa độ:</td>
                    <td style="color: #000;">
                        <i class="fas fa-map-pin"></i> 
                        Latitude: <?php echo htmlspecialchars($theater['latitude']); ?>, 
                        Longitude: <?php echo htmlspecialchars($theater['longitude']); ?>
                        <br>
                        <a href="https://www.google.com/maps?q=<?php echo urlencode($theater['latitude'] . ',' . $theater['longitude']); ?>" 
                           target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-external-link-alt"></i> Xem trên Google Maps
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="color: #000; font-weight: bold;">Số phòng chiếu:</td>
                    <td style="color: #000;"><?php echo $theater['total_screens'] ?? 0; ?> phòng</td>
                </tr>
                <tr>
                    <td style="color: #000; font-weight: bold;">Trạng thái:</td>
                    <td>
                        <span class="badge bg-<?php echo $theater['is_active'] ? 'success' : 'secondary'; ?>">
                            <?php echo $theater['is_active'] ? 'Hoạt động' : 'Ngừng hoạt động'; ?>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="mb-4" style="color: #000;">Admin của rạp</h6>
            <?php if ($moderator): ?>
                <div class="mb-3">
                    <p style="color: #000; margin-bottom: 5px;"><strong>Tên:</strong></p>
                    <p style="color: #000;"><?php echo htmlspecialchars($moderator['name']); ?></p>
                </div>
                <div class="mb-3">
                    <p style="color: #000; margin-bottom: 5px;"><strong>Email:</strong></p>
                    <p style="color: #000;"><?php echo htmlspecialchars($moderator['email']); ?></p>
                </div>
                <div class="mb-3">
                    <p style="color: #000; margin-bottom: 5px;"><strong>Ngày tạo:</strong></p>
                    <p style="color: #000;"><?php echo date('d/m/Y H:i', strtotime($moderator['created_at'])); ?></p>
                </div>
            <?php else: ?>
                <p style="color: #666;">Chưa có admin được gán cho rạp này</p>
            <?php endif; ?>
        </div>
    </div>
</div>

