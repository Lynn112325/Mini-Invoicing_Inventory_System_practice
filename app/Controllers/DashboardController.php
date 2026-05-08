<?php
require_once '../app/Models/Dashboard.php';
class DashboardController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new Dashboard($pdo);
    }

    public function index()
    {

        try {
            $stats = $this->model->getStats();
            extract($stats);
        } catch (PDOException $e) {
            $error = "Failed to fetch data: " . $e->getMessage();
        }

        require '../app/Views/layout/header.php';
        require '../app/Views/dashboard.php';
        require '../app/Views/layout/footer.php';
    }
}
