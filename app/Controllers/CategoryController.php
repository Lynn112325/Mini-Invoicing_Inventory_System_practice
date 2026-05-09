<?php
class CategoryController
{
    private $categoryModel;

    public function __construct($pdo)
    {
        $this->categoryModel = new Category($pdo);
    }

    public function storeAjax()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Name is required']);
            exit;
        }

        try {
            if ($this->categoryModel->exists($name)) {
                echo json_encode(['success' => false, 'message' => 'Category already exists']);
                exit;
            }

            $newId = $this->categoryModel->create($name);
            if ($newId) {
                echo json_encode([
                    'success' => true,
                    'id' => $newId,
                    'name' => $name
                ]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
        exit;
    }
}
