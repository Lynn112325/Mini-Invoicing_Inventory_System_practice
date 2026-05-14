<?php
class CustomerController
{
    private $customerModel;

    public function __construct($db)
    {
        $this->customerModel = new Customer($db);
    }

    public function index()
    {
        $order = isset($_GET['order']) && strtolower($_GET['order']) == 'asc' ? 'ASC' : 'DESC';
        $filters = [
            'customer_level'  => $_GET['level'] ?? '',
            'credit_min' => $_GET['c_min'] ?? '',
            'credit_max' => $_GET['c_max'] ?? '',
        ];
        $search = $_GET['search'] ?? '';
        $paginationParams = [
            'page'  => (int)($_GET['page'] ?? 1),
            'sort'  => $_GET['sort'] ?? 'name',
            'order' => strtolower($_GET['order'] ?? 'asc'),
        ];

        $result = $this->customerModel->getPaginated($search, $filters, $paginationParams);
        // print_r($result);
        // die();
        extract($result);
        $customers = $data;

        require '../app/Views/layout/header.php';
        require '../app/Views/partner/customer/index.php';
        require '../app/Views/layout/footer.php';
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
            $this->customerModel->softDelete($_POST['delete_id']);
            $_SESSION['toast_success'] = "Customer deleted successfully.";
        }
        header("Location: ?route=customer_list");
        exit;
    }
}
