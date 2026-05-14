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
        $filters = [
            'cat'   => $_GET['cat'] ?? '',
            's_min' => $_GET['s_min'] ?? '',
            's_max' => $_GET['s_max'] ?? '',
            'p_min' => $_GET['p_min'] ?? '',
            'p_max' => $_GET['p_max'] ?? '',
        ];
        $paginationParams = [
            'page'  => (int)($_GET['page'] ?? 1),
            'sort'  => $_GET['sort'] ?? 'name',
            'order' => strtolower($_GET['order'] ?? 'asc'),
        ];

        $result = $this->productModel->getPaginated($searchTerm, $filters, $paginationParams);

        // print_r($result);
        // die();

        extract($result);
        $products = $data;
        $categories = $this->productModel->getCategories();

        require '../app/Views/layout/header.php';
        require '../app/Views/product/index.php';
        require '../app/Views/layout/footer.php';
    }

    public function create()
    {
        $this->renderForm();
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->productModel->validate($_POST);

            if (empty($errors)) {
                $this->productModel->save($_POST);
                $_SESSION['toast_success'] = "Product created successfully.";
                header("Location: ?route=product_list");
                exit;
            }

            $this->renderForm($_POST, $errors);
        }
    }

    private function renderForm($product = [], $errors = [], $is_edit = false)
    {
        $categories = $this->productModel->getCategories();

        $id = $_GET['id'] ?? '';
        $formAction = $is_edit ? "?route=product_edit&id=$id" : "?route=product_add";

        require '../app/Views/layout/header.php';
        require '../app/Views/product/' . ($is_edit ? 'edit.php' : 'create.php');
        require '../app/Views/layout/footer.php';
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: ?route=product_list");
            exit;
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            die("Product not found.");
        }

        $this->renderForm($product, [], true);
    }

    public function update()
    {
        $id = $_GET['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $errors = $this->productModel->validate($_POST, $id);

            if (empty($errors)) {
                $this->productModel->update($id, $_POST);
                $_SESSION['toast_success'] = "Product updated successfully!";
                header("Location: ?route=product_list");
                exit;
            }
            $this->renderForm($_POST, $errors, true);
        }
    }
}
