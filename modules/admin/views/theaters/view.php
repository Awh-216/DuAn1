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

<!-- Danh sách phòng chiếu -->
<div class="row mt-4">
    <div class="col-12">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 style="color: #000; margin: 0;">Danh sách phòng chiếu</h6>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addScreenModal">
                    <i class="fas fa-plus"></i> Thêm phòng chiếu
                </button>
            </div>
            
            <?php if (!empty($screens)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="color: #333;">Tên phòng</th>
                            <th style="color: #333;">Loại</th>
                            <th style="color: #333;">Số ghế</th>
                            <th style="color: #333;">Ghế VIP</th>
                            <th style="color: #333;">Ghế đôi</th>
                            <th style="color: #333;">Trạng thái</th>
                            <th style="color: #333;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($screens as $screen): 
                            $layout = $screen['seat_layout_config'] ? json_decode($screen['seat_layout_config'], true) : null;
                            $vipRows = $layout['vip_rows'] ?? [];
                            $coupleRows = $layout['couple_rows'] ?? [];
                        ?>
                        <tr>
                            <td style="color: #333;"><?php echo htmlspecialchars($screen['screen_name']); ?></td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($screen['screen_type'] ?? '2D'); ?></span></td>
                            <td style="color: #333;"><?php echo $screen['total_seats'] ?? 0; ?> ghế</td>
                            <td style="color: #333;">
                                <?php echo !empty($vipRows) ? implode(', ', $vipRows) : 'Chưa cấu hình'; ?>
                            </td>
                            <td style="color: #333;">
                                <?php echo !empty($coupleRows) ? implode(', ', $coupleRows) : 'Chưa cấu hình'; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $screen['is_active'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $screen['is_active'] ? 'Hoạt động' : 'Tắt'; ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="editScreen(<?php echo $screen['id']; ?>)"
                                        title="Chỉnh sửa sơ đồ ghế">
                                    <i class="fas fa-chair"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteScreen(<?php echo $screen['id']; ?>)"
                                        title="Xóa phòng">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="color: #666; text-align: center;">Chưa có phòng chiếu nào. Hãy thêm phòng chiếu mới.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Thêm phòng chiếu -->
<div class="modal fade" id="addScreenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm phòng chiếu mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="?route=admin/theaters/addScreen" method="POST">
                <input type="hidden" name="theater_id" value="<?php echo $theater['id']; ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên phòng <span class="text-danger">*</span></label>
                                <input type="text" name="screen_name" class="form-control" required placeholder="VD: Phòng 1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Loại phòng</label>
                                <select name="screen_type" class="form-select">
                                    <option value="2D">2D</option>
                                    <option value="3D">3D</option>
                                    <option value="IMAX">IMAX</option>
                                    <option value="4DX">4DX</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6>Cấu hình sơ đồ ghế</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Số hàng ghế</label>
                                <input type="number" name="num_rows" class="form-control" value="10" min="1" max="26">
                                <small class="text-muted">Tối đa 26 hàng (A-Z)</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Số ghế mỗi hàng</label>
                                <input type="number" name="num_cols" class="form-control" value="12" min="1" max="30">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tổng số ghế</label>
                                <input type="text" id="totalSeatsDisplay" class="form-control" value="120" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hàng ghế VIP (cách nhau bởi dấu phẩy)</label>
                                <input type="text" name="vip_rows" class="form-control" placeholder="VD: D, E, F">
                                <small class="text-muted">Để trống nếu không có ghế VIP</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hàng ghế đôi (cách nhau bởi dấu phẩy)</label>
                                <input type="text" name="couple_rows" class="form-control" placeholder="VD: J">
                                <small class="text-muted">Thường là hàng cuối cùng</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6>Giá vé</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá ghế thường (VNĐ)</label>
                                <input type="number" name="normal_price" class="form-control" value="90000" min="0" step="1000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá ghế VIP (VNĐ)</label>
                                <input type="number" name="vip_price" class="form-control" value="120000" min="0" step="1000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá ghế đôi (VNĐ)</label>
                                <input type="number" name="couple_price" class="form-control" value="180000" min="0" step="1000">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm phòng chiếu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Chỉnh sửa phòng chiếu -->
<div class="modal fade" id="editScreenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa sơ đồ ghế</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="?route=admin/theaters/updateScreen" method="POST">
                <input type="hidden" name="screen_id" id="edit_screen_id">
                <input type="hidden" name="theater_id" value="<?php echo $theater['id']; ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên phòng <span class="text-danger">*</span></label>
                                <input type="text" name="screen_name" id="edit_screen_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Loại phòng</label>
                                <select name="screen_type" id="edit_screen_type" class="form-select">
                                    <option value="2D">2D</option>
                                    <option value="3D">3D</option>
                                    <option value="IMAX">IMAX</option>
                                    <option value="4DX">4DX</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="edit_is_active" class="form-check-input" value="1">
                            <label class="form-check-label" for="edit_is_active">Hoạt động</label>
                        </div>
                    </div>
                    
                    <hr>
                    <h6>Cấu hình sơ đồ ghế</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Số hàng ghế</label>
                                <input type="number" name="num_rows" id="edit_num_rows" class="form-control" min="1" max="26">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Số ghế mỗi hàng</label>
                                <input type="number" name="num_cols" id="edit_num_cols" class="form-control" min="1" max="30">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tổng số ghế</label>
                                <input type="text" id="edit_total_seats" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hàng ghế VIP</label>
                                <input type="text" name="vip_rows" id="edit_vip_rows" class="form-control" placeholder="VD: D, E, F">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hàng ghế đôi</label>
                                <input type="text" name="couple_rows" id="edit_couple_rows" class="form-control" placeholder="VD: J">
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6>Giá vé</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá ghế thường (VNĐ)</label>
                                <input type="number" name="normal_price" id="edit_normal_price" class="form-control" min="0" step="1000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá ghế VIP (VNĐ)</label>
                                <input type="number" name="vip_price" id="edit_vip_price" class="form-control" min="0" step="1000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Giá ghế đôi (VNĐ)</label>
                                <input type="number" name="couple_price" id="edit_couple_price" class="form-control" min="0" step="1000">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview sơ đồ ghế -->
                    <hr>
                    <h6>Xem trước sơ đồ ghế</h6>
                    <div id="seatPreview" style="background: #1a1a2e; padding: 20px; border-radius: 10px; overflow-x: auto;">
                        <div style="text-align: center; color: #fff; margin-bottom: 20px; padding: 10px; background: #333; border-radius: 5px;">MÀN HÌNH</div>
                        <div id="seatMap" style="display: flex; flex-direction: column; align-items: center; gap: 5px;"></div>
                        <div style="margin-top: 20px; display: flex; justify-content: center; gap: 20px;">
                            <span style="color: #fff;"><span style="display: inline-block; width: 20px; height: 20px; background: #6c757d; border-radius: 3px;"></span> Thường</span>
                            <span style="color: #fff;"><span style="display: inline-block; width: 20px; height: 20px; background: #ffc107; border-radius: 3px;"></span> VIP</span>
                            <span style="color: #fff;"><span style="display: inline-block; width: 20px; height: 20px; background: #9c27b0; border-radius: 3px;"></span> Đôi</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Dữ liệu screens từ PHP
const screensData = <?php echo json_encode($screens ?? []); ?>;

// Tính tổng số ghế khi thay đổi
document.querySelector('input[name="num_rows"]')?.addEventListener('input', updateTotalSeats);
document.querySelector('input[name="num_cols"]')?.addEventListener('input', updateTotalSeats);

function updateTotalSeats() {
    const rows = parseInt(document.querySelector('input[name="num_rows"]').value) || 0;
    const cols = parseInt(document.querySelector('input[name="num_cols"]').value) || 0;
    document.getElementById('totalSeatsDisplay').value = rows * cols;
}

// Chỉnh sửa phòng chiếu
function editScreen(screenId) {
    const screen = screensData.find(s => s.id == screenId);
    if (!screen) return;
    
    const layout = screen.seat_layout_config ? JSON.parse(screen.seat_layout_config) : {};
    
    document.getElementById('edit_screen_id').value = screen.id;
    document.getElementById('edit_screen_name').value = screen.screen_name;
    document.getElementById('edit_screen_type').value = screen.screen_type || '2D';
    document.getElementById('edit_is_active').checked = screen.is_active == 1;
    
    const rows = layout.rows || ['A','B','C','D','E','F','G','H','I','J'];
    const cols = layout.cols || [1,2,3,4,5,6,7,8,9,10,11,12];
    
    document.getElementById('edit_num_rows').value = rows.length;
    document.getElementById('edit_num_cols').value = cols.length;
    document.getElementById('edit_total_seats').value = rows.length * cols.length;
    
    document.getElementById('edit_vip_rows').value = (layout.vip_rows || []).join(', ');
    document.getElementById('edit_couple_rows').value = (layout.couple_rows || []).join(', ');
    
    document.getElementById('edit_normal_price').value = layout.normal_price || 90000;
    document.getElementById('edit_vip_price').value = layout.vip_price || 120000;
    document.getElementById('edit_couple_price').value = layout.couple_price || 180000;
    
    // Render preview
    renderSeatPreview();
    
    // Show modal
    new bootstrap.Modal(document.getElementById('editScreenModal')).show();
}

// Render seat preview
function renderSeatPreview() {
    const numRows = parseInt(document.getElementById('edit_num_rows').value) || 10;
    const numCols = parseInt(document.getElementById('edit_num_cols').value) || 12;
    const vipRowsStr = document.getElementById('edit_vip_rows').value;
    const coupleRowsStr = document.getElementById('edit_couple_rows').value;
    
    const vipRows = vipRowsStr.split(',').map(r => r.trim().toUpperCase()).filter(r => r);
    const coupleRows = coupleRowsStr.split(',').map(r => r.trim().toUpperCase()).filter(r => r);
    
    const seatMap = document.getElementById('seatMap');
    seatMap.innerHTML = '';
    
    for (let i = 0; i < numRows; i++) {
        const rowLetter = String.fromCharCode(65 + i);
        const rowDiv = document.createElement('div');
        rowDiv.style.cssText = 'display: flex; align-items: center; gap: 5px;';
        
        // Row label
        const rowLabel = document.createElement('span');
        rowLabel.style.cssText = 'width: 25px; color: #fff; font-weight: bold;';
        rowLabel.textContent = rowLetter;
        rowDiv.appendChild(rowLabel);
        
        // Seats
        const isVip = vipRows.includes(rowLetter);
        const isCouple = coupleRows.includes(rowLetter);
        
        if (isCouple) {
            // Ghế đôi - mỗi 2 cột là 1 ghế
            for (let j = 0; j < numCols; j += 2) {
                const seat = document.createElement('div');
                seat.style.cssText = 'width: 50px; height: 25px; background: #9c27b0; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 10px;';
                seat.textContent = rowLetter + (j+1) + '-' + (j+2);
                rowDiv.appendChild(seat);
            }
        } else {
            for (let j = 0; j < numCols; j++) {
                const seat = document.createElement('div');
                seat.style.cssText = `width: 25px; height: 25px; background: ${isVip ? '#ffc107' : '#6c757d'}; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: ${isVip ? '#000' : '#fff'}; font-size: 8px;`;
                seat.textContent = (j + 1);
                rowDiv.appendChild(seat);
            }
        }
        
        seatMap.appendChild(rowDiv);
    }
    
    // Update total seats
    document.getElementById('edit_total_seats').value = numRows * numCols;
}

// Update preview when inputs change
document.getElementById('edit_num_rows')?.addEventListener('input', renderSeatPreview);
document.getElementById('edit_num_cols')?.addEventListener('input', renderSeatPreview);
document.getElementById('edit_vip_rows')?.addEventListener('input', renderSeatPreview);
document.getElementById('edit_couple_rows')?.addEventListener('input', renderSeatPreview);

// Xóa phòng chiếu
function deleteScreen(screenId) {
    if (confirm('Bạn có chắc muốn xóa phòng chiếu này? Hành động này không thể hoàn tác.')) {
        window.location.href = '?route=admin/theaters/deleteScreen&id=' + screenId + '&theater_id=<?php echo $theater['id']; ?>';
    }
}
</script>

