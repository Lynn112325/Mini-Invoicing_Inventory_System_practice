<?php
require_once '../config/db.php';
require_once '../app/Models/Partner/BasePartner.php';
require_once '../app/Models/Category.php';
require_once '../app/Models/Product.php';
require_once '../app/Models/Dashboard.php';
require_once '../app/Models/User.php';
require_once '../app/Controllers/ProductController.php';
require_once '../app/Controllers/AuthController.php';
require_once '../app/Controllers/DashboardController.php';
require_once '../app/Controllers/CategoryController.php';
require_once '../app/Controllers/CustomerController.php';
require_once '../app/Models/Partner/Customer.php';

$route = $_GET['route'] ?? 'dashboard';
$method = $_SERVER['REQUEST_METHOD'];
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
    // Product routes
    case 'product_list':
        $controller = new ProductController($pdo);
        $controller->index();
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

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
