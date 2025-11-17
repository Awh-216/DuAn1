<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Thêm phim mới</h5>
    <a href="?route=admin/movies" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="stat-card">
    <form method="POST" action="?route=admin/movies/store" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="title" class="form-label">Tiêu đề phim <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="category_id" class="form-label">Thể loại</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">-- Chọn thể loại --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="level" class="form-label">Cấp độ</label>
                <select class="form-select" id="level" name="level">
                    <option value="Free">Free</option>
                    <option value="Silver">Silver</option>
                    <option value="Gold">Gold</option>
                    <option value="Premium">Premium</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="status_admin" class="form-label">Trạng thái Admin</label>
                <select class="form-select" id="status_admin" name="status_admin">
                    <option value="draft">Draft</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="type" class="form-label">Loại phim <span class="text-danger">*</span></label>
                <select class="form-select" id="type" name="type" onchange="toggleSeriesSection()">
                    <option value="phimle">Phim lẻ</option>
                    <option value="phimbo">Phim bộ</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status" onchange="toggleTheaterSection()">
                    <option value="Sắp chiếu">Sắp chiếu</option>
                    <option value="Chiếu rạp">Chiếu rạp</option>
                    <option value="Chiếu online">Chiếu online</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="duration" class="form-label">Thời lượng (phút)</label>
                <input type="number" class="form-control" id="duration" name="duration" min="0">
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="rating" class="form-label">Đánh giá (0-10)</label>
                <input type="number" class="form-control" id="rating" name="rating" step="0.1" min="0" max="10" value="0">
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="age_rating" class="form-label">Độ tuổi</label>
                <input type="text" class="form-control" id="age_rating" name="age_rating" placeholder="VD: T18, P">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="director" class="form-label">Đạo diễn</label>
                <input type="text" class="form-control" id="director" name="director">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="actors" class="form-label">Diễn viên</label>
                <input type="text" class="form-control" id="actors" name="actors" placeholder="VD: Diễn viên 1, Diễn viên 2">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="country" class="form-label">Quốc gia</label>
                <input type="text" class="form-control" id="country" name="country" placeholder="VD: Việt Nam, Mỹ">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="language" class="form-label">Ngôn ngữ</label>
                <input type="text" class="form-control" id="language" name="language" placeholder="VD: Tiếng Việt, Tiếng Anh">
            </div>
            
            <div class="col-md-12 mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="thumbnail" class="form-label">URL Poster/Thumbnail</label>
                <input type="url" class="form-control" id="thumbnail" name="thumbnail" placeholder="https://example.com/poster.jpg">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="banner" class="form-label">URL Banner</label>
                <input type="url" class="form-control" id="banner" name="banner" placeholder="https://example.com/banner.jpg">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="video_url" class="form-label">URL Video phim</label>
                <input type="url" class="form-control" id="video_url" name="video_url" placeholder="https://example.com/video.mp4">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="trailer_url" class="form-label">URL Trailer</label>
                <input type="url" class="form-control" id="trailer_url" name="trailer_url" placeholder="https://youtube.com/watch?v=...">
            </div>
        </div>
        
        <!-- Phần quản lý tập phim bộ (hiện khi chọn "Phim bộ") -->
        <div id="seriesSection" style="display: none;">
            <hr class="my-4">
            <h6 class="mb-3"><i class="fas fa-list me-2"></i>Quản lý tập phim bộ</h6>
            
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label for="total_episodes" class="form-label">Tổng số tập <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="total_episodes" name="total_episodes" min="1" value="1">
                    <small class="text-muted">Số tập dự kiến của phim bộ</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="current_episode" class="form-label">Tập hiện tại <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="current_episode" name="current_episode" min="1" value="1">
                    <small class="text-muted">Tập mới nhất đã phát hành</small>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Thêm tập mới</label>
                    <div class="border rounded p-3">
                        <div id="episodesContainer">
                            <!-- Các tập sẽ được thêm bởi JavaScript -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addEpisodeInput()">
                            <i class="fas fa-plus"></i> Thêm tập
                        </button>
                    </div>
                    <small class="text-muted">Thêm các tập phim bộ. Mỗi tập cần có số tập, tiêu đề và file video.</small>
                </div>
            </div>
        </div>
        
        <!-- Phần lịch chiếu rạp (hiện khi chọn "Chiếu rạp") -->
        <div id="theaterScheduleSection" style="display: none;">
            <hr class="my-4">
            <h6 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Lịch chiếu rạp</h6>
            
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Chọn rạp <span class="text-danger">*</span></label>
                    <select class="form-select" id="scheduleTheater" name="schedule_theater_id">
                        <option value="">-- Chọn rạp --</option>
                        <?php foreach ($theaters as $theater): ?>
                            <option value="<?php echo $theater['id']; ?>"><?php echo htmlspecialchars($theater['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giá vé mặc định (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="defaultPrice" name="default_price" min="0" step="1000" value="120000">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Từ ngày <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="fromDate" name="from_date">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Đến ngày <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="toDate" name="to_date">
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
                    <input type="number" class="form-control" id="screenId" name="screen_id" min="1" placeholder="Để trống nếu không cần chỉ định phòng">
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
                <i class="fas fa-save"></i> Lưu
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
    const totalEpisodes = document.getElementById('total_episodes');
    const currentEpisode = document.getElementById('current_episode');
    
    if (type === 'phimbo') {
        section.style.display = 'block';
        if (totalEpisodes) totalEpisodes.setAttribute('required', 'required');
        if (currentEpisode) currentEpisode.setAttribute('required', 'required');
    } else {
        section.style.display = 'none';
        if (totalEpisodes) totalEpisodes.removeAttribute('required');
        if (currentEpisode) currentEpisode.removeAttribute('required');
    }
}

function addEpisodeInput() {
    episodeCount++;
    const container = document.getElementById('episodesContainer');
    const currentEpisode = parseInt(document.getElementById('current_episode')?.value || 1);
    const nextEpisode = currentEpisode + episodeCount;
    
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
            <label class="form-label">Video File <span class="text-danger">*</span></label>
            <input type="file" class="form-control" name="episodes[${episodeCount}][video_file]" 
                   accept="video/*" required>
            <small class="text-muted">Chọn file video (MP4, AVI, MOV, etc.)</small>
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
            // Thêm các khung giờ mặc định
            defaultTimeSlots.forEach(function(time) {
                addTimeSlot(time);
            });
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
    
    if (fromDateInput) {
        fromDateInput.value = today;
        fromDateInput.min = today;
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

