    <?php 
    // Kiểm tra xem có phải trang login/register không (sử dụng lại logic từ header)
    $isAuthPage = false;
    if (isset($current_page) && $current_page === 'auth') {
        $isAuthPage = true;
    } elseif (isset($_GET['route']) && (strpos($_GET['route'], 'auth/login') !== false || strpos($_GET['route'], 'auth/register') !== false)) {
        $isAuthPage = true;
    }
    ?>
    
    <?php if (!$isAuthPage): ?>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-film"></i>
                        <span>CineHub</span>
                    </div>
                    <p class="footer-description">
                        Nền tảng xem phim trực tuyến hàng đầu Việt Nam. 
                        Xem phim chất lượng cao, không giới hạn với nhiều thể loại đa dạng.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="social-link" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Danh mục</h3>
                    <ul class="footer-links">
                        <?php
                        // Sử dụng UrlHelper để lấy base URL
                        if (!isset($baseUrl)) {
                            if (!class_exists('UrlHelper')) {
                                require_once __DIR__ . '/../../core/UrlHelper.php';
                            }
                            $baseUrl = UrlHelper::getBaseUrl();
                        }
                        ?>
                        <li><a href="<?php echo $baseUrl; ?>/?route=movie/index">Phim mới</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?route=movie/index">Phim hot</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?route=movie/index">Phim lẻ</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?route=movie/index">Phim bộ</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?route=movie/index">Phim hoạt hình</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Thể loại</h3>
                    <ul class="footer-links">
                        <li><a href="<?php echo $baseUrl; ?>/?route=movie/index&category=1">Hành động</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?route=movie/index&category=2">Tình cảm</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?route=movie/index&category=3">Hài</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?route=movie/index&category=4">Kinh dị</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?route=movie/index&category=5">Hoạt hình</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Hỗ trợ</h3>
                    <ul class="footer-links">
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                        <li><a href="#">Điều khoản sử dụng</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="<?php echo $baseUrl; ?>/?route=booking/index">Đặt vé xem phim</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> CineHub. Tất cả quyền được bảo lưu.</p>
                <div class="footer-payment">
                    <span>Chấp nhận thanh toán:</span>
                    <i class="fab fa-cc-visa" title="Visa"></i>
                    <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                    <i class="fas fa-mobile-alt" title="Momo"></i>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Notification Dropdown
    let notificationDropdownOpen = false;
    
    function toggleNotificationDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        if (!dropdown) return;
        
        notificationDropdownOpen = !notificationDropdownOpen;
        
        if (notificationDropdownOpen) {
            dropdown.style.display = 'block';
            loadNotifications();
        } else {
            dropdown.style.display = 'none';
        }
    }
    
    function loadNotifications() {
        const list = document.getElementById('notificationList');
        if (!list) return;
        
        list.innerHTML = '<div class="notification-loading"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';
        
        fetch('?route=notifications/getNotifications&limit=10')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error('Response is not JSON: ' + text.substring(0, 200));
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Notifications data received:', data);
                if (data && data.notifications && Array.isArray(data.notifications) && data.notifications.length > 0) {
                    let html = '';
                    data.notifications.forEach(notif => {
                        const iconClass = getIconClass(notif.type);
                        const iconName = getIconName(notif.type);
                        const unreadClass = notif.is_read == 0 ? 'unread' : '';
                        const link = notif.link || '?route=notifications/index';
                        
                        html += `
                            <div class="notification-item-dropdown ${unreadClass}" onclick="window.location.href='${link}'; markAsRead(${notif.id});">
                                <div class="notification-icon-dropdown ${iconClass}">
                                    <i class="fas ${iconName}"></i>
                                </div>
                                <div class="notification-content-dropdown">
                                    <div class="notification-title-dropdown">${escapeHtml(notif.title)}</div>
                                    <div class="notification-message-dropdown">${escapeHtml(notif.message)}</div>
                                    <div class="notification-time-dropdown">${notif.time_ago || 'Vừa xong'}</div>
                                </div>
                            </div>
                        `;
                    });
                    list.innerHTML = html;
                } else {
                    console.log('No notifications found or empty array');
                    if (data && data.error) {
                        list.innerHTML = '<div class="notification-empty"><i class="fas fa-exclamation-circle"></i>Lỗi: ' + escapeHtml(data.error) + '</div>';
                    } else {
                        list.innerHTML = '<div class="notification-empty"><i class="fas fa-bell-slash"></i>Không có thông báo</div>';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                console.error('Error details:', error.message, error.stack);
                list.innerHTML = '<div class="notification-empty"><i class="fas fa-exclamation-circle"></i>Không thể tải thông báo: ' + escapeHtml(error.message) + '</div>';
            });
    }
    
    function getIconClass(type) {
        const map = {
            'success': 'success',
            'warning': 'warning',
            'error': 'error',
            'booking': 'booking',
            'info': 'info'
        };
        return map[type] || 'info';
    }
    
    function getIconName(type) {
        const map = {
            'success': 'fa-check-circle',
            'warning': 'fa-exclamation-triangle',
            'error': 'fa-times-circle',
            'booking': 'fa-ticket-alt',
            'info': 'fa-info-circle'
        };
        return map[type] || 'fa-info-circle';
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
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
    
    // Đóng dropdown khi click bên ngoài
    document.addEventListener('click', function(event) {
        const wrapper = document.getElementById('notificationWrapper');
        const dropdown = document.getElementById('notificationDropdown');
        const btn = document.getElementById('notificationBtnFixed');
        
        if (wrapper && dropdown && btn && notificationDropdownOpen) {
            if (!wrapper.contains(event.target)) {
                dropdown.style.display = 'none';
                notificationDropdownOpen = false;
            }
        }
    });
    </script>
</body>
</html>

