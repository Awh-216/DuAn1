<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ($title != 'CineHub' ? ' - ' : '') : ''; ?>CineHub</title>
    <?php if (isset($meta_description)): ?>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <?php endif; ?>
    <?php if (isset($meta_keywords)): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <?php endif; ?>
    <?php if (isset($meta_og_title)): ?>
    <meta property="og:title" content="<?php echo htmlspecialchars($meta_og_title); ?>">
    <?php endif; ?>
    <?php if (isset($meta_og_description)): ?>
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_og_description); ?>">
    <?php endif; ?>
    <?php if (isset($meta_og_image)): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($meta_og_image); ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="CineHub">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php
    // Sử dụng UrlHelper để lấy base URL
    if (!class_exists('UrlHelper')) {
        require_once __DIR__ . '/../../core/UrlHelper.php';
    }
    $baseUrl = UrlHelper::getBaseUrl();
    ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php 
    // Kiểm tra xem có phải trang login/register không
    $isAuthPage = false;
    if (isset($current_page) && $current_page === 'auth') {
        $isAuthPage = true;
    } elseif (isset($_GET['route']) && (strpos($_GET['route'], 'auth/login') !== false || strpos($_GET['route'], 'auth/register') !== false)) {
        $isAuthPage = true;
    }
    ?>
    
    <?php if (!$isAuthPage): ?>
    <?php
    // Load categories và countries cho dropdown menu
    try {
        require_once __DIR__ . '/../../core/Database.php';
        require_once __DIR__ . '/../../modules/movie/CategoryModel.php';
        $db = Database::getInstance();
        $categoryModel = new CategoryModel();
        $menuCategories = $categoryModel->getAll();
        
        // Lấy danh sách quốc gia từ movies
        $countries = $db->fetchAll("SELECT DISTINCT country FROM movies WHERE country IS NOT NULL AND country != '' ORDER BY country");
    } catch (Exception $e) {
        $menuCategories = [];
        $countries = [];
    }
    
    // Lấy thông tin user nếu chưa có
    if (!isset($user)) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../../modules/user/UserModel.php';
            $userModel = new UserModel();
            $user = $userModel->getById($_SESSION['user_id']);
        } else {
            $user = null;
        }
    }
    
    // Kiểm tra admin/moderator sớm để dùng trong cả desktop và mobile
    $isAdmin = false;
    $isModerator = false;
    if (isset($user) && $user) {
        if (isset($user['role']) && $user['role'] === 'admin') {
            $isAdmin = true;
        } else {
            try {
                require_once __DIR__ . '/../../core/AdminMiddleware.php';
                $isAdmin = AdminMiddleware::hasRole($user['id'], 'Super Admin') || 
                          AdminMiddleware::hasRole($user['id'], 'Admin');
            } catch (Exception $e) {}
        }
        
        if (!$isAdmin) {
            if (isset($user['role']) && $user['role'] === 'moderator') {
                $isModerator = true;
            } elseif (isset($user['theater_id']) && !empty($user['theater_id'])) {
                $isModerator = true;
            } else {
                try {
                    require_once __DIR__ . '/../../core/AdminMiddleware.php';
                    $isModerator = AdminMiddleware::hasRole($user['id'], 'Moderator') || 
                                 AdminMiddleware::hasRole($user['id'], 'Theater Manager');
                } catch (Exception $e) {}
            }
        }
    }
    ?>
    
    <!-- Desktop Header -->
    <header class="header-new header-desktop">
        <div class="header-container">
            <div class="header-left">
                <div class="logo-new">
                    <a href="<?php echo $baseUrl; ?>/">
                        <i class="fas fa-film"></i>
                        <span>CineHub</span>
                    </a>
                </div>
                
                <div class="search-bar">
                    <form method="GET" action="<?php echo $baseUrl; ?>/?route=movie/index" class="search-form-inline">
                        <input type="hidden" name="route" value="movie/index">
                        <label class="labeo" for="search-input-header"></label>
                        <input type="text" name="search" id="search-input-header" class="search-input" placeholder="Tìm kiếm phim...">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <nav class="nav-new">
                <a href="<?php echo $baseUrl; ?>/?route=movie/index&type=phimle" class="nav-link-new">
                    Phim lẻ
                </a>
                <a href="<?php echo $baseUrl; ?>/?route=movie/index&type=phimbo" class="nav-link-new">
                    Phim bộ
                </a>
                <div class="nav-dropdown">
                    <span class="nav-link-new dropdown-trigger">
                        Thể loại <i class="fas fa-chevron-down"></i>
                    </span>
                    <div class="dropdown-menu">
                        <?php foreach ($menuCategories as $cat): ?>
                            <a href="<?php echo $baseUrl; ?>/?route=movie/index&category=<?php echo $cat['id']; ?>" class="dropdown-item">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="nav-dropdown">
                    <span class="nav-link-new dropdown-trigger">
                        Quốc gia <i class="fas fa-chevron-down"></i>
                    </span>
                    <div class="dropdown-menu">
                        <?php foreach ($countries as $country): ?>
                            <a href="<?php echo $baseUrl; ?>/?route=movie/index&country=<?php echo urlencode($country['country']); ?>" class="dropdown-item">
                                <?php echo htmlspecialchars($country['country']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="<?php echo $baseUrl; ?>/?route=movie/index" class="nav-link-new">
                    Top phim
                </a>
                <a href="<?php echo $baseUrl; ?>/?route=booking/index" class="nav-link-new" id="booking-link">
                    Vé xem phim
                </a>
            </nav>
            
            <div class="header-right">
                <?php if (isset($user) && $user): ?>
                    <?php if ($isAdmin): ?>
                        <a href="<?php echo $baseUrl; ?>/?route=admin/index" class="sign-in-btn" style="background-color: #FFFFFF37; margin-right: 10px;">
                            <i class="fas fa-cog"></i>
                            <span>Admin Panel</span>
                        </a>
                    <?php elseif ($isModerator): ?>
                        <a href="<?php echo $baseUrl; ?>/?route=moderator/index" class="sign-in-btn" style="margin-right: 10px;">
                            <i class="fas fa-building"></i>
                            <span>Quản lý rạp</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo $baseUrl; ?>/?route=profile/index" class="sign-in-btn">
                        <i class="fas fa-user"></i>
                        <span><?php echo htmlspecialchars($user['name']); ?></span>
                    </a>
                <?php else: ?>
                    <a href="#" class="sign-in-btn" onclick="event.preventDefault(); openAuthModal('login');">
                        <i class="fas fa-user"></i>
                        <span>Login</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <!-- Mobile Header (Top Bar) -->
    <header class="header-mobile">
        <div class="mobile-header-container">
            <div class="logo-new">
                <a href="<?php echo $baseUrl; ?>/">
                    <i class="fas fa-film"></i>
                    <span>CineHub</span>
                </a>
            </div>
        </div>
    </header>
    
    <!-- Mobile Bottom Navigation -->
    <style>
    @media screen and (max-width: 768px) {
        .header-desktop { display: none !important; }
        .header-mobile { display: block !important; }
        .mobile-bottom-nav { display: flex !important; }
    }
    </style>
    <nav class="mobile-bottom-nav">
        <button class="mobile-nav-item" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
            <span>Menu</span>
        </button>
        <a href="<?php echo $baseUrl; ?>/?route=movie/index" class="mobile-nav-item">
            <i class="fas fa-search"></i>
            <span>Tìm kiếm</span>
        </a>
        <a href="<?php echo $baseUrl; ?>/" class="mobile-nav-item mobile-nav-home">
            <i class="fas fa-home"></i>
        </a>
        <?php if (isset($user) && $user): ?>
            <a href="<?php echo $baseUrl; ?>/?route=profile/index" class="mobile-nav-item">
                <i class="fas fa-user"></i>
                <span>Tài khoản</span>
            </a>
        <?php else: ?>
            <a href="#" class="mobile-nav-item" onclick="event.preventDefault(); openAuthModal('login');">
                <i class="fas fa-user"></i>
                <span>Đăng nhập</span>
            </a>
        <?php endif; ?>
    </nav>
    
    <!-- Mobile Slide Menu -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
    <div class="mobile-slide-menu" id="mobileSlideMenu">
        <div class="mobile-menu-header">
            <div class="mobile-menu-logo">
                <i class="fas fa-film"></i>
                <span>CineHub</span>
            </div>
            <button class="mobile-menu-close" onclick="closeMobileMenu()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <?php if (isset($user) && $user): ?>
        <div class="mobile-menu-user">
            <div class="mobile-user-avatar">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
            <div class="mobile-user-info">
                <span class="mobile-user-name"><?php echo htmlspecialchars($user['name']); ?></span>
                <span class="mobile-user-email"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mobile-menu-search">
            <form method="GET" action="<?php echo $baseUrl; ?>/?route=movie/index">
                <input type="hidden" name="route" value="movie/index">
                <input type="text" name="search" placeholder="Tìm kiếm phim..." class="mobile-search-input">
                <button type="submit" class="mobile-search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <div class="mobile-menu-content">
            <div class="mobile-menu-section">
                <a href="<?php echo $baseUrl; ?>/?route=movie/index&type=phimle" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-film"></i>
                    <span>Phim lẻ</span>
                </a>
                <a href="<?php echo $baseUrl; ?>/?route=movie/index&type=phimbo" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-tv"></i>
                    <span>Phim bộ</span>
                </a>
                <a href="<?php echo $baseUrl; ?>/?route=movie/index" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-star"></i>
                    <span>Top phim</span>
                </a>
                <a href="<?php echo $baseUrl; ?>/?route=booking/index" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Vé xem phim</span>
                </a>
            </div>
            
            <div class="mobile-menu-section">
                <div class="mobile-menu-section-title">Thể loại</div>
                <div class="mobile-menu-tags">
                    <?php foreach (array_slice($menuCategories, 0, 8) as $cat): ?>
                        <a href="<?php echo $baseUrl; ?>/?route=movie/index&category=<?php echo $cat['id']; ?>" class="mobile-menu-tag" onclick="closeMobileMenu()">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="mobile-menu-section">
                <div class="mobile-menu-section-title">Quốc gia</div>
                <div class="mobile-menu-tags">
                    <?php foreach (array_slice($countries, 0, 6) as $country): ?>
                        <a href="<?php echo $baseUrl; ?>/?route=movie/index&country=<?php echo urlencode($country['country']); ?>" class="mobile-menu-tag" onclick="closeMobileMenu()">
                            <?php echo htmlspecialchars($country['country']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php if (isset($user) && $user): ?>
            <div class="mobile-menu-section">
                <div class="mobile-menu-section-title">Tài khoản</div>
                <a href="<?php echo $baseUrl; ?>/?route=profile/index" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-user-circle"></i>
                    <span>Hồ sơ của tôi</span>
                </a>
                <a href="<?php echo $baseUrl; ?>/?route=booking/myTickets" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Vé của tôi</span>
                </a>
                <?php if ($isAdmin): ?>
                <a href="<?php echo $baseUrl; ?>/?route=admin/index" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-cog"></i>
                    <span>Admin Panel</span>
                </a>
                <?php elseif ($isModerator): ?>
                <a href="<?php echo $baseUrl; ?>/?route=moderator/index" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-building"></i>
                    <span>Quản lý rạp</span>
                </a>
                <?php endif; ?>
                <a href="<?php echo $baseUrl; ?>/?route=auth/logout" class="mobile-menu-link mobile-menu-logout" onclick="closeMobileMenu()">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Đăng xuất</span>
                </a>
            </div>
            <?php else: ?>
            <div class="mobile-menu-section">
                <a href="#" class="mobile-menu-link mobile-menu-login" onclick="event.preventDefault(); closeMobileMenu(); openAuthModal('login');">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Đăng nhập</span>
                </a>
                <a href="#" class="mobile-menu-link" onclick="event.preventDefault(); closeMobileMenu(); openAuthModal('register');">
                    <i class="fas fa-user-plus"></i>
                    <span>Đăng ký</span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileSlideMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        menu.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';
    }
    
    function closeMobileMenu() {
        const menu = document.getElementById('mobileSlideMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        menu.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    </script>
    <?php endif; ?>
    
    <?php
    // Nút thông báo hình tròn fixed bên phải ngoài cùng
    if (isset($user) && $user): 
        // Đếm số thông báo chưa đọc
        $unreadCount = 0;
        try {
            require_once __DIR__ . '/../../core/Database.php';
            $db = Database::getInstance();
            $unreadCount = $db->fetch("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0", [$user['id']])['count'] ?? 0;
        } catch (Exception $e) {
            // Bảng chưa tồn tại
        }
    ?>
    <div class="notification-wrapper-fixed" id="notificationWrapper">
        <a href="javascript:void(0);" class="notification-btn-fixed" id="notificationBtnFixed" onclick="toggleNotificationDropdown()">
            <i class="fas fa-bell"></i>
            <?php if ($unreadCount > 0): ?>
                <span class="notification-badge"><?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?></span>
            <?php endif; ?>
        </a>
        <div class="notification-dropdown" id="notificationDropdown" style="display: none;">
            <div class="notification-dropdown-header">
                <h6>Thông báo</h6>
                <?php 
                // Nếu là moderator, link đến trang yêu cầu thay đổi quyền
                $viewAllLink = '?route=notifications/index';
                if ($isModerator) {
                    $viewAllLink = '?route=moderator/permissionRequests';
                }
                ?>
                <a href="<?php echo $viewAllLink; ?>" class="view-all-link">Xem tất cả</a>
            </div>
            <div class="notification-dropdown-body" id="notificationList">
                <div class="notification-loading">
                    <i class="fas fa-spinner fa-spin"></i> Đang tải...
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <?php 
        $successMsg = $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
        <div class="alert-modal alert-success-modal" id="alertModal">
            <div class="alert-modal-content">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($successMsg); ?></span>
                <button class="alert-close" onclick="closeAlertModal()">&times;</button>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showAlertModal('success', <?php echo json_encode($successMsg); ?>);
            });
        </script>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <?php 
        $errorMsg = $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
        <div class="alert-modal alert-error-modal" id="alertModal">
            <div class="alert-modal-content">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($errorMsg); ?></span>
                <button class="alert-close" onclick="closeAlertModal()">&times;</button>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showAlertModal('error', <?php echo json_encode($errorMsg); ?>);
            });
        </script>
    <?php endif; ?>


