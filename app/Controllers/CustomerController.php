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
            'search' => $_GET['search'] ?? '',
            'customer_level'  => $_GET['level'] ?? '',
            'credit_min' => $_GET['credit_min'] ?? '',
            'credit_max' => $_GET['credit_max'] ?? '',
        ];

        $result = $this->customerModel->getPaginated($filters);
        // print_r($result);
        // die();
        extract($result);
        $customers = $data;

        require '../app/Views/layout/header.php';
        require '../app/Views/partner/customer/index.php';
        require '../app/Views/layout/footer.php';
    }
}
