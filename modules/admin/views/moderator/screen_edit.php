<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Cấu hình layout ghế - <?php echo htmlspecialchars($screen['screen_name']); ?></h5>
    <a href="?route=moderator/screens" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<form method="POST" action="?route=moderator/screenLayoutUpdate">
    <input type="hidden" name="screen_id" value="<?php echo $screen['id']; ?>">
    
    <div class="row">
        <div class="col-md-6">
            <div class="stat-card mb-4">
                <h6 class="mb-3">Thông tin phòng</h6>
                <div class="mb-3">
                    <label class="form-label">Tên phòng:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($screen['screen_name']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Loại phòng:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($screen['screen_type'] ?? '2D'); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số ghế:</label>
                    <input type="text" class="form-control" value="<?php echo $screen['total_seats']; ?>" disabled>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="stat-card mb-4">
                <h6 class="mb-3">Giá vé</h6>
                <div class="mb-3">
                    <label for="normal_price" class="form-label">Giá ghế thường (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="normal_price" id="normal_price" class="form-control" 
                           value="<?php echo $layout['normal_price'] ?? 120000; ?>" min="0" required>
                </div>
                <div class="mb-3">
                    <label for="vip_price" class="form-label">Giá ghế VIP (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="vip_price" id="vip_price" class="form-control" 
                           value="<?php echo $layout['vip_price'] ?? 180000; ?>" min="0" required>
                </div>
                <div class="mb-3">
                    <label for="couple_price" class="form-label">Giá ghế đôi (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="couple_price" id="couple_price" class="form-control" 
                           value="<?php echo $layout['couple_price'] ?? 240000; ?>" min="0" required>
                </div>
            </div>
        </div>
    </div>
    
    <div class="stat-card mb-4">
        <h6 class="mb-3">Cấu hình hàng ghế</h6>
        <div class="mb-3">
            <label for="rows" class="form-label">Danh sách hàng (mỗi hàng một dòng, ví dụ: A, B, C, D, E...) <span class="text-danger">*</span></label>
            <textarea name="rows" id="rows" class="form-control" rows="3" required 
                      placeholder="A, B, C, D, E, F, G, H, I, J, K, L"><?php echo implode(', ', $layout['rows'] ?? []); ?></textarea>
            <small class="form-text text-muted">Nhập các hàng cách nhau bởi dấu phẩy hoặc xuống dòng</small>
        </div>
    </div>
    
    <div class="stat-card mb-4">
        <h6 class="mb-3">Cấu hình cột ghế</h6>
        <div class="mb-3">
            <label for="cols" class="form-label">Danh sách cột (mỗi cột một dòng, ví dụ: 1, 2, 3, 4...) <span class="text-danger">*</span></label>
            <textarea name="cols" id="cols" class="form-control" rows="3" required 
                      placeholder="1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12"><?php echo implode(', ', $layout['cols'] ?? []); ?></textarea>
            <small class="form-text text-muted">Nhập các cột cách nhau bởi dấu phẩy hoặc xuống dòng</small>
        </div>
    </div>
    
    <div class="stat-card mb-4">
        <h6 class="mb-3">Hàng ghế VIP</h6>
        <div class="mb-3">
            <label for="vip_rows" class="form-label">Danh sách hàng VIP (mỗi hàng một dòng, ví dụ: C, D, E)</label>
            <textarea name="vip_rows" id="vip_rows" class="form-control" rows="2" 
                      placeholder="C, D, E"><?php echo implode(', ', $layout['vip_rows'] ?? []); ?></textarea>
            <small class="form-text text-muted">Nhập các hàng VIP cách nhau bởi dấu phẩy. Để trống nếu không có hàng VIP.</small>
        </div>
    </div>
    
    <div class="stat-card mb-4">
        <h6 class="mb-3">Hàng ghế đôi</h6>
        <div class="mb-3">
            <label for="couple_rows" class="form-label">Danh sách hàng ghế đôi (mỗi hàng một dòng, ví dụ: L)</label>
            <textarea name="couple_rows" id="couple_rows" class="form-control" rows="2" 
                      placeholder="L"><?php echo implode(', ', $layout['couple_rows'] ?? []); ?></textarea>
            <small class="form-text text-muted">Nhập các hàng ghế đôi cách nhau bởi dấu phẩy. Thường là hàng cuối cùng. Để trống nếu không có hàng ghế đôi.</small>
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <a href="?route=moderator/screens" class="btn btn-secondary">Hủy</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Lưu cấu hình
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý textarea để chuyển đổi giữa dấu phẩy và xuống dòng
    function parseTextarea(value) {
        // Loại bỏ khoảng trắng thừa và split theo dấu phẩy hoặc xuống dòng
        return value.split(/[,\n]/)
            .map(item => item.trim())
            .filter(item => item.length > 0);
    }
    
    // Khi submit form, chuyển đổi textarea thành array
    document.querySelector('form').addEventListener('submit', function(e) {
        const rowsInput = document.getElementById('rows');
        const colsInput = document.getElementById('cols');
        const vipRowsInput = document.getElementById('vip_rows');
        const coupleRowsInput = document.getElementById('couple_rows');
        
        // Tạo hidden inputs với giá trị đã parse
        const rows = parseTextarea(rowsInput.value);
        const cols = parseTextarea(colsInput.value);
        const vipRows = vipRowsInput.value.trim() ? parseTextarea(vipRowsInput.value) : [];
        const coupleRows = coupleRowsInput.value.trim() ? parseTextarea(coupleRowsInput.value) : [];
        
        // Thêm hidden inputs
        rows.forEach((row, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'rows[]';
            input.value = row;
            this.appendChild(input);
        });
        
        cols.forEach((col, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'cols[]';
            input.value = col;
            this.appendChild(input);
        });
        
        vipRows.forEach((row, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'vip_rows[]';
            input.value = row;
            this.appendChild(input);
        });
        
        coupleRows.forEach((row, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'couple_rows[]';
            input.value = row;
            this.appendChild(input);
        });
        
        // Disable textarea để không gửi giá trị cũ
        rowsInput.disabled = true;
        colsInput.disabled = true;
        vipRowsInput.disabled = true;
        coupleRowsInput.disabled = true;
    });
});
</script>

