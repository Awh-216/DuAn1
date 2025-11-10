<?php
// Khởi động session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/core/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Router đơn giản
$route = $_GET['route'] ?? 'home/index';
$parts = explode('/', $route);

$controllerName = ucfirst($parts[0]) . 'Controller';
$action = $parts[1] ?? 'index';

// Kiểm tra controller tồn tại
if (!class_exists($controllerName)) {
    $controllerName = 'HomeController';
    $action = 'index';
}

// Kiểm tra action tồn tại
$controller = new $controllerName();
if (!method_exists($controller, $action)) {
    $action = 'index';
}

// Gọi action
$controller->$action();
?>
