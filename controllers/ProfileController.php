<?php
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
        
        // Số dư (tạm thời để 0, có thể thêm trường balance vào DB sau)
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

