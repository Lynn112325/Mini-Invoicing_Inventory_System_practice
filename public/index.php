<?php
require_once '../config/db.php';

// Autoloader function to load classes from Controllers and Models directories
spl_autoload_register(function ($className) {
    // define possible directories to look for class files
    $dirs = [
        '../app/Controllers/',
        '../app/Models/',
        '../app/Models/Partner/',
    ];

    foreach ($dirs as $dir) {
        $file = $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return; // stop after loading the class
        }
    }
});

session_start();

$route = $_GET['route'] ?? 'dashboard';
$method = $_SERVER['REQUEST_METHOD'];

$auth = new AuthController($pdo);

$publicRoutes = ['login', 'login_process'];

if (!isset($_SESSION['user_id']) && !in_array($route, $publicRoutes)) {
    header("Location: ?route=login");
    exit;
}

switch ($route) {
    case 'login':
        $auth->showLogin();
        break;

    case 'logout':
        $auth->logout();
        break;

    case 'login_process':
        $auth->login();
        break;

    case 'dashboard':
        $controller = new DashboardController($pdo);
        $controller->index();
        break;
    // Product routes
    case 'product_list':
        $controller = new ProductController($pdo);
        $controller->index();
        break;

    case 'product_delete':
        $controller = new ProductController($pdo);
        $controller->delete();
        break;

    case 'product_add':
        $controller = new ProductController($pdo);
        if ($method === 'POST') {
            $controller->store();
        } else {
            $controller->create();
        }
        break;

    case 'product_edit':
        $controller = new ProductController($pdo);
        if ($method === 'POST') {
            $controller->update();
        } else {
            $controller->edit();
        }
        break;

    case 'category_add_ajax':
        $controller = new CategoryController($pdo);
        $controller->storeAjax();
        break;
    // Customer routes
    case 'customer_list':
        $controller = new CustomerController($pdo);
        $controller->index();
        break;

    case 'customer_delete':
        $controller = new CustomerController($pdo);
        $controller->delete();
        break;

    case 'customer_add':
        $controller = new CustomerController($pdo);
        if ($method === 'POST') {
            $controller->store();
        } else {
            $controller->create();
        }

        // Supplier routes
    case 'supplier_list':
        $controller = new SupplierController($pdo);
        $controller->index();
        break;
    case 'supplier_delete':
        $controller = new SupplierController($pdo);
        $controller->delete();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
