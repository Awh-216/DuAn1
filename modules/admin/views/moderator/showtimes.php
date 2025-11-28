<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý Lịch chiếu - <?php echo htmlspecialchars($theater['name']); ?></h5>
    <div>
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addScreenModal">
            <i class="fas fa-plus"></i> Thêm phòng
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShowtimeModal">
            <i class="fas fa-plus"></i> Thêm lịch chiếu
        </button>
    </div>
</div>

<!-- Filters -->
<div class="stat-card mb-3">
    <form method="GET" class="row g-2">
        <input type="hidden" name="route" value="moderator/showtimes">
        <div class="col-md-4">
            <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($_GET['date'] ?? date('Y-m-d')); ?>" onchange="this.form.submit()">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="fas fa-search"></i> Lọc
            </button>
        </div>
        <div class="col-md-2">
            <a href="?route=moderator/showtimes" class="btn btn-outline-secondary w-100">
                <i class="fas fa-redo"></i> Xóa lọc
            </a>
        </div>
    </form>
</div>

<!-- Showtimes Table -->
<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Phim</th>
                    <th>Phòng</th>
                    <th>Ngày chiếu</th>
                    <th>Giờ chiếu</th>
                    <th>Giá vé</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($showtimes)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Chưa có lịch chiếu nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($showtimes as $showtime): ?>
                        <tr>
                            <td><?php echo $showtime['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (isset($showtime['thumbnail']) && $showtime['thumbnail']): ?>
                                        <img src="<?php echo htmlspecialchars($showtime['thumbnail']); ?>" alt="" class="rounded me-2" style="width: 50px; height: 75px; object-fit: cover;">
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($showtime['movie_title']); ?></span>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($showtime['screen_name'] ?? 'Phòng ' . $showtime['screen_id']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($showtime['show_date'])); ?></td>
                            <td><?php echo date('H:i', strtotime($showtime['show_time'])); ?></td>
                            <td><?php echo number_format($showtime['price']); ?>₫</td>
                            <td>
                                <?php
                                $showDate = strtotime($showtime['show_date'] . ' ' . $showtime['show_time']);
                                $now = time();
                                if ($showDate < $now) {
                                    echo '<span class="badge bg-secondary">Đã chiếu</span>';
                                } elseif ($showDate - $now < 3600) {
                                    echo '<span class="badge bg-warning">Sắp chiếu</span>';
                                } else {
                                    echo '<span class="badge bg-success">Sắp tới</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editShowtimeModal"
                                        data-id="<?php echo $showtime['id']; ?>"
                                        data-movie-id="<?php echo $showtime['movie_id']; ?>"
                                        data-screen-id="<?php echo $showtime['screen_id']; ?>"
                                        data-show-date="<?php echo $showtime['show_date']; ?>"
                                        data-show-time="<?php echo date('H:i', strtotime($showtime['show_time'])); ?>"
                                        data-price="<?php echo $showtime['price']; ?>">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <a href="?route=moderator/showtimesDelete&id=<?php echo $showtime['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Bạn chắc chắn muốn xóa lịch chiếu này?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Showtime Modal -->
<div class="modal fade" id="addShowtimeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm lịch chiếu mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?route=moderator/showtimesStore">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="movie_id" class="form-label">Phim <span class="text-danger">*</span></label>
                        <select name="movie_id" id="movie_id" class="form-select" required>
                            <option value="">Chọn phim</option>
                            <?php foreach ($movies as $movie): ?>
                                <option value="<?php echo $movie['id']; ?>"><?php echo htmlspecialchars($movie['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="screen_id" class="form-label">Phòng <span class="text-danger">*</span></label>
                        <select name="screen_id" id="screen_id" class="form-select" required>
                            <option value="">Chọn phòng</option>
                            <?php foreach ($screens as $screen): ?>
                                <option value="<?php echo $screen['id']; ?>">
                                    <?php echo htmlspecialchars($screen['screen_name']); ?> 
                                    (<?php echo $screen['total_seats']; ?> ghế, <?php echo htmlspecialchars($screen['screen_type']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="show_date" class="form-label">Ngày chiếu <span class="text-danger">*</span></label>
                        <input type="date" name="show_date" id="show_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="show_time" class="form-label">Giờ chiếu <span class="text-danger">*</span></label>
                        <input type="time" name="show_time" id="show_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Giá vé (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="price" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm lịch chiếu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Showtime Modal -->
<div class="modal fade" id="editShowtimeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa lịch chiếu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?route=moderator/showtimesUpdate">
                <input type="hidden" name="id" id="edit_showtime_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_movie_id" class="form-label">Phim <span class="text-danger">*</span></label>
                        <select name="movie_id" id="edit_movie_id" class="form-select" required>
                            <option value="">Chọn phim</option>
                            <?php foreach ($movies as $movie): ?>
                                <option value="<?php echo $movie['id']; ?>"><?php echo htmlspecialchars($movie['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_screen_id" class="form-label">Phòng <span class="text-danger">*</span></label>
                        <select name="screen_id" id="edit_screen_id" class="form-select" required>
                            <option value="">Chọn phòng</option>
                            <?php foreach ($screens as $screen): ?>
                                <option value="<?php echo $screen['id']; ?>">
                                    <?php echo htmlspecialchars($screen['screen_name']); ?> 
                                    (<?php echo $screen['total_seats']; ?> ghế, <?php echo htmlspecialchars($screen['screen_type']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_show_date" class="form-label">Ngày chiếu <span class="text-danger">*</span></label>
                        <input type="date" name="show_date" id="edit_show_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_show_time" class="form-label">Giờ chiếu <span class="text-danger">*</span></label>
                        <input type="time" name="show_time" id="edit_show_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Giá vé (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="edit_price" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
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
                        <label for="screen_type" class="form-label">Loại màn hình</label>
                        <select name="screen_type" id="screen_type" class="form-select">
                            <option value="2D">2D</option>
                            <option value="3D">3D</option>
                            <option value="IMAX">IMAX</option>
                            <option value="4DX">4DX</option>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý modal sửa lịch chiếu
    const editShowtimeModal = document.getElementById('editShowtimeModal');
    if (editShowtimeModal) {
        editShowtimeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const movieId = button.getAttribute('data-movie-id');
            const screenId = button.getAttribute('data-screen-id');
            const showDate = button.getAttribute('data-show-date');
            const showTime = button.getAttribute('data-show-time');
            const price = button.getAttribute('data-price');
            
            editShowtimeModal.querySelector('#edit_showtime_id').value = id;
            editShowtimeModal.querySelector('#edit_movie_id').value = movieId;
            editShowtimeModal.querySelector('#edit_screen_id').value = screenId;
            editShowtimeModal.querySelector('#edit_show_date').value = showDate;
            editShowtimeModal.querySelector('#edit_show_time').value = showTime;
            editShowtimeModal.querySelector('#edit_price').value = price;
        });
    }
});
</script>
