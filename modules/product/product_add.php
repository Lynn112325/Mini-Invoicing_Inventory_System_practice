<?php
require_once '../../includes/db.php';
require_once '../../includes/header.php';
require_once 'product_validate.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

$errors = [];
$success = false;
$product = []; // Empty for "Add"

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = $_POST; // Capture for sticky form

    $errors = validateProduct($_POST, $pdo);

    if (empty($errors)) {
        $sql = "INSERT INTO products (sku, name, category_id, stock_quantity, unit_price, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";

        $pdo->prepare($sql)->execute([
            $_POST['sku'],
            $_POST['name'],
            $_POST['category_id'],
            $_POST['stock_quantity'],
            $_POST['unit_price']
        ]);
        $success = true;
        $product = []; // Clear form on success

        $_SESSION['toast_success'] = "Product created successfully!";

        header("Location: products.php");
        exit;
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Add New Product</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php include '_form.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>