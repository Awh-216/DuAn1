<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Sửa phim</h5>
    <a href="?route=admin/movies" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="stat-card">
    <form method="POST" action="?route=admin/movies/update" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $movie['id']; ?>">
        
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="title" class="form-label">Tiêu đề phim <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="category_id" class="form-label">Thể loại</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">-- Chọn thể loại --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($movie['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="level" class="form-label">Cấp độ</label>
                <select class="form-select" id="level" name="level">
                    <option value="Free" <?php echo ($movie['level'] == 'Free') ? 'selected' : ''; ?>>Free</option>
                    <option value="Silver" <?php echo ($movie['level'] == 'Silver') ? 'selected' : ''; ?>>Silver</option>
                    <option value="Gold" <?php echo ($movie['level'] == 'Gold') ? 'selected' : ''; ?>>Gold</option>
                    <option value="Premium" <?php echo ($movie['level'] == 'Premium') ? 'selected' : ''; ?>>Premium</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="status_admin" class="form-label">Trạng thái Admin</label>
                <select class="form-select" id="status_admin" name="status_admin">
                    <option value="draft" <?php echo (($movie['status_admin'] ?? 'draft') == 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="scheduled" <?php echo (($movie['status_admin'] ?? '') == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="published" <?php echo (($movie['status_admin'] ?? '') == 'published') ? 'selected' : ''; ?>>Published</option>
                    <option value="archived" <?php echo (($movie['status_admin'] ?? '') == 'archived') ? 'selected' : ''; ?>>Archived</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="type" class="form-label">Loại phim <span class="text-danger">*</span></label>
                <select class="form-select" id="type" name="type" onchange="toggleSeriesSection()">
                    <option value="phimle" <?php echo (($movie['type'] ?? 'phimle') == 'phimle') ? 'selected' : ''; ?>>Phim lẻ</option>
                    <option value="phimbo" <?php echo (($movie['type'] ?? '') == 'phimbo') ? 'selected' : ''; ?>>Phim bộ</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status" onchange="toggleTheaterSection()">
                    <option value="Sắp chiếu" <?php echo ($movie['status'] == 'Sắp chiếu') ? 'selected' : ''; ?>>Sắp chiếu</option>
                    <option value="Chiếu rạp" <?php echo ($movie['status'] == 'Chiếu rạp') ? 'selected' : ''; ?>>Chiếu rạp</option>
                    <option value="Chiếu online" <?php echo ($movie['status'] == 'Chiếu online') ? 'selected' : ''; ?>>Chiếu online</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="duration" class="form-label">Thời lượng (phút)</label>
                <input type="number" class="form-control" id="duration" name="duration" value="<?php echo $movie['duration'] ?? ''; ?>" min="0">
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="rating" class="form-label">Đánh giá (0-10)</label>
                <input type="number" class="form-control" id="rating" name="rating" step="0.1" min="0" max="10" value="<?php echo $movie['rating'] ?? 0; ?>">
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="age_rating" class="form-label">Độ tuổi</label>
                <input type="text" class="form-control" id="age_rating" name="age_rating" value="<?php echo htmlspecialchars($movie['age_rating'] ?? ''); ?>" placeholder="VD: T18, P">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="director" class="form-label">Đạo diễn</label>
                <input type="text" class="form-control" id="director" name="director" value="<?php echo htmlspecialchars($movie['director'] ?? ''); ?>">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="actors" class="form-label">Diễn viên</label>
                <input type="text" class="form-control" id="actors" name="actors" value="<?php echo htmlspecialchars($movie['actors'] ?? ''); ?>" placeholder="VD: Diễn viên 1, Diễn viên 2">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="country" class="form-label">Quốc gia</label>
                <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($movie['country'] ?? ''); ?>" placeholder="VD: Việt Nam, Mỹ">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="language" class="form-label">Ngôn ngữ</label>
                <input type="text" class="form-control" id="language" name="language" value="<?php echo htmlspecialchars($movie['language'] ?? ''); ?>" placeholder="VD: Tiếng Việt, Tiếng Anh">
            </div>
            
            <div class="col-md-12 mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($movie['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="thumbnail" class="form-label">URL Poster/Thumbnail</label>
                <input type="url" class="form-control" id="thumbnail" name="thumbnail" value="<?php echo htmlspecialchars($movie['thumbnail'] ?? ''); ?>" placeholder="https://example.com/poster.jpg">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="banner" class="form-label">URL Banner</label>
                <input type="url" class="form-control" id="banner" name="banner" value="<?php echo htmlspecialchars($movie['banner'] ?? ''); ?>" placeholder="https://example.com/banner.jpg">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="video_file" class="form-label">Video phim</label>
                <input type="file" class="form-control" id="video_file" name="video_file" accept="video/*">
                <small class="text-muted">Chọn file video mới để thay thế (MP4, AVI, MOV, etc.)</small>
                <?php if (!empty($movie['video_url'])): ?>
                    <div class="mt-2">
                        <small class="text-muted">Video hiện tại: 
                            <?php if (strpos($movie['video_url'], 'http') === 0): ?>
                                <a href="<?php echo htmlspecialchars($movie['video_url']); ?>" target="_blank"><?php echo htmlspecialchars($movie['video_url']); ?></a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($movie['video_url']); ?>
                            <?php endif; ?>
                        </small>
                    </div>
                <?php endif; ?>
                <div class="mt-2">
                    <label class="form-label">Hoặc nhập URL video (nếu có)</label>
                    <input type="url" class="form-control" id="video_url" name="video_url" value="<?php echo htmlspecialchars($movie['video_url'] ?? ''); ?>" placeholder="https://example.com/video.mp4">
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="trailer_url" class="form-label">URL Trailer</label>
                <input type="url" class="form-control" id="trailer_url" name="trailer_url" value="<?php echo htmlspecialchars($movie['trailer_url'] ?? ''); ?>" placeholder="https://youtube.com/watch?v=...">
            </div>
        </div>
        
        <!-- Phần quản lý tập phim bộ (hiện khi chọn "Phim bộ") -->
        <div id="seriesSection" style="display: <?php echo (($movie['type'] ?? 'phimle') == 'phimbo') ? 'block' : 'none'; ?>;">
            <hr class="my-4">
            <h6 class="mb-3"><i class="fas fa-list me-2"></i>Quản lý tập phim bộ</h6>
            
            <!-- Hiển thị các tập hiện có -->
            <?php if (!empty($episodes)): ?>
            <div class="mb-4">
                <h6 class="mb-3">Các tập hiện có:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Số tập</th>
                                <th>Tiêu đề</th>
                                <th>URL Video</th>
                                <th>Thời lượng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($episodes as $episode): ?>
                            <tr>
                                <td><?php echo $episode['episode_number']; ?></td>
                                <td><?php echo htmlspecialchars($episode['title'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if (!empty($episode['video_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($episode['video_url']); ?>" target="_blank">Xem video</a>
                                    <?php else: ?>
                                        <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Chưa có video</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $episode['duration'] ? $episode['duration'] . ' phút' : 'N/A'; ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteEpisode(<?php echo $episode['id']; ?>)">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Thêm tập mới -->
            <div class="row mb-3">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Thêm tập mới</label>
                    <div class="border rounded p-3">
                        <div id="episodesContainer">
                            <!-- Các tập mới sẽ được thêm bởi JavaScript -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addEpisodeInput()">
                            <i class="fas fa-plus"></i> Thêm tập
                        </button>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Thêm các tập phim bộ mới. 
                        Mỗi tập cần có số tập (bắt buộc). Video file có thể thêm sau khi cần. 
                        <strong>Danh sách tập sẽ hiển thị ngay sau khi thêm, kể cả khi chưa có video.</strong>
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Phần lịch chiếu rạp (hiện khi chọn "Chiếu rạp") -->
        <div id="theaterScheduleSection" style="display: <?php echo ($movie['status'] === 'Chiếu rạp') ? 'block' : 'none'; ?>;">
            <hr class="my-4">
            <h6 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Lịch chiếu rạp</h6>
            
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Lưu ý:</strong> 
                Nếu bạn cập nhật lịch chiếu, tất cả các suất chiếu cũ sẽ bị xóa và thay thế bằng lịch chiếu mới.
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Chọn rạp <span class="text-danger">*</span></label>
                    <select class="form-select" id="scheduleTheater" name="schedule_theater_id">
                        <option value="">-- Chọn rạp --</option>
                        <?php foreach ($theaters as $theater): ?>
                            <option value="<?php echo $theater['id']; ?>" <?php echo (isset($existingShowtimes) && !empty($existingShowtimes) && $existingShowtimes[0]['theater_id'] == $theater['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($theater['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giá vé mặc định (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="defaultPrice" name="default_price" min="0" step="1000" 
                           value="<?php echo isset($existingShowtimes) && !empty($existingShowtimes) ? $existingShowtimes[0]['price'] : '120000'; ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Từ ngày <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="fromDate" name="from_date" 
                           value="<?php echo isset($existingShowtimes) && !empty($existingShowtimes) ? min(array_column($existingShowtimes, 'show_date')) : ''; ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Đến ngày <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="toDate" name="to_date"
                           value="<?php echo isset($existingShowtimes) && !empty($existingShowtimes) ? max(array_column($existingShowtimes, 'show_date')) : ''; ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Chọn khung giờ chiếu <span class="text-danger">*</span></label>
                    <div class="border rounded p-3">
                        <div class="row" id="timeSlotsContainer">
                            <!-- Các khung giờ sẽ được thêm bởi JavaScript -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addTimeSlot()">
                            <i class="fas fa-plus"></i> Thêm khung giờ
                        </button>
                    </div>
                    <small class="text-muted">Chọn các khung giờ sẽ áp dụng cho tất cả các ngày trong khoảng thời gian đã chọn</small>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Phòng chiếu (tùy chọn)</label>
                    <input type="number" class="form-control" id="screenId" name="screen_id" min="1" 
                           value="<?php echo isset($existingShowtimes) && !empty($existingShowtimes) && !empty($existingShowtimes[0]['screen_id']) ? $existingShowtimes[0]['screen_id'] : ''; ?>" 
                           placeholder="Để trống nếu không cần chỉ định phòng">
                    <small class="text-muted">Số phòng chiếu sẽ áp dụng cho tất cả các suất chiếu</small>
                </div>
            </div>
            
            <div class="alert alert-info" id="schedulePreview" style="display: none;">
                <strong><i class="fas fa-info-circle"></i> Xem trước:</strong>
                <div id="previewContent"></div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="?route=admin/movies" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Cập nhật
            </button>
        </div>
    </form>
</div>

<script>
let timeSlotCount = 0;
let episodeCount = 0;
const defaultTimeSlots = ['10:00', '14:00', '18:00', '20:30'];

function toggleSeriesSection() {
    const type = document.getElementById('type').value;
    const section = document.getElementById('seriesSection');
    
    if (type === 'phimbo') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

function addEpisodeInput() {
    episodeCount++;
    const container = document.getElementById('episodesContainer');
    const existingEpisodes = <?php echo json_encode(array_column($episodes ?? [], 'episode_number')); ?>;
    const maxEpisode = existingEpisodes.length > 0 ? Math.max(...existingEpisodes) : 0;
    const nextEpisode = maxEpisode + episodeCount;
    
    const episodeDiv = document.createElement('div');
    episodeDiv.className = 'row mb-3 episode-item';
    episodeDiv.id = 'episode-' + episodeCount;
    
    episodeDiv.innerHTML = `
        <div class="col-md-2">
            <label class="form-label">Số tập <span class="text-danger">*</span></label>
            <input type="number" class="form-control episode-number" name="episodes[${episodeCount}][episode_number]" 
                   value="${nextEpisode}" min="1" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Tiêu đề tập</label>
            <input type="text" class="form-control" name="episodes[${episodeCount}][title]" 
                   placeholder="VD: Tập ${nextEpisode}: Tên tập">
        </div>
        <div class="col-md-4">
            <label class="form-label">Video File</label>
            <input type="file" class="form-control" name="episodes[${episodeCount}][video_file]" 
                   accept="video/*">
            <small class="text-muted">Chọn file video (MP4, AVI, MOV, etc.). Có thể thêm video sau.</small>
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-outline-danger w-100" onclick="removeEpisode(${episodeCount})">
                <i class="fas fa-times"></i> Xóa
            </button>
        </div>
    `;
    
    container.appendChild(episodeDiv);
}

function removeEpisode(id) {
    const episode = document.getElementById('episode-' + id);
    if (episode) {
        episode.remove();
    }
}

function deleteEpisode(episodeId) {
    if (confirm('Bạn có chắc chắn muốn xóa tập này?')) {
        window.location.href = '?route=admin/movies/delete-episode&id=' + episodeId + '&movie_id=<?php echo $movie['id']; ?>';
    }
}
const existingTimes = <?php echo isset($existingShowtimes) && !empty($existingShowtimes) ? json_encode(array_unique(array_column($existingShowtimes, 'show_time'))) : '[]'; ?>;

function toggleTheaterSection() {
    const status = document.getElementById('status').value;
    const section = document.getElementById('theaterScheduleSection');
    const scheduleTheater = document.getElementById('scheduleTheater');
    const defaultPrice = document.getElementById('defaultPrice');
    const fromDate = document.getElementById('fromDate');
    const toDate = document.getElementById('toDate');
    const timeInputs = document.querySelectorAll('input[name="showtimes_time[]"]');
    
    if (status === 'Chiếu rạp') {
        section.style.display = 'block';
        // Thêm required cho các trường khi hiển thị
        if (scheduleTheater) scheduleTheater.setAttribute('required', 'required');
        if (defaultPrice) defaultPrice.setAttribute('required', 'required');
        if (fromDate) fromDate.setAttribute('required', 'required');
        if (toDate) toDate.setAttribute('required', 'required');
        timeInputs.forEach(input => input.setAttribute('required', 'required'));
        
        if (timeSlotCount === 0) {
            // Nếu có showtimes cũ, load các giờ đó, nếu không thì dùng mặc định
            if (existingTimes.length > 0) {
                existingTimes.forEach(function(time) {
                    addTimeSlot(time);
                });
            } else {
                defaultTimeSlots.forEach(function(time) {
                    addTimeSlot(time);
                });
            }
        }
        updateSchedulePreview();
    } else {
        section.style.display = 'none';
        // Xóa required khi ẩn phần này
        if (scheduleTheater) scheduleTheater.removeAttribute('required');
        if (defaultPrice) defaultPrice.removeAttribute('required');
        if (fromDate) fromDate.removeAttribute('required');
        if (toDate) toDate.removeAttribute('required');
        timeInputs.forEach(input => input.removeAttribute('required'));
    }
}

function addTimeSlot(time = '') {
    timeSlotCount++;
    const container = document.getElementById('timeSlotsContainer');
    const section = document.getElementById('theaterScheduleSection');
    const isVisible = section && section.style.display !== 'none';
    
    const col = document.createElement('div');
    col.className = 'col-md-3 mb-2';
    col.id = 'timeslot-' + timeSlotCount;
    
    const requiredAttr = isVisible ? 'required' : '';
    col.innerHTML = `
        <div class="input-group">
            <input type="time" class="form-control" name="showtimes_time[]" value="${time}" ${requiredAttr}
                   onchange="updateSchedulePreview()">
            <button type="button" class="btn btn-outline-danger" onclick="removeTimeSlot(${timeSlotCount})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    container.appendChild(col);
    updateSchedulePreview();
}

function removeTimeSlot(id) {
    const slot = document.getElementById('timeslot-' + id);
    if (slot) {
        slot.remove();
        updateSchedulePreview();
    }
}

function updateSchedulePreview() {
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;
    const timeInputs = document.querySelectorAll('input[name="showtimes_time[]"]');
    const times = Array.from(timeInputs).map(input => input.value).filter(v => v);
    
    const preview = document.getElementById('schedulePreview');
    const previewContent = document.getElementById('previewContent');
    
    if (fromDate && toDate && times.length > 0) {
        // Tính số ngày
        const from = new Date(fromDate);
        const to = new Date(toDate);
        const daysDiff = Math.ceil((to - from) / (1000 * 60 * 60 * 24)) + 1;
        const totalShowtimes = daysDiff * times.length;
        
        previewContent.innerHTML = `
            <p class="mb-1">Sẽ tạo <strong>${totalShowtimes}</strong> suất chiếu:</p>
            <ul class="mb-0">
                <li>Khoảng thời gian: ${formatDate(fromDate)} - ${formatDate(toDate)} (${daysDiff} ngày)</li>
                <li>Khung giờ: ${times.join(', ')}</li>
                <li>Tổng: ${daysDiff} ngày × ${times.length} khung giờ = ${totalShowtimes} suất chiếu</li>
            </ul>
        `;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

// Set ngày mặc định là hôm nay cho fromDate
document.addEventListener('DOMContentLoaded', function() {
    const fromDateInput = document.getElementById('fromDate');
    const toDateInput = document.getElementById('toDate');
    const today = new Date().toISOString().split('T')[0];
    
    <?php if (isset($existingShowtimes) && !empty($existingShowtimes)): ?>
        // Nếu có showtimes cũ, set từ ngày và đến ngày
        const existingDates = <?php echo json_encode(array_column($existingShowtimes, 'show_date')); ?>;
        if (existingDates.length > 0) {
            existingDates.sort();
            const minDate = existingDates[0];
            const maxDate = existingDates[existingDates.length - 1];
            
            if (fromDateInput) {
                fromDateInput.value = minDate;
                fromDateInput.min = today;
            }
            if (toDateInput) {
                toDateInput.value = maxDate;
                if (fromDateInput && fromDateInput.value) {
                    toDateInput.min = fromDateInput.value;
                } else {
                    toDateInput.min = today;
                }
            }
        }
    <?php else: ?>
        if (fromDateInput) {
            fromDateInput.value = today;
            fromDateInput.min = today;
        }
    <?php endif; ?>
    
    if (fromDateInput) {
        fromDateInput.addEventListener('change', function() {
            if (toDateInput && this.value) {
                toDateInput.min = this.value;
            }
            updateSchedulePreview();
        });
    }
    
    if (toDateInput) {
        toDateInput.addEventListener('change', updateSchedulePreview);
    }
    
    const defaultPrice = document.getElementById('defaultPrice');
    if (defaultPrice) {
        defaultPrice.addEventListener('change', updateSchedulePreview);
    }
    
    toggleTheaterSection();
    toggleSeriesSection();
});
</script>

