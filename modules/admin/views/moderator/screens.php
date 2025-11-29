<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý phòng chiếu - <?php echo htmlspecialchars($theater['name']); ?></h5>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScreenModal">
        <i class="fas fa-plus"></i> Thêm phòng mới
    </button>
</div>

<!-- Filter -->
<div class="stat-card mb-3">
    <form method="GET" class="row g-2">
        <input type="hidden" name="route" value="moderator/screens">
        <div class="col-md-4">
            <label for="movie_filter" class="form-label">Lọc theo phim</label>
            <select name="movie_id" id="movie_filter" class="form-select" onchange="this.form.submit()">
                <option value="">Tất cả phòng</option>
                <?php foreach ($movies as $movie): ?>
                    <option value="<?php echo $movie['id']; ?>" <?php echo (isset($_GET['movie_id']) && $_GET['movie_id'] == $movie['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($movie['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <a href="?route=moderator/screens" class="btn btn-outline-secondary w-100">
                <i class="fas fa-redo"></i> Xóa lọc
            </a>
        </div>
    </form>
</div>

<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên phòng</th>
                    <th>Loại phòng</th>
                    <th>Số ghế</th>
                    <th>Phim đang chiếu</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($screens)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Chưa có phòng chiếu nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($screens as $screen): ?>
                        <tr>
                            <td><?php echo $screen['id']; ?></td>
                            <td><?php echo htmlspecialchars($screen['screen_name']); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($screen['screen_type'] ?? '2D'); ?></span>
                            </td>
                            <td><?php echo $screen['total_seats']; ?> ghế</td>
                            <td>
                                <?php if (!empty($screen['current_movies']) && is_array($screen['current_movies']) && count($screen['current_movies']) > 0): ?>
                                    <div class="d-flex flex-column gap-1">
                                        <?php foreach ($screen['current_movies'] as $movie): ?>
                                            <span class="badge bg-primary">
                                                <?php echo htmlspecialchars($movie['title'] ?? ''); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Chưa có phim</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($screen['is_active']): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tạm dừng</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="?route=moderator/screenEdit&id=<?php echo $screen['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-cog"></i> Layout
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#manageMoviesModal"
                                            data-screen-id="<?php echo $screen['id']; ?>"
                                            data-screen-name="<?php echo htmlspecialchars($screen['screen_name']); ?>"
                                            onclick="loadScreenMovies(<?php echo $screen['id']; ?>, '<?php echo htmlspecialchars($screen['screen_name']); ?>')">
                                        <i class="fas fa-film"></i> Quản lý phim
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Screen Modal -->
<div class="modal fade" id="addScreenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm phòng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?route=moderator/screensStore">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="screen_name" class="form-label">Tên phòng <span class="text-danger">*</span></label>
                        <input type="text" name="screen_name" id="screen_name" class="form-control" required placeholder="Ví dụ: Phòng 1, Phòng VIP">
                    </div>
                    <div class="mb-3">
                        <label for="total_seats" class="form-label">Số ghế <span class="text-danger">*</span></label>
                        <input type="number" name="total_seats" id="total_seats" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="screen_type" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                        <select name="screen_type" id="screen_type" class="form-select" required>
                            <option value="2D">2D</option>
                            <option value="3D">3D</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm phòng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Movies Modal -->
<div class="modal fade" id="manageMoviesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quản lý phim - <span id="modalScreenName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="currentScreenId" value="">
                
                <!-- Add Movie Form -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-plus"></i> Thêm phim vào phòng</h6>
                    </div>
                    <div class="card-body">
                        <form id="addMovieToScreenForm" method="POST" action="?route=moderator/screenAddMovie">
                            <input type="hidden" name="screen_id" id="addMovieScreenId">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="add_movie_id" class="form-label">Chọn phim <span class="text-danger">*</span></label>
                                    <select name="movie_id" id="add_movie_id" class="form-select" required>
                                        <option value="">-- Chọn phim --</option>
                                        <?php foreach ($movies as $movie): ?>
                                            <option value="<?php echo $movie['id']; ?>">
                                                <?php echo htmlspecialchars($movie['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="add_price" class="form-label">Giá vé (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="add_price" class="form-control" min="0" value="120000" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="add_from_date" class="form-label">Từ ngày <span class="text-danger">*</span></label>
                                    <input type="date" name="from_date" id="add_from_date" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="add_to_date" class="form-label">Đến ngày <span class="text-danger">*</span></label>
                                    <input type="date" name="to_date" id="add_to_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Khung giờ chiếu <span class="text-danger">*</span></label>
                                <div class="border rounded p-3">
                                    <div class="row" id="addTimeSlotsContainer">
                                        <!-- Time slots will be added here -->
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addTimeSlotToForm()">
                                        <i class="fas fa-plus"></i> Thêm khung giờ
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm lịch chiếu
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Current Movies List -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-list"></i> Phim đang chiếu trong phòng</h6>
                    </div>
                    <div class="card-body">
                        <div id="currentMoviesList">
                            <p class="text-muted text-center">Đang tải...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
let timeSlotCount = 0;
const defaultTimeSlots = ['10:00', '14:00', '18:00', '20:30'];

function loadScreenMovies(screenId, screenName) {
    document.getElementById('currentScreenId').value = screenId;
    document.getElementById('addMovieScreenId').value = screenId;
    document.getElementById('modalScreenName').textContent = screenName;
    
    // Reset form
    document.getElementById('addMovieToScreenForm').reset();
    document.getElementById('addMovieScreenId').value = screenId;
    timeSlotCount = 0;
    document.getElementById('addTimeSlotsContainer').innerHTML = '';
    
    // Add default time slots
    defaultTimeSlots.forEach(function(time) {
        addTimeSlotToForm(time);
    });
    
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('add_from_date').value = today;
    document.getElementById('add_from_date').min = today;
    document.getElementById('add_to_date').min = today;
    
    // Load current movies
    fetch('?route=moderator/screenMovies&screen_id=' + screenId)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('currentMoviesList');
            if (data.success && data.movies && data.movies.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Phim</th><th>Từ ngày</th><th>Đến ngày</th><th>Số suất</th><th>Thao tác</th></tr></thead><tbody>';
                
                data.movies.forEach(function(movie) {
                    html += '<tr>';
                    html += '<td><strong>' + escapeHtml(movie.title) + '</strong></td>';
                    html += '<td>' + formatDate(movie.from_date) + '</td>';
                    html += '<td>' + formatDate(movie.to_date) + '</td>';
                    html += '<td>' + movie.showtime_count + ' suất</td>';
                    html += '<td>';
                    html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMovieFromScreen(' + screenId + ', ' + movie.id + ', \'' + escapeHtml(movie.title) + '\')">';
                    html += '<i class="fas fa-trash"></i> Xóa lịch chiếu';
                    html += '</button>';
                    html += '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-muted text-center">Chưa có phim nào trong phòng này</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('currentMoviesList').innerHTML = '<p class="text-danger text-center">Lỗi khi tải dữ liệu</p>';
        });
}

function addTimeSlotToForm(time = '') {
    timeSlotCount++;
    const container = document.getElementById('addTimeSlotsContainer');
    const col = document.createElement('div');
    col.className = 'col-md-3 mb-2';
    col.id = 'timeslot-' + timeSlotCount;
    col.innerHTML = `
        <div class="input-group">
            <input type="time" class="form-control" name="showtimes_time[]" value="${time}" required>
            <button type="button" class="btn btn-outline-danger" onclick="removeTimeSlotFromForm(${timeSlotCount})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(col);
}

function removeTimeSlotFromForm(id) {
    const slot = document.getElementById('timeslot-' + id);
    if (slot) {
        slot.remove();
    }
}

function removeMovieFromScreen(screenId, movieId, movieTitle) {
    if (!confirm('Bạn có chắc chắn muốn xóa tất cả lịch chiếu của phim "' + movieTitle + '" khỏi phòng này?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '?route=moderator/screenRemoveMovie';
    
    const screenInput = document.createElement('input');
    screenInput.type = 'hidden';
    screenInput.name = 'screen_id';
    screenInput.value = screenId;
    
    const movieInput = document.createElement('input');
    movieInput.type = 'hidden';
    movieInput.name = 'movie_id';
    movieInput.value = movieId;
    
    form.appendChild(screenInput);
    form.appendChild(movieInput);
    document.body.appendChild(form);
    form.submit();
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

// Set min date for to_date when from_date changes
document.addEventListener('DOMContentLoaded', function() {
    const fromDateInput = document.getElementById('add_from_date');
    const toDateInput = document.getElementById('add_to_date');
    
    if (fromDateInput && toDateInput) {
        fromDateInput.addEventListener('change', function() {
            if (this.value) {
                toDateInput.min = this.value;
            }
        });
    }
});
</script>
