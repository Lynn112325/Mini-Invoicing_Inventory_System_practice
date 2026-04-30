<?php
require_once '../../includes/db.php';
require_once '../../includes/header.php';
require_once '../../includes/functions/pagination_helper.php';

// Handle Soft Delete Action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("UPDATE products SET deleted_at = NOW() WHERE id = ?");
    if ($stmt->execute([$delete_id])) {
        echo "<div class='alert alert-success'>Product deleted successfully.</div>";
    }
}

// 1. Define base SQL components for pagination, searching, and sorting
$selectSql = "SELECT p.*, c.name as category_name";
$fromWhereSql = "FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.deleted_at IS NULL";

// 2. Define searchable columns and allowed sorting options
$searchColumns = ['p.name', 'p.sku'];
$allowedSort = ['sku', 'name', 'category_name', 'stock_quantity', 'unit_price', 'p.id'];

// 3. Get paginated data based on current search, sort, and pagination parameters
$result = getPaginatedData($pdo, $selectSql, $fromWhereSql, $searchColumns, $allowedSort, 'p.id');

// 4. Extract data and metadata for use in the HTML below
$products = $result['data'];
$total_pages = $result['total_pages'];
$current_page = $result['current_page'];
$current_sort = $result['current_sort'];
$current_order = $result['current_order'];
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Product Management</h2>
        <a href="product_add.php" class="btn btn-primary">+ Add New Product</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Search by Product Name or SKU..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <?php echo renderSortHeader('SKU', 'sku', $current_sort, $current_order); ?>
                        <?php echo renderSortHeader('Product Name', 'name', $current_sort, $current_order); ?>
                        <?php echo renderSortHeader('Category', 'category_name', $current_sort, $current_order); ?>
                        <?php echo renderSortHeader('Stock', 'stock_quantity', $current_sort, $current_order); ?>
                        <?php echo renderSortHeader('Unit Price', 'unit_price', $current_sort, $current_order); ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><strong><?php echo $product['sku']; ?></strong></td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['category_name'] ?? 'N/A'; ?></td>
                                <td><?php echo $product['stock_quantity']; ?></td>
                                <td>$<?php echo number_format($product['unit_price'], 2); ?></td>
                                <td>
                                    <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-info">Edit</a>
                                    <a href="products.php?delete_id=<?php echo $product['id']; ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                </tbody>
            </table>

            <?php echo renderPagination($total_pages, $current_page); ?>

            <div class="text-muted small">
                Showing <?php echo count($products); ?> of <?php echo $result['total_results']; ?> products
            </div>

        </div>
    </div>
</div>
<?php
if (isset($_SESSION['toast_success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast("<?= $_SESSION['toast_success'] ?>");
        });
    </script>
<?php
    unset($_SESSION['toast_success']);
endif; ?>
<?php require_once '../../includes/footer.php'; ?>