<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/UserModel.php';
require_once __DIR__ . '/../movie/WatchHistoryModel.php';
require_once __DIR__ . '/../booking/BookingModel.php';
require_once __DIR__ . '/../../core/Database.php';

class ProfileController extends Controller {
    
    public function index() {
        $this->requireLogin();
        
        $user = $this->getCurrentUser();
        $watchHistoryModel = new WatchHistoryModel();
        $bookingModel = new BookingModel();
        $db = Database::getInstance();
        
        $history = $watchHistoryModel->getUserHistory($user['id']);
        $tickets = $bookingModel->getUserTickets($user['id']);
        
        // Lấy thông tin subscription
        $subscription = null;
        if ($user['subscription_id']) {
            $subscription = $db->fetch("SELECT * FROM subscriptions WHERE id = ?", [$user['subscription_id']]);
        }
        
        // Xác định role từ bảng roles hoặc cột role
        $userRole = 'Thành viên';
        
        // Kiểm tra role từ bảng roles (ưu tiên)
        if (isset($user['roles']) && !empty($user['roles'])) {
            // Lấy role đầu tiên (thường là role cao nhất)
            $role = $user['roles'][0];
            $roleName = $role['name'] ?? '';
            
            // Map tên role sang tiếng Việt
            $roleMap = [
                'Super Admin' => 'Super Admin',
                'Admin' => 'Admin',
                'Moderator' => 'Quản lý rạp',
                'Content Manager' => 'Quản lý nội dung',
                'Support Staff' => 'Nhân viên hỗ trợ',
                'Theater Manager' => 'Quản lý rạp'
            ];
            
            if (isset($roleMap[$roleName])) {
                $userRole = $roleMap[$roleName];
            } else {
                $userRole = $roleName;
            }
        } else {
            // Kiểm tra role từ cột role cũ
            $role = $user['role'] ?? 'user';
            
            $roleMap = [
                'user' => 'Thành viên',
                'admin' => 'Admin',
                'moderator' => 'Quản lý rạp',
                'manager' => 'Quản lý'
            ];
            
            if (isset($roleMap[$role])) {
                $userRole = $roleMap[$role];
            } else {
                $userRole = ucfirst($role);
            }
        }
        
        // Số dư = điểm (1 VND = 1 điểm)
        $balance = $user['points'] ?? 0;
        
        // Lấy tất cả các gói subscription để hiển thị
        $allSubscriptions = $db->fetchAll("SELECT * FROM subscriptions ORDER BY price ASC");
        
        // Kiểm tra xem user có phải moderator không
        require_once __DIR__ . '/../../core/AdminMiddleware.php';
        $isModerator = false;
        
        // Kiểm tra role từ cột role cũ
        if (isset($user['role']) && $user['role'] === 'moderator') {
            $isModerator = true;
        }
        
        // Kiểm tra role từ bảng roles mới
        if (!$isModerator && !empty($user['roles'])) {
            foreach ($user['roles'] as $role) {
                if (isset($role['name']) && ($role['name'] === 'Moderator' || $role['name'] === 'Theater Manager')) {
                    $isModerator = true;
                    break;
                }
            }
        }
        
        // Nếu vẫn chưa tìm thấy, dùng method của AdminMiddleware
        if (!$isModerator) {
            $isModerator = AdminMiddleware::isModerator($user['id']);
        }
        
        $this->view('profile/index', [
            'user' => $user,
            'history' => $history,
            'tickets' => $tickets,
            'subscription' => $subscription,
            'allSubscriptions' => $allSubscriptions,
            'userRole' => $userRole,
            'balance' => $balance,
            'isModerator' => $isModerator
        ]);
    }
    