<!-- Auth Modal (Login/Register) -->
<div id="authModal" class="modal-overlay" style="display: none;">
    <div class="modal-content-login">
        <span class="modal-close" onclick="closeAuthModal()">&times;</span>
        
        <!-- Tab Navigation -->
        <div class="auth-tabs">
            <button class="auth-tab active" onclick="switchAuthTab('login')">Đăng nhập</button>
            <button class="auth-tab" onclick="switchAuthTab('register')">Đăng ký</button>
        </div>
        
        <!-- Login Form -->
        <div id="loginTab" class="auth-tab-content active">
            <h2 class="modal-title">Đăng nhập</h2>
            <div id="loginError" class="alert alert-error" style="display: none;"></div>
            <form id="loginForm" method="POST" action="<?php echo $baseUrl; ?>/?route=auth/login">
                <div class="form-group-new">
                    <input type="email" name="email" required placeholder="Email" class="input-field">
                </div>
                <div class="form-group-new">
                    <input type="password" name="password" required placeholder="Mật khẩu" class="input-field">
                </div>
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember_me">
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="forgot-password">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="btn-login">Đăng nhập</button>
            </form>
            <div class="modal-footer">
                <p>Chưa có tài khoản? <a href="#" onclick="event.preventDefault(); switchAuthTab('register');">Đăng ký ngay</a></p>
            </div>
        </div>
        
        <!-- Register Form -->
        <div id="registerTab" class="auth-tab-content" style="display: none;">
            <h2 class="modal-title">Đăng ký</h2>
            <div id="registerError" class="alert alert-error" style="display: none;"></div>
            <form id="registerForm" method="POST" action="<?php echo $baseUrl; ?>/?route=auth/register">
                <div class="form-group-new">
                    <input type="text" name="name" required placeholder="Họ và tên" class="input-field">
                </div>
                <div class="form-group-new">
                    <input type="email" name="email" required placeholder="Email" class="input-field">
                </div>
                <div class="form-group-new">
                    <input type="password" name="password" required placeholder="Mật khẩu" class="input-field">
                </div>
                <div class="form-group-new">
                    <input type="password" name="confirm_password" required placeholder="Xác nhận mật khẩu" class="input-field">
                </div>
                <button type="submit" class="btn-register-form">Đăng ký</button>
            </form>
            <div class="modal-footer">
                <p>Đã có tài khoản? <a href="#" onclick="event.preventDefault(); switchAuthTab('login');">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.modal-overlay.show {
    opacity: 1;
    visibility: visible;
}

