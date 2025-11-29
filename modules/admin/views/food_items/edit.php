<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Sửa Combo/Đồ ăn</h5>
    <a href="?route=admin/foodItems" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="stat-card">
    <form method="POST" action="?route=admin/foodItemsUpdate" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $foodItem['id']; ?>">
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Tên combo/đồ ăn <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($foodItem['name']); ?>" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="type" class="form-label">Loại <span class="text-danger">*</span></label>
                <select class="form-select" id="type" name="type" required>
                    <option value="combo" <?php echo $foodItem['type'] === 'combo' ? 'selected' : ''; ?>>Combo</option>
                    <option value="snack" <?php echo $foodItem['type'] === 'snack' ? 'selected' : ''; ?>>Snack</option>
                    <option value="drink" <?php echo $foodItem['type'] === 'drink' ? 'selected' : ''; ?>>Đồ uống</option>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo $foodItem['price']; ?>" min="0" step="1000" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="image" class="form-label">Ảnh</label>
                <?php if ($foodItem['image']): ?>
                    <div class="mb-2">
                        <img src="<?php echo htmlspecialchars($foodItem['image']); ?>" 
                             alt="Ảnh hiện tại" 
                             style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <small class="text-muted">Chấp nhận: JPEG, PNG, GIF, WebP (tối đa 5MB). Để trống nếu không muốn thay đổi ảnh.</small>
                <div id="image-preview" class="mt-2" style="display: none;">
                    <img id="preview-img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                </div>
            </div>
            
            <div class="col-12 mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Mô tả chi tiết về combo/đồ ăn..."><?php echo htmlspecialchars($foodItem['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="col-12 mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo $foodItem['is_active'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">
                        Hoạt động (hiển thị cho khách hàng)
                    </label>
                </div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Cập nhật
            </button>
            <a href="?route=admin/foodItems" class="btn btn-secondary">
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


