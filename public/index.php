<?php
require_once '../config/db.php';
require_once '../app/Controllers/ProductController.php';
require_once '../app/Controllers/UserController.php';
require_once '../app/Controllers/AuthController.php';
require_once '../app/Controllers/DashboardController.php';
session_start();

$route = $_GET['route'] ?? 'dashboard';
$auth = new AuthController($pdo);

switch ($route) {
    case 'login':
        $auth->showLogin();
        break;

    case 'login_process':
        $auth->login();
        break;

    case 'dashboard':
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?route=login");
            exit;
        }
        $controller = new DashboardController($pdo);
        $controller->index();
        break;
    case 'product_list':
        $controller = new ProductController();
        $controller->index();
        break;

    case 'product_add':
        $controller = new ProductController();
        $controller->create();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