.modal-content-login {
    background: #1a1a1a;
    padding: 40px;
    border-radius: 10px;
    width: 90%;
    max-width: 450px;
    position: relative;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    transform: scale(0.7) translateY(-50px);
    opacity: 0;
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-overlay.show .modal-content-login {
    transform: scale(1) translateY(0);
    opacity: 1;
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 28px;
    font-weight: bold;
    color: #fff;
    cursor: pointer;
    transition: color 0.3s;
}

.modal-close:hover {
    color: #e50914;
}

.modal-title {
    color: #fff;
    margin-bottom: 30px;
    text-align: center;
    font-size: 28px;
}

.modal-footer {
    text-align: center;
    margin-top: 20px;
    color: #999;
}

.modal-footer a {
    color: #e50914;
    text-decoration: none;
}

.modal-footer a:hover {
    text-decoration: underline;
}

.auth-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid #333;
}

.auth-tab {
    flex: 1;
    padding: 12px;
    background: transparent;
    border: none;
    color: #999;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    transition: all 0.3s;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
}

.auth-tab:hover {
    color: #fff;
}

.auth-tab.active {
    color: #e50914;
    border-bottom-color: #e50914;
}

.auth-tab-content {
    display: none;
    opacity: 0;
    transform: translateX(-20px);
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.auth-tab-content.active {
    display: block;
    opacity: 1;
    transform: translateX(0);
}
</style>

<script>
function openAuthModal(tab = 'login') {
    const modal = document.getElementById('authModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Trigger animation by adding 'show' class after a small delay
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
    
    switchAuthTab(tab);
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    modal.classList.remove('show');
    
    // Wait for animation to complete before hiding
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('loginError').style.display = 'none';
        document.getElementById('registerError').style.display = 'none';
        document.getElementById('loginForm').reset();
        document.getElementById('registerForm').reset();
    }, 300);
}

