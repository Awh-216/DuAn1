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
            <p>&copy; 2024 CineHub. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>
    <?php endif; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

