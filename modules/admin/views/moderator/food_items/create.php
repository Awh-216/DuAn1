<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Thêm Combo/Đồ ăn mới</h5>
    <a href="?route=moderator/foodItems" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="stat-card">
    <form method="POST" action="?route=moderator/foodItemsStore" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Tên combo/đồ ăn <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="type" class="form-label">Loại <span class="text-danger">*</span></label>
                <select class="form-select" id="type" name="type" required>
                    <option value="combo">Combo</option>
                    <option value="snack">Snack</option>
                    <option value="drink">Đồ uống</option>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="price" name="price" min="0" step="1000" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="image" class="form-label">Ảnh</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <small class="text-muted">Chấp nhận: JPEG, PNG, GIF, WebP (tối đa 5MB)</small>
                <div id="image-preview" class="mt-2" style="display: none;">
                    <img id="preview-img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                </div>
            </div>
            
            <div class="col-12 mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Mô tả chi tiết về combo/đồ ăn..."></textarea>
            </div>
            
            <div class="col-12 mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">
                        Hoạt động (hiển thị cho khách hàng)
                    </label>
                </div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu
            </button>
            <a href="?route=moderator/foodItems" class="btn btn-secondary">
                <i class="fas fa-times"></i> Hủy
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('image-preview').style.display = 'none';
    }
});
</script>

