<?php
class SupplierController
{
    private $supplierModel;

    public function __construct($db)
    {
        $this->supplierModel = new Supplier($db);
    }

    public function index()
    {
        $order = isset($_GET['order']) && strtolower($_GET['order']) == 'asc' ? 'ASC' : 'DESC';
        $filters = [
            'tax_id' => $_GET['tax_id'] ?? '',
            'payment_terms' => $_GET['payment_terms'] ?? '',
            'bank_account' => $_GET['bank_account'] ?? ''
        ];
        $search = $_GET['search'] ?? '';
        $paginationParams = [
            'page'  => (int)($_GET['page'] ?? 1),
            'sort'  => $_GET['sort'] ?? 'name',
            'order' => strtolower($_GET['order'] ?? 'asc'),
        ];

        $result = $this->supplierModel->getPaginated($search, $filters, $paginationParams);
        // print_r($result);
        // die();
        extract($result);
        $suppliers = $data;

        require '../app/Views/layout/header.php';
        require '../app/Views/partner/supplier/index.php';
        require '../app/Views/layout/footer.php';
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
            $this->supplierModel->softDelete($_POST['delete_id']);
            $_SESSION['toast_success'] = "Supplier deleted successfully.";
        }
        header("Location: ?route=supplier_list");
        exit;
    }
}
