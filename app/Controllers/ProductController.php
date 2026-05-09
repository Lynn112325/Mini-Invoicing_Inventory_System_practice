<?php
class ProductController
{
    private $productModel;

    public function __construct($pdo)
    {
        $this->productModel = new Product($pdo);
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
            $this->productModel->softDelete($_POST['delete_id']);
            $_SESSION['toast_success'] = "Product deleted successfully.";
            header("Location: ?route=product_list");
            exit;
        }
        $searchTerm = htmlspecialchars($_GET['search'] ?? '');
        $result = $this->productModel->getPaginated($_GET);

        // print_r(array_keys($result));
        // die();

        extract($result);
        $products = $data;
        $searchTerm = $searchTerm;
        $categories = $this->productModel->getCategories();

        require '../app/Views/layout/header.php';
        require '../app/Views/product/index.php';
        require '../app/Views/layout/footer.php';
    }

    public function create()
    {
        $categories = $this->productModel->getCategories();
        $product = [];
        $errors = [];

        require '../app/Views/layout/header.php';
        require '../app/Views/product/create.php';
        require '../app/Views/layout/footer.php';
    }

    // public function edit()
    // {
    //     if (!isset($_GET['id'])) {
    //         header("Location: ?route=product_list");
    //         exit;
    //     }

    //     $categories = $this->productModel->getCategories();
    //     $product = $this->productModel->findById($_GET['id']);
    //     if (!$product) {
    //         header("Location: ?route=product_list");
    //         exit;
    //     }
    //     $errors = [];

    //     require '../app/Views/layout/header.php';
    //     require '../app/Views/product/edit.php';
    //     require '../app/Views/layout/footer.php';
    // }

    // public function store()
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $errors = $this->productModel->validate($_POST);

    //         if (empty($errors)) {
    //             $this->productModel->save($_POST);
    //             $_SESSION['toast_success'] = "product is created successfully.";
    //             header("Location: ?route=product_list");
    //             exit;
    //         }

    //         $categories = $this->productModel->getCategories();
    //         $product = $_POST;
    //         require '../app/Views/layout/header.php';
    //         require '../app/Views/product/create.php';
    //         require '../app/Views/layout/footer.php';
    //     }
    // }
}
