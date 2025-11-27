<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/UserModel.php';

class AuthController extends Controller {
    
    public function login() {
        if ($this->isLoggedIn()) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'redirect' => '']);
                return;
            }
            $this->redirect('');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Kiểm tra IP spam
            require_once __DIR__ . '/../../core/IPSpamChecker.php';
            $ipCheck = IPSpamChecker::checkIPSpam(null, 'login');
            if (!$ipCheck['allowed']) {
                $error = $ipCheck['message'];
                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error]);
                    return;
                }
                $_SESSION['error'] = $error;
                $this->redirect('');
                return;
            }
            
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $userModel = new UserModel();
            $user = $userModel->getByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                // Tạo token cho user
                require_once __DIR__ . '/../../core/TokenHelper.php';
                $deviceInfo = TokenHelper::getDeviceInfo();
                $ipAddress = TokenHelper::getClientIp();
                $token = $userModel->createToken($user['id'], $deviceInfo, $ipAddress);
                
                // Lưu token vào session
                $_SESSION['auth_token'] = $token;
                
                // Kiểm tra nếu là admin thì redirect đến admin panel
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
                
                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'redirect' => $isAdmin ? '?route=admin/index' : ''
                    ]);
                    return;
                }
                
                // Log IP action thành công
                IPSpamChecker::logIPAction(null, 'login', false, "Đăng nhập thành công: $email", $user['id']);
                
                if ($isAdmin) {
                    $this->redirect('?route=admin/index');
                } else {
                    $this->redirect('');
                }
            } else {
                $error = 'Email hoặc mật khẩu không đúng!';
                
                // Log IP action thất bại (có thể là spam)
                $isSpam = !empty($email) && !empty($password); // Nếu có cả email và password nhưng sai
                IPSpamChecker::logIPAction(null, 'login', $isSpam, "Đăng nhập thất bại: $email", null);
                
                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error]);
                    return;
                }
                
                // Nếu không phải AJAX, redirect về trang chủ với modal login
                $_SESSION['error'] = $error;
                $this->redirect('');
                return;
            }
        }
        
        // Nếu GET request (không phải POST), redirect về trang chủ
        $this->redirect('');
    }
    
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    public function register() {
        if ($this->isLoggedIn()) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'redirect' => '']);
                return;
            }
            $this->redirect('');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Kiểm tra IP spam
            require_once __DIR__ . '/../../core/IPSpamChecker.php';
            $ipCheck = IPSpamChecker::checkIPSpam(null, 'register');
            if (!$ipCheck['allowed']) {
                $error = $ipCheck['message'];
                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error]);
                    return;
                }
                $_SESSION['error'] = $error;
                $this->redirect('');
                return;
            }
            
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if ($password !== $confirm_password) {
                // Log IP action (lỗi validation)
                IPSpamChecker::logIPAction(null, 'register', false, "Lỗi xác nhận mật khẩu: $email", null);
                
                $error = 'Mật khẩu xác nhận không khớp!';
                
                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error]);
                    return;
                }
                
                // Nếu không phải AJAX, redirect về trang chủ với modal register
                $_SESSION['error'] = $error;
                $this->redirect('');
                return;
            }
                $error = 'Mật khẩu xác nhận không khớp!';
                
                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error]);
                    return;
                }
                
                // Nếu không phải AJAX, redirect về trang chủ với modal register
                $_SESSION['error'] = $error;
                $this->redirect('');
                return;
            }
            
            $userModel = new UserModel();
            $existingUser = $userModel->getByEmail($email);
            
            if ($existingUser) {
                // Log IP action (email đã tồn tại - có thể là spam)
                IPSpamChecker::logIPAction(null, 'register', true, "Email đã tồn tại: $email", null);
                
                $error = 'Email đã được sử dụng!';
                
                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error]);
                    return;
                }
                
                // Nếu không phải AJAX, redirect về trang chủ với modal register
                $_SESSION['error'] = $error;
                $this->redirect('');
                return;
            }
            
            $user_id = $userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'subscription_id' => 1
            ]);
            
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            
            // Tạo token cho user mới đăng ký
            require_once __DIR__ . '/../../core/TokenHelper.php';
            $deviceInfo = TokenHelper::getDeviceInfo();
            $ipAddress = TokenHelper::getClientIp();
            $token = $userModel->createToken($user_id, $deviceInfo, $ipAddress);
            
            // Lưu token vào session
            $_SESSION['auth_token'] = $token;
            
            // Log IP action thành công
            IPSpamChecker::logIPAction(null, 'register', false, "Đăng ký thành công: $email", $user_id);
            
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'redirect' => '']);
                return;
            }
            
            $this->redirect('');
        }
        
        // Nếu GET request (không phải POST), redirect về trang chủ
        $this->redirect('');
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Xóa token khỏi database nếu có
        if (isset($_SESSION['auth_token'])) {
            $userModel = new UserModel();
            $userModel->deleteToken($_SESSION['auth_token']);
        }
        
        // Xóa session hoàn toàn
        $_SESSION = array(); // Xóa tất cả session variables
        
        // Xóa session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        // Ngăn cache để không thể quay lại trang cũ
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Ngày trong quá khứ
        
        $this->redirect('?route=home/index');
    }
    
    /**
     * Đăng xuất khỏi tất cả thiết bị
     */
    public function logoutAll() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Xóa tất cả token của user
        if (isset($_SESSION['user_id'])) {
            $userModel = new UserModel();
            $userModel->deleteAllUserTokens($_SESSION['user_id']);
        }
        
        // Xóa session hoàn toàn
        $_SESSION = array();
        
        // Xóa session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        // Ngăn cache
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        
        $this->redirect('?route=home/index');
    }
}
?>