function switchAuthTab(tab) {
    // Ẩn tất cả tabs
    document.querySelectorAll('.auth-tab-content').forEach(content => {
        content.classList.remove('active');
        content.style.display = 'none';
    });
    
    document.querySelectorAll('.auth-tab').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Hiển thị tab được chọn
    if (tab === 'login') {
        document.getElementById('loginTab').classList.add('active');
        document.getElementById('loginTab').style.display = 'block';
        document.querySelectorAll('.auth-tab')[0].classList.add('active');
    } else {
        document.getElementById('registerTab').classList.add('active');
        document.getElementById('registerTab').style.display = 'block';
        document.querySelectorAll('.auth-tab')[1].classList.add('active');
    }
    
    // Reset errors
    document.getElementById('loginError').style.display = 'none';
    document.getElementById('registerError').style.display = 'none';
}

// Đóng modal khi click bên ngoài
document.getElementById('authModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAuthModal();
    }
});

// Xử lý form đăng nhập với AJAX
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const errorDiv = document.getElementById('loginError');
    errorDiv.style.display = 'none';
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            errorDiv.textContent = data.error || 'Đăng nhập thất bại!';
            errorDiv.style.display = 'block';
        }
    })
    .catch(error => {
        // Nếu không phải JSON response, thử submit form bình thường
        this.submit();
    });
});

