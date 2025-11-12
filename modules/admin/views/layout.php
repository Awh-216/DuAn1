<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>Admin Panel - CineHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="http://localhost/DuAn1/style.css">
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            padding: 0;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        
        .admin-main {
            margin-left: 250px;
            padding: 20px;
            background-color: #f5f5f5;
            min-height: 100vh;
        }
        
        .admin-header {
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
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-film"></i> CineHub Admin
        </div>
        <ul class="sidebar-menu">
            <li><a href="?route=admin/index" class="<?php echo ($current_page ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a></li>
            <li><a href="?route=admin/users" class="<?php echo ($current_page ?? '') === 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Quản lý người dùng
            </a></li>
            <li><a href="?route=admin/movies" class="<?php echo ($current_page ?? '') === 'movies' ? 'active' : ''; ?>">
                <i class="fas fa-film"></i> Quản lý phim
            </a></li>
            <li><a href="?route=admin/theaters" class="<?php echo ($current_page ?? '') === 'theaters' ? 'active' : ''; ?>">
                <i class="fas fa-building"></i> Quản lý rạp
            </a></li>
            <li><a href="?route=admin/tickets" class="<?php echo ($current_page ?? '') === 'tickets' ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt"></i> Quản lý vé
            </a></li>
            <li><a href="?route=admin/analytics" class="<?php echo ($current_page ?? '') === 'analytics' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Analytics & Báo cáo
            </a></li>
            <li><a href="?route=admin/support" class="<?php echo ($current_page ?? '') === 'support' ? 'active' : ''; ?>">
                <i class="fas fa-headset"></i> Hỗ trợ khách hàng
            </a></li>
            <li><a href="?route=admin/logs" class="<?php echo ($current_page ?? '') === 'logs' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> System Logs
            </a></li>
            <li><a href="http://localhost/DuAn1/">
                <i class="fas fa-home"></i> Về trang chủ
            </a></li>
            <li><a href="?route=auth/logout">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a></li>
        </ul>
    </div>
    
    <div class="admin-main">
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?php echo $title ?? 'Admin Panel'; ?></h4>
                <div class="d-flex align-items-center gap-3">
                    <span><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?></span>
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

