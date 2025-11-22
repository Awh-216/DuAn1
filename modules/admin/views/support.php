<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Hỗ trợ khách hàng</h5>
</div>

<!-- Filters -->
<form method="GET" class="mb-3">
    <input type="hidden" name="route" value="admin/support">
    <div class="row g-2">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="Mới" <?php echo ($status ?? '') === 'Mới' ? 'selected' : ''; ?>>Mới</option>
                <option value="Đang xử lý" <?php echo ($status ?? '') === 'Đang xử lý' ? 'selected' : ''; ?>>Đang xử lý</option>
                <option value="Đã giải quyết" <?php echo ($status ?? '') === 'Đã giải quyết' ? 'selected' : ''; ?>>Đã giải quyết</option>
                <option value="Đã đóng" <?php echo ($status ?? '') === 'Đã đóng' ? 'selected' : ''; ?>>Đã đóng</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Tất cả phạm vi hỗ trợ</option>
                <option value="Mua bán vé" <?php echo ($category ?? '') === 'Mua bán vé' ? 'selected' : ''; ?>>Mua bán vé</option>
                <option value="Lỗi mua bán vé" <?php echo ($category ?? '') === 'Lỗi mua bán vé' ? 'selected' : ''; ?>>Lỗi mua bán vé</option>
                <option value="Lỗi về phim" <?php echo ($category ?? '') === 'Lỗi về phim' ? 'selected' : ''; ?>>Lỗi về phim</option>
                <option value="Đăng nhập/Đăng xuất" <?php echo ($category ?? '') === 'Đăng nhập/Đăng xuất' ? 'selected' : ''; ?>>Đăng nhập/Đăng xuất</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="fas fa-filter"></i> Lọc
            </button>
        </div>
    </div>
</form>

<!-- Tickets Table -->
<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Người dùng</th>
                    <th>Gói thành viên</th>
                    <th>Tiêu đề</th>
                    <th>Trạng thái</th>
                    <th>Phạm vi hỗ trợ</th>
                    <th>Người xử lý</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">Không có ticket nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo $ticket['id']; ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($ticket['user_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($ticket['user_email']); ?></small>
                            </td>
                            <td>
                                <?php 
                                $subscriptionName = $ticket['subscription_name'] ?? 'Free';
                                $subscriptionClass = match($subscriptionName) {
                                    'Premium' => 'danger',
                                    'Gold' => 'warning',
                                    'Silver' => 'info',
                                    'Basic' => 'secondary',
                                    default => 'dark'
                                };
                                ?>
                                <span class="badge bg-<?php echo $subscriptionClass; ?>">
                                    <?php echo htmlspecialchars($subscriptionName); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($ticket['status']) {
                                        'Mới' => 'primary',
                                        'Đang xử lý' => 'warning',
                                        'Đã giải quyết' => 'success',
                                        default => 'secondary'
                                    };
                                ?> status-badge-<?php echo $ticket['id']; ?>" id="status-badge-<?php echo $ticket['id']; ?>">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                // Extract category từ tags
                                $category = 'Khác';
                                $tags = $ticket['tags'] ?? '';
                                
                                if (!empty($tags)) {
                                    // Kiểm tra Lỗi mua bán vé trước (vì nó chứa cả "Mua bán vé" và "Lỗi")
                                    if ((stripos($tags, 'Đặt vé') !== false || stripos($tags, 'Mua bán vé') !== false) && 
                                        (stripos($tags, 'Lỗi') !== false || stripos($tags, 'Lỗi thanh toán') !== false || 
                                         stripos($tags, 'Không nhận được vé') !== false || stripos($tags, 'Vấn đề về ghế ngồi') !== false)) {
                                        $category = 'Lỗi mua bán vé';
                                    } elseif (stripos($tags, 'Đặt vé') !== false || stripos($tags, 'Mua bán vé') !== false) {
                                        $category = 'Mua bán vé';
                                    } elseif (stripos($tags, 'Phim') !== false && stripos($tags, 'Lỗi') !== false) {
                                        $category = 'Lỗi về phim';
                                    } elseif (stripos($tags, 'Đăng nhập') !== false || stripos($tags, 'Đăng xuất') !== false) {
                                        $category = 'Đăng nhập/Đăng xuất';
                                    }
                                }
                                
                                $categoryClass = match($category) {
                                    'Lỗi mua bán vé' => 'danger',
                                    'Mua bán vé' => 'warning',
                                    'Lỗi về phim' => 'info',
                                    'Đăng nhập/Đăng xuất' => 'primary',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?php echo $categoryClass; ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['assigned_name'] ?? 'Chưa gán'); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?></td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="?route=admin/support/view&id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($ticket['status'] !== 'Đã giải quyết'): ?>
                                        <?php if ($ticket['status'] !== 'Đang xử lý'): ?>
                                            <form method="POST" action="?route=admin/support/update-status" style="display: inline;" class="status-update-form" onsubmit="return handleStatusUpdate(this, 'Đang xử lý');">
                                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                                <input type="hidden" name="status" value="Đang xử lý">
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Đang giải quyết">
                                                    <i class="fas fa-spinner"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="?route=admin/support/update-status" style="display: inline;" class="status-update-form" onsubmit="return handleStatusUpdate(this, 'Đã giải quyết');">
                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                            <input type="hidden" name="status" value="Đã giải quyết">
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Đã giải quyết">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="openReplyModal(<?php echo $ticket['id']; ?>, '<?php echo htmlspecialchars($ticket['user_email'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($ticket['subject'], ENT_QUOTES); ?>')" title="Phản hồi">
                                        <i class="fas fa-reply"></i>
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

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel">
                    <i class="fas fa-reply me-2"></i>Phản hồi khách hàng
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="?route=admin/support/reply">
                <div class="modal-body">
                    <input type="hidden" name="ticket_id" id="reply_ticket_id">
                    <div class="mb-3">
                        <label class="form-label">Gửi đến:</label>
                        <input type="email" class="form-control" id="reply_user_email" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề ticket:</label>
                        <input type="text" class="form-control" id="reply_ticket_subject" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="reply_message" class="form-label">Nội dung phản hồi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reply_message" name="reply_message" rows="6" required placeholder="Nhập nội dung phản hồi..."></textarea>
                        <small class="text-muted">Nội dung này sẽ được gửi đến email của khách hàng.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Gửi phản hồi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReplyModal(ticketId, userEmail, ticketSubject) {
    document.getElementById('reply_ticket_id').value = ticketId;
    document.getElementById('reply_user_email').value = userEmail;
    document.getElementById('reply_ticket_subject').value = ticketSubject;
    document.getElementById('reply_message').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('replyModal'));
    modal.show();
}

function handleStatusUpdate(form, newStatus) {
    const ticketId = form.querySelector('input[name="ticket_id"]').value;
    const statusBadge = document.getElementById('status-badge-' + ticketId);
    
    if (!confirm('Xác nhận chuyển ticket sang trạng thái "' + newStatus + '"?')) {
        return false;
    }
    
    // Cập nhật UI ngay lập tức
    if (statusBadge) {
        statusBadge.textContent = newStatus;
        statusBadge.className = 'badge status-badge-' + ticketId;
        if (newStatus === 'Đang xử lý') {
            statusBadge.classList.add('bg-warning');
        } else if (newStatus === 'Đã giải quyết') {
            statusBadge.classList.add('bg-success');
        } else if (newStatus === 'Mới') {
            statusBadge.classList.add('bg-primary');
        } else {
            statusBadge.classList.add('bg-secondary');
        }
    }
    
    // Form sẽ submit bình thường
    return true;
}
</script>

