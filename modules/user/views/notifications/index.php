<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: #fff;">Thông báo</h2>
                <?php if ($unreadCount > 0): ?>
                    <form method="POST" action="?route=notifications/markAsRead" style="display: inline;">
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            
            <?php if (empty($notifications)): ?>
                <div class="card" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bell-slash" style="font-size: 3rem; color: rgba(255, 255, 255, 0.5); margin-bottom: 1rem;"></i>
                        <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.1rem;">Chưa có thông báo nào</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="card mb-3 notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>" 
                             style="background: <?php echo $notification['is_read'] ? 'rgba(255, 255, 255, 0.05)' : 'rgba(229, 9, 20, 0.1)'; ?>; 
                                    border: 1px solid <?php echo $notification['is_read'] ? 'rgba(255, 255, 255, 0.1)' : 'rgba(229, 9, 20, 0.3)'; ?>; 
                                    cursor: pointer;"
                             onclick="window.location.href='<?php echo $notification['link'] ? htmlspecialchars($notification['link']) : '?route=notifications/index'; ?>'; markAsRead(<?php echo $notification['id']; ?>);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <?php
                                            $iconClass = 'fa-info-circle';
                                            $iconColor = '#0d6efd';
                                            switch ($notification['type']) {
                                                case 'success':
                                                    $iconClass = 'fa-check-circle';
                                                    $iconColor = '#28a745';
                                                    break;
                                                case 'warning':
                                                    $iconClass = 'fa-exclamation-triangle';
                                                    $iconColor = '#ffc107';
                                                    break;
                                                case 'error':
                                                    $iconClass = 'fa-times-circle';
                                                    $iconColor = '#dc3545';
                                                    break;
                                                case 'booking':
                                                    $iconClass = 'fa-ticket-alt';
                                                    $iconColor = '#e50914';
                                                    break;
                                            }
                                            ?>
                                            <i class="fas <?php echo $iconClass; ?>" style="color: <?php echo $iconColor; ?>; margin-right: 10px; font-size: 1.2rem;"></i>
                                            <h5 class="mb-0" style="color: #fff; font-weight: <?php echo $notification['is_read'] ? 'normal' : 'bold'; ?>;">
                                                <?php echo htmlspecialchars($notification['title']); ?>
                                            </h5>
                                            <?php if (!$notification['is_read']): ?>
                                                <span class="badge bg-danger ms-2">Mới</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-2" style="color: rgba(255, 255, 255, 0.8);">
                                            <?php echo htmlspecialchars($notification['message']); ?>
                                        </p>
                                        <small style="color: rgba(255, 255, 255, 0.5);">
                                            <i class="far fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="ms-3">
                                        <a href="?route=notifications/delete&id=<?php echo $notification['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="event.stopPropagation(); return confirm('Bạn chắc chắn muốn xóa thông báo này?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch('?route=notifications/markAsRead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + notificationId
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              // Cập nhật UI
              const item = document.querySelector('.notification-item[onclick*="' + notificationId + '"]');
              if (item) {
                  item.classList.remove('unread');
                  item.style.background = 'rgba(255, 255, 255, 0.05)';
                  item.style.border = '1px solid rgba(255, 255, 255, 0.1)';
                  const badge = item.querySelector('.badge');
                  if (badge) badge.remove();
                  const title = item.querySelector('h5');
                  if (title) title.style.fontWeight = 'normal';
              }
              
              // Cập nhật badge trên header
              updateNotificationBadge();
          }
      });
}

function updateNotificationBadge() {
    fetch('?route=notifications/getUnreadCount')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notification-badge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                } else {
                    // Tạo badge mới nếu chưa có
                    const btn = document.querySelector('.notification-btn-fixed');
                    if (btn) {
                        const newBadge = document.createElement('span');
                        newBadge.className = 'notification-badge';
                        newBadge.textContent = data.count > 99 ? '99+' : data.count;
                        btn.appendChild(newBadge);
                    }
                }
            } else {
                if (badge) badge.remove();
            }
        });
}
</script>

<style>
.notification-item:hover {
    transform: translateX(5px);
    transition: all 0.3s;
}

.notification-item.unread {
    border-left: 4px solid #e50914 !important;
}
</style>

