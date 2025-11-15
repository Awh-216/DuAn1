<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Thêm rạp mới</h5>
    <a href="?route=admin/theaters" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="stat-card">
    <form method="POST" action="?route=admin/theaters/store">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Tên rạp <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="location" class="form-label">Tỉnh/Thành phố</label>
                <input type="text" class="form-control" id="location" name="location" placeholder="VD: Hà Nội, TP.HCM">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="address" class="form-label">Địa chỉ chi tiết</label>
                <textarea class="form-control" id="address" name="address" rows="2" placeholder="VD: 72 Lê Thánh Tôn, Hoàn Kiếm, Hà Nội"></textarea>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="VD: 0241234567">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="total_screens" class="form-label">Số phòng chiếu</label>
                <input type="number" class="form-control" id="total_screens" name="total_screens" value="1" min="1">
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="?route=admin/theaters" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu
            </button>
        </div>
    </form>
</div>

