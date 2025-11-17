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
        
        // Xác định role
        $userRole = 'Thành viên';
        if (stripos($user['email'], 'admin') !== false) {
            $userRole = 'Admin tối cao';
        }
        
        // Số dư = điểm (1 VND = 1 điểm)
        $balance = $user['points'] ?? 0;
        
        // Lấy tất cả các gói subscription để hiển thị
        $allSubscriptions = $db->fetchAll("SELECT * FROM subscriptions ORDER BY price ASC");
        
        $this->view('profile/index', [
            'user' => $user,
            'history' => $history,
            'tickets' => $tickets,
            'subscription' => $subscription,
            'allSubscriptions' => $allSubscriptions,
            'userRole' => $userRole,
            'balance' => $balance
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
            
            $userModel->update($user['id'], [
                'name' => $_POST['name'] ?? $user['name'],
                'email' => $_POST['email'] ?? $user['email'],
                'birthdate' => $_POST['birthdate'] ?? $user['birthdate']
            ]);
            
            $_SESSION['success'] = 'Cập nhật thông tin thành công!';
            $this->redirect('profile');
        }
        
        $this->redirect('profile');
    }
}
?>

