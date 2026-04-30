<?php
require_once '../../includes/db.php';
require_once '../../includes/header.php';
require_once 'product_validate.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: products.php");
    exit;
}

$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

$is_edit = true;
$errors = [];
$success = false;

// 1. Load existing data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found.");
}

// 2. Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = $_POST;

    $errors = validateProduct($_POST, $pdo, $id);

    if (empty($errors)) {
        $sql = "UPDATE products SET sku=?, name=?, category_id=?, stock_quantity=?, unit_price=? WHERE id=?";
        $pdo->prepare($sql)->execute([
            $_POST['sku'],
            $_POST['name'],
            $_POST['category_id'],
            $_POST['stock_quantity'],
            $_POST['unit_price'],
            $id
        ]);
        $success = true;

        $_SESSION['toast_success'] = "Product updated successfully!";
        header("Location: products.php");
        exit;
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>Edit Product: <?= htmlspecialchars($product['name']) ?></h2>
            <?php if ($success): ?>
                <div class="alert alert-success">Update successful! <a href="products.php">Back to list</a></div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <?php include '_form.php'; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success">Product created! <a href="products.php">Go to list</a></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../includes/header.php'; ?>