// Xử lý form đăng ký với AJAX
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const errorDiv = document.getElementById('registerError');
    errorDiv.style.display = 'none';
    
    // Kiểm tra mật khẩu khớp
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');
    
    if (password !== confirmPassword) {
        errorDiv.textContent = 'Mật khẩu xác nhận không khớp!';
        errorDiv.style.display = 'block';
        return;
    }
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            errorDiv.textContent = data.error || 'Đăng ký thất bại!';
            errorDiv.style.display = 'block';
        }
    })
    .catch(error => {
        // Nếu không phải JSON response, thử submit form bình thường
        this.submit();
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputt = document.querySelector('.search-input');
    const label = document.querySelector('.labeo');

    if (inputt && label) {
        inputt.addEventListener('focus', function() {
            label.style.opacity = '0';
        });

        inputt.addEventListener('blur', function() {
            if (inputt.value.trim() === '') {
                label.style.opacity = '0.7';
            }
        });
    }
});

// Alert Modal Functions
function showAlertModal(type, message) {
    const modal = document.getElementById('alertModal');
    if (modal) {
        modal.classList.add('show');
        // Tự động đóng sau 5 giây
        setTimeout(function() {
            closeAlertModal();
        }, 5000);
    } else {
        // Fallback: dùng alert nếu không tìm thấy modal
        alert(message);
    }
}

