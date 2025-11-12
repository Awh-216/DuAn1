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
        
        // Số dư
        $balance = 0;
        
        $this->view('profile/index', [
            'user' => $user,
            'history' => $history,
            'tickets' => $tickets,
            'subscription' => $subscription,
            'userRole' => $userRole,
            'balance' => $balance
        ]);
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

