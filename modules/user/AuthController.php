<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/UserModel.php';

class AuthController extends Controller {
    
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $userModel = new UserModel();
            $user = $userModel->getByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
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
                
                if ($isAdmin) {
                    $this->redirect('?route=admin/index');
                } else {
                    $this->redirect('');
                }
            } else {
                $error = 'Email hoặc mật khẩu không đúng!';
                $this->view('auth/login', ['error' => $error]);
                return;
            }
        }
        
        $this->view('auth/login');
    }
    
    public function register() {
        if ($this->isLoggedIn()) {
            $this->redirect('');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if ($password !== $confirm_password) {
                $error = 'Mật khẩu xác nhận không khớp!';
                $this->view('auth/register', ['error' => $error]);
                return;
            }
            
            $userModel = new UserModel();
            $existingUser = $userModel->getByEmail($email);
            
            if ($existingUser) {
                $error = 'Email đã được sử dụng!';
                $this->view('auth/register', ['error' => $error]);
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
            $this->redirect('');
        }
        
        $this->view('auth/register');
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        $this->redirect('?route=home/index');
    }
}
?>