function closeAlertModal() {
    const modal = document.getElementById('alertModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(function() {
            modal.remove();
        }, 300);
    }
}

// Đóng modal khi click vào overlay
document.addEventListener('click', function(e) {
    const modal = document.getElementById('alertModal');
    if (modal && e.target === modal) {
        closeAlertModal();
    }
});

// Đóng modal khi nhấn ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAlertModal();
    }
});

// Biến JavaScript để kiểm tra trạng thái đăng nhập
var isUserLoggedIn = <?php echo (isset($user) && $user) ? 'true' : 'false'; ?>;

// Kiểm tra đăng nhập khi click vào "Vé xem phim"
document.addEventListener('DOMContentLoaded', function() {
    const bookingLink = document.getElementById('booking-link');
    if (bookingLink) {
        bookingLink.addEventListener('click', function(e) {
            if (!isUserLoggedIn) {
                // Nếu chưa đăng nhập, hiển thị modal đăng nhập
                e.preventDefault();
                openAuthModal('login');
                // Hiển thị thông báo yêu cầu đăng nhập
                setTimeout(function() {
                    const loginError = document.getElementById('loginError');
                    if (loginError) {
                        loginError.textContent = 'Vui lòng đăng nhập để đặt vé xem phim!';
                        loginError.style.display = 'block';
                        loginError.classList.add('alert-error');
                    }
                }, 100);
            }
        });
    }
    
    // Search bar functionality (giữ lại cho tương lai)
    const searchBar = document.querySelector('.search-bar');
    const searchInput = document.getElementById('search-input-header');
    const searchForm = document.querySelector('.search-form-inline');
    
    if (searchInput && searchForm) {
        // Focus vào input khi click vào search bar
        if (searchBar) {
            searchBar.addEventListener('click', function(e) {
                if (e.target === searchBar || (e.target.closest('.search-bar') && !e.target.closest('.search-btn'))) {
                    searchInput.focus();
                }
            });
        }
        
        // Xử lý submit form
        searchForm.addEventListener('submit', function(e) {
            const searchValue = searchInput.value.trim();
            if (!searchValue) {
                e.preventDefault();
                return false;
            }
        });
        
        // Clear search khi nhấn ESC
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchInput.value = '';
                searchInput.blur();
            }
        });
    }
});
</script>

