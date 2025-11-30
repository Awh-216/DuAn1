<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Yêu cầu thay đổi quyền</h5>
</div>

<?php if (empty($requests)): ?>
    <div class="stat-card text-center py-5">
        <i class="fas fa-inbox" style="font-size: 3rem; color: rgba(255, 255, 255, 0.3); margin-bottom: 1rem;"></i>
        <p class="text-muted">Chưa có yêu cầu nào</p>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="mb-3">Danh sách yêu cầu</h6>
                <div class="list-group">
                    <?php foreach ($requests as $req): ?>
                        <a href="?route=moderator/permissionRequests&id=<?php echo $req['id']; ?>" 
                           class="list-group-item list-group-item-action <?php echo ($selectedRequest && $selectedRequest['id'] == $req['id']) ? 'active' : ''; ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($req['target_user_name']); ?></h6>
                                    <small class="text-muted">
                                        Yêu cầu bởi: <?php echo htmlspecialchars($req['requested_by_name']); ?>
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y H:i', strtotime($req['created_at'])); ?>
                                    </small>
                                </div>
                                <span class="badge bg-warning">Chờ xử lý</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <?php if ($selectedRequest): ?>
                <div class="stat-card">
                    <h6 class="mb-4">Chi tiết yêu cầu</h6>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Người được yêu cầu thay đổi quyền:</label>
                        <p class="fw-bold"><?php echo htmlspecialchars($selectedRequest['target_user_name']); ?></p>
                        <small class="text-muted"><?php echo htmlspecialchars($selectedRequest['target_user_email']); ?></small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Yêu cầu bởi:</label>
                        <p class="fw-bold"><?php echo htmlspecialchars($selectedRequest['requested_by_name']); ?></p>
                        <small class="text-muted"><?php echo htmlspecialchars($selectedRequest['requested_by_email']); ?></small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Rạp:</label>
                        <p class="fw-bold"><?php echo htmlspecialchars($selectedRequest['theater_name']); ?></p>
                    </div>
                    
                    <?php 
                    $oldData = json_decode($selectedRequest['old_data'], true);
                    $newData = json_decode($selectedRequest['new_data'], true);
                    ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Quyền hiện tại:</label>
                            <p class="fw-bold">
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($oldData['role'] ?? 'user'); ?></span>
                                <?php if (isset($oldData['theater_id']) && $oldData['theater_id']): ?>
                                    <br><small class="text-muted">Rạp ID: <?php echo $oldData['theater_id']; ?></small>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Quyền mới:</label>
                            <p class="fw-bold">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($newData['role'] ?? 'user'); ?></span>
                                <?php if (isset($newData['theater_id']) && $newData['theater_id']): ?>
                                    <br><small class="text-muted">Rạp ID: <?php echo $newData['theater_id']; ?></small>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Thời gian yêu cầu:</label>
                        <p><?php echo date('d/m/Y H:i:s', strtotime($selectedRequest['created_at'])); ?></p>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <form method="POST" action="?route=moderator/handlePermissionRequest" style="flex: 1;">
                            <input type="hidden" name="request_id" value="<?php echo $selectedRequest['id']; ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Bạn chắc chắn muốn chấp nhận yêu cầu này?');">
                                <i class="fas fa-check"></i> Chấp nhận
                            </button>
                        </form>
                        <form method="POST" action="?route=moderator/handlePermissionRequest" style="flex: 1;">
                            <input type="hidden" name="request_id" value="<?php echo $selectedRequest['id']; ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Bạn chắc chắn muốn từ chối yêu cầu này?');">
                                <i class="fas fa-times"></i> Từ chối
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="stat-card text-center py-5">
                    <p class="text-muted">Chọn một yêu cầu để xem chi tiết</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

