<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Import tập phim từ folder</h5>
    <a href="?route=admin/movies" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<?php if (empty($folders)): ?>
    <div class="stat-card">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Không tìm thấy folder nào trong <code>data/phim/phimbo/</code>.
            <br>Vui lòng tạo folder và thêm các file video vào đó.
        </div>
    </div>
<?php else: ?>
    <?php foreach ($folders as $folder): ?>
        <div class="stat-card mb-3">
            <h6 class="mb-3">
                <i class="fas fa-folder"></i> Folder: <strong><?php echo htmlspecialchars($folder['name']); ?></strong>
                <span class="badge bg-primary ms-2"><?php echo $folder['count']; ?> file</span>
            </h6>
            
            <form method="POST" action="?route=admin/movies/importEpisodes" id="importForm_<?php echo htmlspecialchars($folder['name']); ?>">
                <input type="hidden" name="folder_name" value="<?php echo htmlspecialchars($folder['name']); ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Chọn phim bộ <span class="text-danger">*</span></label>
                        <select name="movie_id" class="form-select" required>
                            <option value="">-- Chọn phim --</option>
                            <?php foreach ($movies as $movie): ?>
                                <option value="<?php echo $movie['id']; ?>">
                                    <?php echo htmlspecialchars($movie['title']); ?> (ID: <?php echo $movie['id']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Chọn phim bộ để import các tập</small>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">Import</th>
                                <th width="100">Số tập</th>
                                <th>Tên file</th>
                                <th width="120">Kích thước</th>
                                <th width="100">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($folder['files'] as $index => $file): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" 
                                               name="files[<?php echo $index; ?>][import]" 
                                               value="1" 
                                               checked 
                                               onchange="toggleEpisodeNumber(<?php echo $index; ?>, this.checked)">
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="files[<?php echo $index; ?>][episode_number]" 
                                               class="form-control form-control-sm" 
                                               min="1" 
                                               value="<?php 
                                                   // Cố gắng extract số tập từ tên file (ví dụ: tap1, tap_1, episode_1, etc.)
                                                   $filename = strtolower($file['name']);
                                                   if (preg_match('/(?:tap|episode|ep)[_ ]?(\d+)/i', $filename, $matches)) {
                                                       echo intval($matches[1]);
                                                   } else {
                                                       echo ($index + 1);
                                                   }
                                               ?>" 
                                               required>
                                    </td>
                                    <td>
                                        <input type="hidden" name="files[<?php echo $index; ?>][path]" value="<?php echo htmlspecialchars($file['path']); ?>">
                                        <code><?php echo htmlspecialchars($file['name']); ?></code>
                                    </td>
                                    <td>
                                        <?php 
                                        $size = $file['size'];
                                        if ($size < 1024) {
                                            echo $size . ' B';
                                        } elseif ($size < 1024 * 1024) {
                                            echo number_format($size / 1024, 2) . ' KB';
                                        } else {
                                            echo number_format($size / (1024 * 1024), 2) . ' MB';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editEpisodeTitle(<?php echo $index; ?>)">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                    </td>
                                </tr>
                                <tr id="titleRow_<?php echo $index; ?>" style="display: none;">
                                    <td colspan="5">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Tên tập:</span>
                                            <input type="text" 
                                                   name="files[<?php echo $index; ?>][title]" 
                                                   class="form-control" 
                                                   placeholder="Ví dụ: Tập 1 - Khởi đầu" 
                                                   id="titleInput_<?php echo $index; ?>">
                                            <button type="button" class="btn btn-outline-secondary" onclick="saveEpisodeTitle(<?php echo $index; ?>)">
                                                <i class="fas fa-check"></i> Lưu
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import các tập đã chọn
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="selectAllFiles('<?php echo htmlspecialchars($folder['name']); ?>')">
                        <i class="fas fa-check-square"></i> Chọn tất cả
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="deselectAllFiles('<?php echo htmlspecialchars($folder['name']); ?>')">
                        <i class="fas fa-square"></i> Bỏ chọn tất cả
                    </button>
                </div>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
function toggleEpisodeNumber(index, checked) {
    const numberInput = document.querySelector(`input[name="files[${index}][episode_number]"]`);
    if (numberInput) {
        numberInput.disabled = !checked;
        numberInput.required = checked;
    }
}

function editEpisodeTitle(index) {
    const titleRow = document.getElementById(`titleRow_${index}`);
    const titleInput = document.getElementById(`titleInput_${index}`);
    if (titleRow && titleInput) {
        titleRow.style.display = titleRow.style.display === 'none' ? 'table-row' : 'none';
        if (titleRow.style.display !== 'none') {
            titleInput.focus();
        }
    }
}

function saveEpisodeTitle(index) {
    editEpisodeTitle(index); // Ẩn form sau khi lưu
}

function selectAllFiles(folderName) {
    const form = document.getElementById(`importForm_${folderName}`);
    if (form) {
        const checkboxes = form.querySelectorAll('input[type="checkbox"][name*="[import]"]');
        checkboxes.forEach(cb => {
            cb.checked = true;
            toggleEpisodeNumber(parseInt(cb.name.match(/\[(\d+)\]/)[1]), true);
        });
    }
}

function deselectAllFiles(folderName) {
    const form = document.getElementById(`importForm_${folderName}`);
    if (form) {
        const checkboxes = form.querySelectorAll('input[type="checkbox"][name*="[import]"]');
        checkboxes.forEach(cb => {
            cb.checked = false;
            toggleEpisodeNumber(parseInt(cb.name.match(/\[(\d+)\]/)[1]), false);
        });
    }
}

// Khởi tạo trạng thái ban đầu
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name*="[import]"]');
    checkboxes.forEach(cb => {
        toggleEpisodeNumber(parseInt(cb.name.match(/\[(\d+)\]/)[1]), cb.checked);
    });
});
</script>