    /**
     * Nâng cấp gói subscription bằng điểm
     */
    public function upgradeSubscription() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('profile');
            return;
        }
        
        $user = $this->getCurrentUser();
        $subscriptionId = intval($_POST['subscription_id'] ?? 0);
        
        if (!$subscriptionId) {
            $_SESSION['error'] = 'Vui lòng chọn gói!';
            $this->redirect('profile');
            return;
        }
        
        $db = Database::getInstance();
        $userModel = new UserModel();
        
        // Lấy thông tin gói subscription
        $subscription = $db->fetch("SELECT * FROM subscriptions WHERE id = ?", [$subscriptionId]);
        if (!$subscription) {
            $_SESSION['error'] = 'Gói không tồn tại!';
            $this->redirect('profile');
            return;
        }
        
        // Kiểm tra nếu đã có gói này hoặc gói cao hơn
        $currentSubscription = null;
        if ($user['subscription_id']) {
            $currentSubscription = $db->fetch("SELECT * FROM subscriptions WHERE id = ?", [$user['subscription_id']]);
        }
        
        if ($currentSubscription) {
            $currentPrice = floatval($currentSubscription['price']);
            $newPrice = floatval($subscription['price']);
            
            // Nếu gói mới rẻ hơn hoặc bằng gói hiện tại
            if ($newPrice <= $currentPrice) {
                $_SESSION['error'] = 'Bạn đã có gói tương đương hoặc cao hơn!';
                $this->redirect('profile');
                return;
            }
        }
        
        // Tính số điểm cần (giá gói = số điểm)
        $requiredPoints = intval($subscription['price']);
        $userPoints = $user['points'] ?? 0;
        
        if ($userPoints < $requiredPoints) {
            $_SESSION['error'] = "Bạn không đủ điểm! Cần {$requiredPoints} điểm, hiện có {$userPoints} điểm.";
            $this->redirect('profile');
            return;
        }
        
        // Trừ điểm và cập nhật gói
        $userModel->deductPoints($user['id'], $requiredPoints);
        $db->execute("UPDATE users SET subscription_id = ? WHERE id = ?", [$subscriptionId, $user['id']]);
        
        // Tạo transaction record
        require_once __DIR__ . '/TransactionModel.php';
        $transactionModel = new TransactionModel();
        $transactionModel->create([
            'user_id' => $user['id'],
            'type' => 'subscription',
            'related_id' => $subscriptionId,
            'amount' => $requiredPoints,
            'method' => 'Points',
            'status' => 'Thành công'
        ]);
        
        $_SESSION['success'] = "Nâng cấp gói {$subscription['name']} thành công! Đã trừ {$requiredPoints} điểm.";
        $this->redirect('profile');
    }
    
    public function update() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->getCurrentUser();
            $userModel = new UserModel();
            
            $avatarPath = $user['avatar'] ?? null;
            
            // Xử lý upload avatar
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../data/avatars/';
                
                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Kiểm tra loại file
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = $_FILES['avatar']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    $_SESSION['error'] = 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)!';
                    $this->redirect('profile');
                    return;
                }
                
                // Kiểm tra kích thước file (tối đa 5MB)
                $maxSize = 5 * 1024 * 1024; // 5MB
                if ($_FILES['avatar']['size'] > $maxSize) {
                    $_SESSION['error'] = 'Kích thước file quá lớn! Tối đa 5MB.';
                    $this->redirect('profile');
                    return;
                }
                
                // Tạo tên file duy nhất
                $fileExtension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $fileName = 'avatar_' . $user['id'] . '_' . time() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;
                
                // Upload file
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
                    // Xóa avatar cũ nếu có
                    if ($avatarPath && file_exists(__DIR__ . '/../../' . $avatarPath)) {
                        @unlink(__DIR__ . '/../../' . $avatarPath);
                    }
                    
                    $avatarPath = 'data/avatars/' . $fileName;
                } else {
                    $_SESSION['error'] = 'Lỗi khi upload ảnh đại diện!';
                    $this->redirect('profile');
                    return;
                }
            }
            
            $userModel->update($user['id'], [
                'name' => $_POST['name'] ?? $user['name'],
                'email' => $_POST['email'] ?? $user['email'],
                'birthdate' => $_POST['birthdate'] ?? $user['birthdate'],
                'avatar' => $avatarPath
            ]);
            
            $_SESSION['success'] = 'Cập nhật thông tin thành công!';
            $this->redirect('profile');
        }
        
        $this->redirect('profile');
    }
    
    /**
     * Upload avatar riêng (AJAX)
     */
    public function uploadAvatar() {
        $this->requireLogin();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $user = $this->getCurrentUser();
        $userModel = new UserModel();
        
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Không có file được upload hoặc file bị lỗi!']);
            return;
        }
        
        $uploadDir = __DIR__ . '/../../data/avatars/';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Kiểm tra loại file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['avatar']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)!']);
            return;
        }
        
        // Kiểm tra kích thước file (tối đa 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($_FILES['avatar']['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'Kích thước file quá lớn! Tối đa 5MB.']);
            return;
        }
        
        // Tạo tên file duy nhất
        $fileExtension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $fileName = 'avatar_' . $user['id'] . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;
        
        // Upload file
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
            // Xóa avatar cũ nếu có
            $oldAvatar = $user['avatar'] ?? null;
            if ($oldAvatar && file_exists(__DIR__ . '/../../' . $oldAvatar)) {
                @unlink(__DIR__ . '/../../' . $oldAvatar);
            }
            
            $avatarPath = 'data/avatars/' . $fileName;
            
            // Cập nhật database
            $userModel->update($user['id'], [
                'name' => $user['name'],
                'email' => $user['email'],
                'birthdate' => $user['birthdate'],
                'avatar' => $avatarPath
            ]);
            
            $baseUrl = defined('BASE_URL') ? BASE_URL : 'http://localhost/DuAn1/';
            echo json_encode([
                'success' => true, 
                'message' => 'Cập nhật ảnh đại diện thành công!',
                'avatar_url' => $baseUrl . $avatarPath
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi upload ảnh đại diện!']);
        }
    }
}
?>

