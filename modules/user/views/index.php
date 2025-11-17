<?php
$current_page = 'profile';
$title = 'Hồ Sơ';
?>

<section class="section py-4">
    <div class="container">
        <h1 class="page-title mb-4"><i class="fas fa-user"></i> Hồ Sơ Của Tôi</h1>
        
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <!-- Profile Header -->
                        <div class="text-center mb-4">
                            <div class="profile-avatar mx-auto mb-3">
                                <?php if ($user['avatar']): ?>
                                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="avatar-placeholder rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; background-color: #e9ecef;">
                                        <i class="fas fa-user fa-3x text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h3 class="mb-1 fw-bold"><?php echo htmlspecialchars($user['name']); ?></h3>
                            <p class="text-muted small mb-2">( <?php echo htmlspecialchars($userRole); ?> )</p>
                            <?php if ($subscription && in_array(strtolower($subscription['name']), ['gold', 'premium', 'pro vip'])): ?>
                                <span class="badge bg-danger px-3 py-2">Pro Vip</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Current Subscription -->
                        <?php if ($subscription): ?>
                            <div class="alert alert-info mb-3">
                                <small class="d-block text-muted mb-1">Gói hiện tại</small>
                                <strong><?php echo htmlspecialchars($subscription['name']); ?></strong>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Upgrade Button -->
                        <button class="btn btn-warning w-100 mb-4 fw-bold" onclick="openUpgradeModal()">
                            <i class="fas fa-wallet me-2"></i> Nâng cấp gói ngay
                        </button>
                        
                        <!-- Balance Section -->
                        <div class="card bg-light mb-4">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-coins text-warning me-2 fs-5"></i>
                                    <div>
                                        <small class="text-muted d-block">Số dư (điểm)</small>
                                        <strong class="fs-5"><?php echo number_format($balance, 0, ',', '.'); ?> điểm</strong>
                                    </div>
                                </div>
                                <button class="btn btn-danger btn-sm" onclick="alert('Tính năng nạp điểm sẽ được thêm sau!');">
                                    <i class="fas fa-plus me-1"></i> Nạp
                                </button>
                            </div>
                        </div>
                        
                        <!-- Menu -->
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action border-0 px-0 py-3">
                                <i class="fas fa-list text-primary me-2"></i> Danh sách
                            </a>
                            <a href="#history" class="list-group-item list-group-item-action border-0 px-0 py-3">
                                <i class="fas fa-history text-primary me-2"></i> Lịch sử
                            </a>
                            <a href="#" class="list-group-item list-group-item-action border-0 px-0 py-3">
                                <i class="fas fa-heart text-primary me-2"></i> Yêu thích
                            </a>
                            <a href="?route=auth/logout" class="list-group-item list-group-item-action border-0 px-0 py-3 text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-8 col-md-12">
                <!-- Personal Info -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0"><i class="fas fa-edit text-primary me-2"></i> Thông tin cá nhân</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="http://localhost/DuAn1/?route=profile/update">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Họ và tên</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="birthdate" class="form-label fw-semibold">Ngày sinh</label>
                                <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $user['birthdate']; ?>">
                            </div>
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="fas fa-save me-2"></i> Cập nhật
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Watch History -->
                <div class="card shadow-sm border-0 mb-4" id="history">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0"><i class="fas fa-history text-primary me-2"></i> Lịch sử xem phim</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (empty($history)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Chưa có lịch sử xem phim.</p>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($history as $item): ?>
                                    <div class="col-12">
                                        <a href="http://localhost/DuAn1/?route=movie/watch&id=<?php echo $item['movie_id']; ?>" class="text-decoration-none text-dark">
                                            <div class="card border h-100 hover-shadow">
                                                <div class="card-body p-3">
                                                    <div class="d-flex gap-3">
                                                        <?php if ($item['thumbnail']): ?>
                                                            <img src="<?php echo htmlspecialchars($item['thumbnail']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="rounded" style="width: 80px; height: 120px; object-fit: cover;">
                                                        <?php endif; ?>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($item['title']); ?></h6>
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock me-1"></i>
                                                                <?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Tickets -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0"><i class="fas fa-ticket-alt text-primary me-2"></i> Vé của tôi</h5>
                    </div>
                    <div class="card-body p-4">
                        <a href="http://localhost/DuAn1/?route=booking/my-tickets" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i> Xem tất cả vé
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Upgrade Subscription Modal -->
<div class="modal fade" id="upgradeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nâng cấp gói</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Điểm hiện tại của bạn: <strong><?php echo number_format($balance, 0, ',', '.'); ?> điểm</strong>
                </div>
                
                <div class="row g-3">
                    <?php foreach ($allSubscriptions as $sub): ?>
                        <?php 
                        $subPrice = intval($sub['price']);
                        $canAfford = $balance >= $subPrice;
                        $isCurrent = $subscription && $subscription['id'] == $sub['id'];
                        $isHigher = $subscription && intval($subscription['price']) >= $subPrice;
                        ?>
                        <div class="col-md-6">
                            <div class="card h-100 <?php echo $isCurrent ? 'border-warning' : ''; ?> <?php echo !$canAfford ? 'opacity-50' : ''; ?>">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($sub['name']); ?>
                                        <?php if ($isCurrent): ?>
                                            <span class="badge bg-warning text-dark">Gói hiện tại</span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="card-text text-muted small"><?php echo htmlspecialchars($sub['description']); ?></p>
                                    <div class="mb-2">
                                        <strong class="text-danger"><?php echo number_format($subPrice, 0, ',', '.'); ?> điểm</strong>
                                    </div>
                                    <?php if ($sub['benefits']): ?>
                                        <small class="text-muted d-block mb-2"><?php echo htmlspecialchars($sub['benefits']); ?></small>
                                    <?php endif; ?>
                                    
                                    <?php if ($isCurrent): ?>
                                        <button class="btn btn-secondary w-100" disabled>Đang sử dụng</button>
                                    <?php elseif ($isHigher): ?>
                                        <button class="btn btn-secondary w-100" disabled>Gói thấp hơn</button>
                                    <?php elseif (!$canAfford): ?>
                                        <button class="btn btn-secondary w-100" disabled>Không đủ điểm</button>
                                    <?php else: ?>
                                        <form method="POST" action="http://localhost/DuAn1/?route=profile/upgradeSubscription" class="d-inline">
                                            <input type="hidden" name="subscription_id" value="<?php echo $sub['id']; ?>">
                                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Bạn có chắc muốn nâng cấp lên gói <?php echo htmlspecialchars($sub['name']); ?> với giá <?php echo number_format($subPrice, 0, ',', '.'); ?> điểm?');">
                                                <i class="fas fa-arrow-up me-2"></i> Nâng cấp
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openUpgradeModal() {
    const modal = new bootstrap.Modal(document.getElementById('upgradeModal'));
    modal.show();
}
</script>

