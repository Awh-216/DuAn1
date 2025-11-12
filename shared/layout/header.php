<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>CineHub</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://localhost/DuAn1/style.css">
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
    ?>
    <header class="header-new">
        <div class="header-container">
            <div class="header-left">
                <div class="logo-new">
                    <a href="http://localhost/DuAn1/">
                        <i class="fas fa-film"></i>
                        <span>CineHub</span>
                    </a>
                </div>
                
                <div class="search-bar">
                    <form method="GET" action="http://localhost/DuAn1/?route=movie/index" class="search-form-inline">
                        <input type="hidden" name="route" value="movie/index">
                        <label class="labeo" for="search-input-header">Tìm kiếm phim.....................................................</label>
                        <input type="text" name="search" id="search-input-header" class="search-input" placeholder="Tìm kiếm phim...">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <nav class="nav-new">
                <a href="http://localhost/DuAn1/?route=movie/index&category=phim-bo" class="nav-link-new">
                    Phim bộ <i class="fas fa-chevron-down"></i>
                </a>
                <div class="nav-dropdown">
                    <a href="http://localhost/DuAn1/?route=movie/index" class="nav-link-new">
                        Thể loại <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu">
                        <?php foreach ($menuCategories as $cat): ?>
                            <a href="http://localhost/DuAn1/?route=movie/index&category=<?php echo $cat['id']; ?>" class="dropdown-item">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="nav-dropdown">
                    <a href="http://localhost/DuAn1/?route=movie/index" class="nav-link-new">
                        Quốc gia <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu">
                        <?php foreach ($countries as $country): ?>
                            <a href="http://localhost/DuAn1/?route=movie/index&country=<?php echo urlencode($country['country']); ?>" class="dropdown-item">
                                <?php echo htmlspecialchars($country['country']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="http://localhost/DuAn1/?route=movie/index" class="nav-link-new">
                    Top phim<i class="fas fa-chevron-down"></i>
                </a>
                <a href="http://localhost/DuAn1/?route=booking/index" class="nav-link-new">
                    Vé xem phim
                </a>
            </nav>
            
            <div class="header-right">
                <?php if (isset($user) && $user): ?>
                    <?php 
                    // Kiểm tra nếu là admin
                    $isAdmin = false;
                    if (isset($user['role']) && $user['role'] === 'admin') {
                        $isAdmin = true;
                    } else {
                        try {
                            require_once __DIR__ . '/../../core/AdminMiddleware.php';
                            $isAdmin = AdminMiddleware::hasRole($user['id'], 'Super Admin') || 
                                      AdminMiddleware::hasRole($user['id'], 'Admin');
                        } catch (Exception $e) {
                            // Bảng chưa tồn tại, bỏ qua
                        }
                    }
                    ?>
                    <?php if ($isAdmin): ?>
                        <a href="http://localhost/DuAn1/?route=admin/index" class="sign-in-btn" style="background-color: #28a745; margin-right: 10px;">
                            <i class="fas fa-cog"></i>
                            <span>Admin Panel</span>
                        </a>
                    <?php endif; ?>
                    <a href="http://localhost/DuAn1/?route=profile/index" class="sign-in-btn">
                        <i class="fas fa-user"></i>
                        <span><?php echo htmlspecialchars($user['name']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                <?php else: ?>
                    <a href="http://localhost/DuAn1/?route=auth/login" class="sign-in-btn">
                        <i class="fas fa-user"></i>
                        <span>Login</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>


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
</script>

