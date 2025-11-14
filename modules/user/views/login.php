<?php
$current_page = 'auth';
$title = 'Đăng nhập';
?>

<section class="auth-section-new">
    <div class="background_login"></div>
    <div class="auth-container-new">
        <div class="auth-card">
            <!-- Left Column: Login Form -->
            <div class="auth-form-column">
                <div class="auth-form-wrapper">
                    <h1 class="auth-title">LOGIN HIRE</h1>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="http://localhost/DuAn1/?route=auth/login" class="auth-form-new">
                        <div class="form-group-new">
                            <input type="email" name="email" required placeholder="Email" class="input-field">
                        </div>
                        
                        <div class="form-group-new">
                            <input type="password" name="password" required placeholder="PassWord" class="input-field">
                        </div>
                        
                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" name="remember_me">
                                <span>Remember Me</span>
                            </label>
                            <a href="#" class="forgot-password">Forgot Password?</a>
                        </div>
                        
                        <button type="submit" class="btn-login">Login</button>
                        
                        <div class="social-login">
                            <p class="social-text">Of use your account</p>
                            <div class="social-icons">
                                <a href="#" class="social-icon facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-icon google">
                                    <i class="fab fa-google"></i>
                                </a>
                                <a href="#" class="social-icon other">
                                    <i class="fas fa-paw"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Right Column: Promotional Panel -->
            <div class="auth-promo-column">
                <div class="promo-content">
                    <h2 class="promo-title">Start your journey now</h2>
                    <p class="promo-text">
                        Cứ bảo học không khó ừ thì mở máy ra code đi rồi mình nói chuyện<br>
                        Mỗi lần mở máy ra trái tim tôi lại mở đường cho giấc ngủ
                    </p>
                    <a href="http://localhost/DuAn1/?route=auth/register" class="btn-register">Resister</a>
                </div>
            </div>
        </div>
    </div>
</section>
