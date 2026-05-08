<?php
require_once '../config/db.php';
require_once '../app/Controllers/ProductController.php';
require_once '../app/Controllers/UserController.php';
require_once '../app/Controllers/AuthController.php';
require_once '../app/Controllers/DashboardController.php';

$route = $_GET['route'] ?? 'dashboard';
$auth = new AuthController($pdo);

session_start();
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

    case 'product_list':
        $controller = new ProductController($pdo);
        $controller->index();
        break;

    case 'product_add':
        $controller = new ProductController($pdo);
        // $controller->create();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
