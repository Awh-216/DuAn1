<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>Quản lý rạp - CineHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php
    // Sử dụng UrlHelper để lấy base URL
    if (!class_exists('UrlHelper')) {
        require_once __DIR__ . '/../../../../core/UrlHelper.php';
    }
    $baseUrl = UrlHelper::getBaseUrl();
    ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/style.css?v=<?php echo time(); ?>">
    <style>
        .moderator-sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            padding: 0;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        
        .moderator-main {
            margin-left: 250px;
            padding: 20px;
            background-color: rgba(60, 60 ,60);
            min-height: 100vh;
        }
        
        .moderator-header {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .sidebar-brand {
            padding: 20px;
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            padding-left: 25px;
        }
        
        .sidebar-menu a i {
            width: 25px;
            margin-right: 10px;
        }
        
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            color: #333;
        }
        
        .stat-card h5,
        .stat-card h6 {
            color: #333 !important;
        }
        
        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-card .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Đảm bảo text trong card trắng có màu tối */
        .stat-card,
        .moderator-header {
            color: #333;
        }
        
        .stat-card h4,
        .stat-card h5,
        .stat-card h6,
        .moderator-header h4,
        .moderator-header h5,
        .moderator-header h6 {
            color: #333 !important;
        }
        
        .stat-card .table {
            color: #333;
        }
        
        .stat-card .table th {
            color: #333;
            background-color: #f8f9fa;
        }
        
        .stat-card .table td {
            color: #333;
        }
        
        .stat-card .form-control {
            color: #333;
            background-color: #fff;
        }
        
        .stat-card .text-muted {
            color: #666 !important;
        }
        
        .moderator-main {
            color: #fff;
        }
        
        .moderator-main > h4,
        .moderator-main > h5,
        .moderator-main > h6 {
            color: #fff !important;
        }
        
        /* Button styles trên nền tối - đảm bảo màu nổi bật */
        .moderator-main .btn-primary {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-primary:hover {
            background-color: #0b5ed7 !important;
            border-color: #0a58ca !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-secondary:hover {
            background-color: #5c636a !important;
            border-color: #565e64 !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-outline-primary {
            border-color: #0d6efd !important;
            color: #0d6efd !important;
            background-color: transparent !important;
        }
        
        .moderator-main .btn-outline-primary:hover {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-outline-secondary {
            border-color: #6c757d !important;
            color: #6c757d !important;
            background-color: transparent !important;
        }
        
        .moderator-main .btn-outline-secondary:hover {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-outline-danger {
            border-color: #dc3545 !important;
            color: #dc3545 !important;
            background-color: transparent !important;
        }
        
        .moderator-main .btn-outline-danger:hover {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-outline-info {
            border-color: #0dcaf0 !important;
            color: #0dcaf0 !important;
            background-color: transparent !important;
        }
        
        .moderator-main .btn-outline-info:hover {
            background-color: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-outline-success {
            border-color: #198754 !important;
            color: #198754 !important;
            background-color: transparent !important;
        }
        
        .moderator-main .btn-outline-success:hover {
            background-color: #198754 !important;
            border-color: #198754 !important;
            color: #fff !important;
        }
        
        /* Button trong stat-card (nền trắng) - đảm bảo màu rõ ràng */
        .stat-card .btn-outline-primary {
            border-color: #0d6efd !important;
            color: #0d6efd !important;
        }
        
        .stat-card .btn-outline-primary:hover {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }
        
        .stat-card .btn-outline-secondary {
            border-color: #6c757d !important;
            color: #6c757d !important;
        }
        
        .stat-card .btn-outline-secondary:hover {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }
        
        .stat-card .btn-outline-danger {
            border-color: #dc3545 !important;
            color: #dc3545 !important;
        }
        
        .stat-card .btn-outline-danger:hover {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #fff !important;
        }
        
        .stat-card .btn-primary {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }
        
        .stat-card .btn-primary:hover {
            background-color: #0b5ed7 !important;
            border-color: #0a58ca !important;
            color: #fff !important;
        }
        
        .stat-card .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }
        
        .stat-card .btn-secondary:hover {
            background-color: #5c636a !important;
            border-color: #565e64 !important;
            color: #fff !important;
        }
    </style>
</head>
<body>
    <div class="moderator-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-building"></i> Quản lý rạp
        </div>
        <ul class="sidebar-menu">
            <li><a href="?route=moderator/index" class="<?php echo ($current_page ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a></li>
            <li><a href="?route=moderator/theater" class="<?php echo ($current_page ?? '') === 'theater' ? 'active' : ''; ?>">
                <i class="fas fa-building"></i> Thông tin rạp
            </a></li>
            <li><a href="?route=moderator/screens" class="<?php echo ($current_page ?? '') === 'screens' ? 'active' : ''; ?>">
                <i class="fas fa-door-open"></i> Quản lý phòng
            </a></li>
            <li><a href="?route=moderator/showtimes" class="<?php echo ($current_page ?? '') === 'showtimes' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> Lịch chiếu
            </a></li>
            <li><a href="?route=moderator/tickets" class="<?php echo ($current_page ?? '') === 'tickets' ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt"></i> Quản lý vé
            </a></li>
            <li><a href="?route=moderator/foodItems" class="<?php echo ($current_page ?? '') === 'food_items' ? 'active' : ''; ?>">
                <i class="fas fa-utensils"></i> Combo & Đồ ăn
            </a></li>
            <li><a href="?route=moderator/permissionRequests" class="<?php echo ($current_page ?? '') === 'permission_requests' ? 'active' : ''; ?>">
                <i class="fas fa-user-shield"></i> Yêu cầu thay đổi quyền
                <?php
                // Đếm số yêu cầu chưa xử lý
                try {
                    $db = Database::getInstance();
                    $pendingCount = $db->fetch("
                        SELECT COUNT(*) as count 
                        FROM moderator_permission_requests 
                        WHERE theater_id = ? AND status = 'pending'
                    ", [$theaterId])['count'] ?? 0;
                    if ($pendingCount > 0):
                ?>
                    <span class="badge bg-danger ms-2"><?php echo $pendingCount; ?></span>
                <?php endif; } catch (Exception $e) {} ?>
            </a></li>
            <li><a href="http://localhost/DuAn1/">
                <i class="fas fa-home"></i> Về trang chủ
            </a></li>
            <li><a href="?route=profile/index">
                <i class="fas fa-user"></i> Hồ sơ
            </a></li>
            <li><a href="?route=auth/logout">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a></li>
        </ul>
    </div>
    
    <div class="moderator-main">
        <div class="moderator-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><?php echo $title ?? 'Quản lý rạp'; ?></h4>
                    <?php if (isset($theater)): ?>
                        <small class="text-muted"><?php echo htmlspecialchars($theater['name']); ?> - <?php echo htmlspecialchars($theater['location'] ?? ''); ?></small>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user['name'] ?? 'Moderator'); ?></span>
                </div>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php echo $content; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>


