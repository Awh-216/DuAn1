    <?php 
    // Kiểm tra xem có phải trang login/register không (sử dụng lại logic từ header)
    $isAuthPage = false;
    if (isset($current_page) && $current_page === 'auth') {
        $isAuthPage = true;
    } elseif (isset($_GET['route']) && (strpos($_GET['route'], 'auth/login') !== false || strpos($_GET['route'], 'auth/register') !== false)) {
        $isAuthPage = true;
    }
    ?>
    
    <?php if (!$isAuthPage): ?>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-film"></i>
                        <span>CineHub</span>
                    </div>
                    <p class="footer-description">
                        Nền tảng xem phim trực tuyến hàng đầu Việt Nam. 
                        Xem phim chất lượng cao, không giới hạn với nhiều thể loại đa dạng.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="social-link" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Danh mục</h3>
                    <ul class="footer-links">
                        <li><a href="http://localhost/DuAn1/?route=movie/index">Phim mới</a></li>
                        <li><a href="http://localhost/DuAn1/?route=movie/index">Phim hot</a></li>
                        <li><a href="http://localhost/DuAn1/?route=movie/index">Phim lẻ</a></li>
                        <li><a href="http://localhost/DuAn1/?route=movie/index">Phim bộ</a></li>
                        <li><a href="http://localhost/DuAn1/?route=movie/index">Phim hoạt hình</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Thể loại</h3>
                    <ul class="footer-links">
                        <li><a href="http://localhost/DuAn1/?route=movie/index&category=1">Hành động</a></li>
                        <li><a href="http://localhost/DuAn1/?route=movie/index&category=2">Tình cảm</a></li>
                        <li><a href="http://localhost/DuAn1/?route=movie/index&category=3">Hài</a></li>
                        <li><a href="http://localhost/DuAn1/?route=movie/index&category=4">Kinh dị</a></li>
                        <li><a href="http://localhost/DuAn1/?route=movie/index&category=5">Hoạt hình</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Hỗ trợ</h3>
                    <ul class="footer-links">
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                        <li><a href="#">Điều khoản sử dụng</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="http://localhost/DuAn1/?route=booking/index">Đặt vé xem phim</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> CineHub. Tất cả quyền được bảo lưu.</p>
                <div class="footer-payment">
                    <span>Chấp nhận thanh toán:</span>
                    <i class="fab fa-cc-visa" title="Visa"></i>
                    <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                    <i class="fas fa-mobile-alt" title="Momo"></i>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

