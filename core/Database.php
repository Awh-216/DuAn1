<?php
class Database {
    private static $instance = null;
    private $pdo;
    
    // ============================================
    // CẤU HÌNH DATABASE - LOCAL (XAMPP)
    // ============================================
    private $host = 'localhost';
    private $port = 3306;        // XAMPP: 3306, MAMP: 8889
    private $dbname = 'cinehub';
    private $username = 'root';
    private $password = '';      // XAMPP: '' (rỗng), MAMP: 'root'
    private $charset = 'utf8mb4';
    
    // ============================================
    // CẤU HÌNH DATABASE - HOSTING (uncomment khi upload)
    // ============================================
    // private $host = 'localhost';
    // private $port = 3306;
    // private $dbname = 'cinehub';
    // private $username = 'cinehub';
    // private $password = 'Cinehub1234@';
    // private $charset = 'utf8mb4';



    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Lỗi kết nối database: " . $e->getMessage() . 
                "<br><br>Vui lòng kiểm tra:<br>" .
                "1. MySQL đã được khởi động chưa?<br>" .
                "2. Database '{$this->dbname}' đã được tạo chưa?<br>" .
                "3. Password MySQL có đúng không? (hiện tại: " . ($this->password ?: 'rỗng') . ")<br>" .
                "4. Port MySQL có đúng không? (hiện tại: {$this->port})<br>" .
                "5. Nếu cần thay đổi password hoặc port, sửa file core/Database.php");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function execute($sql, $params = []) {
        return $this->query($sql, $params);
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
?>
