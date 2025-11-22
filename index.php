<?php
// Khởi động session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload classes
spl_autoload_register(function ($class) {
    // Core classes
    $corePath = __DIR__ . '/core/' . $class . '.php';
    if (file_exists($corePath)) {
        require_once $corePath;
        return;
    }
    
    // Module classes (controllers, models)
    $modules = ['movie', 'user', 'booking', 'review', 'admin', 'home'];
    foreach ($modules as $module) {
        $controllerPath = __DIR__ . '/modules/' . $module . '/' . $class . '.php';
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            return;
        }
    }
    
    // Fallback: old structure (for backward compatibility)
    $oldPaths = [
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php'
    ];
    
    foreach ($oldPaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Router đơn giản
$route = $_GET['route'] ?? 'home/index';
$parts = explode('/', $route);

// Nếu route rỗng hoặc chỉ có '/', mặc định về home/index
if (empty($route) || $route === '/' || (count($parts) === 1 && empty($parts[0]))) {
    $route = 'home/index';
    $parts = explode('/', $route);
}

// Map các route đặc biệt cho controller name
$controllerMap = [
    'auth' => 'AuthController',  // auth -> AuthController trong user module
    'profile' => 'ProfileController'  // profile -> ProfileController trong user module
];

$firstPart = $parts[0];
$controllerName = null;

// Kiểm tra xem có controller đặc biệt không
if (isset($controllerMap[$firstPart])) {
    $controllerName = $controllerMap[$firstPart];
    // Load controller từ user module
    $controllerPath = __DIR__ . '/modules/user/' . $controllerName . '.php';
    if (file_exists($controllerPath) && !class_exists($controllerName)) {
        require_once $controllerPath;
    }
} else {
    $controllerName = ucfirst($firstPart) . 'Controller';
}

// Xử lý nested routes (ví dụ: admin/theaters/create -> theatersCreate)
$action = '';
if (count($parts) >= 3) {
    // Nested route: admin/theaters/create -> theatersCreate
    // Xử lý dấu gạch ngang trong action: update-status -> updateStatus
    $actionPart = $parts[2];
    // Convert kebab-case to camelCase: update-status -> UpdateStatus
    $actionPart = str_replace('-', ' ', $actionPart);
    $actionPart = ucwords($actionPart);
    $actionPart = str_replace(' ', '', $actionPart); // UpdateStatus
    $action = $parts[1] . $actionPart; // supportUpdateStatus
} else {
    $action = $parts[1] ?? 'index';
}

// Kiểm tra controller tồn tại
if (!class_exists($controllerName)) {
    // Nếu controller được map từ route đặc biệt, thử load từ module
    if (isset($controllerMap[$firstPart])) {
        $moduleForMap = 'user';
        $controllerPath = __DIR__ . '/modules/' . $moduleForMap . '/' . $controllerName . '.php';
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
        }
    }
    
    // Nếu vẫn không tồn tại, fallback về HomeController
    if (!class_exists($controllerName)) {
        $controllerName = 'HomeController';
        $action = 'index';
    }
}

// Kiểm tra action tồn tại
try {
    $controller = new $controllerName();
    if (!method_exists($controller, $action)) {
        // Fallback: thử với action đơn giản (theaters/create -> theaters)
        if (count($parts) >= 2 && !empty($parts[1])) {
            $action = $parts[1];
        } else {
            $action = 'index';
        }
        
        if (!method_exists($controller, $action)) {
            // Nếu vẫn không có, dùng HomeController
            error_log("Method $action not found in $controllerName, falling back to HomeController");
            $controllerName = 'HomeController';
            $controller = new $controllerName();
            $action = 'index';
        }
    }
    
    // Gọi action
    $controller->$action();
} catch (Exception $e) {
    // Log lỗi để debug
    error_log("Error in routing: " . $e->getMessage());
    error_log("Route: $route, Controller: $controllerName, Action: $action");
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Fallback về HomeController nếu có lỗi
    if ($controllerName !== 'HomeController') {
        $controllerName = 'HomeController';
        $controller = new $controllerName();
        $controller->index();
    } else {
        die('Error: Controller not found');
    }
}
?>
