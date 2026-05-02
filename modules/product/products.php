<?php
require_once '../../includes/db.php';
require_once '../../includes/header.php';
require_once '../../includes/functions/pagination_helper.php';

// fetch categories for filter dropdown
$stmtCat = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmtCat->fetchAll();

$conditions = ["p.deleted_at IS NULL"];
$params = [];

// set up filterable fields and their corresponding SQL conditions
$filters = [
    'cat'   => ['col' => 'p.category_id',    'type' => 'int'],
    's_min' => ['col' => 'p.stock_quantity', 'op' => '>=', 'type' => 'int'],
    's_max' => ['col' => 'p.stock_quantity', 'op' => '<=', 'type' => 'int'],
    'p_min' => ['col' => 'p.unit_price',     'op' => '>=', 'type' => 'float'],
    'p_max' => ['col' => 'p.unit_price',     'op' => '<=', 'type' => 'float'],
];

foreach ($filters as $key => $val) {
    if (isset($_GET[$key]) && $_GET[$key] !== '') {
        $op = $val['op'] ?? '='; // operation defaults to '=' if not specified
        $paramName = "filter_" . $key; // unique parameter name to avoid conflicts
        $conditions[] = "{$val['col']} {$op} :{$paramName}";

        $params[$paramName] = ($val['type'] === 'int') ? (int)$_GET[$key] : (float)$_GET[$key];
    }
}

// construct the FROM and WHERE part of the SQL query based on filters
$fromWhereSql = "FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE " . implode(" AND ", $conditions);

// soft delete handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("UPDATE products SET deleted_at = NOW() WHERE id = ?");
    if ($stmt->execute([$_POST['delete_id']])) {
        $_SESSION['toast_success'] = "Product deleted successfully.";
        header("Location: products.php");
        exit;
    }
}

// 1. Define base SQL components for pagination, searching, and sorting
$selectSql = "SELECT p.*, c.name as category_name";

// 2. Define searchable columns and allowed sorting options
$searchColumns = ['p.name', 'p.sku'];
$allowedSort = ['sku', 'name', 'category_name', 'stock_quantity', 'unit_price', 'p.id'];

// 3. Get paginated data based on current search, sort, and pagination parameters
$result = getPaginatedData($pdo, $selectSql, $fromWhereSql, $searchColumns, $allowedSort, 'p.id', $params);

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
    <!-- filter -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body ">
            <form method="GET" id="filterForm">
                <!-- Search Box -->
                <div class="row g-2 mb-3">
                    <div class="col-md-10">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by Product Name or SKU..."
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-funnel-fill me-1"></i> Apply Filter
                        </button>
                    </div>
                </div>
                <!-- Advanced Filters -->
                <div class="row g-2 align-items-end">
                    <!-- Category -->
                    <div class="col-md-3">
                        <label class="form-label mb-1 fw-bold" style="font-size: 0.75rem;">CATEGORY</label>
                        <select name="cat" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= (($_GET['cat'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Stock Range -->
                    <div class="col-md-4">
                        <label class="form-label mb-1 fw-bold" style="font-size: 0.75rem;">STOCK RANGE</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="s_min" class="form-control" placeholder="Min" value="<?= htmlspecialchars($_GET['s_min'] ?? '') ?>">
                            <span class="input-group-text border-start-0 border-end-0 bg-transparent">-</span>
                            <input type="number" name="s_max" class="form-control" placeholder="Max" value="<?= htmlspecialchars($_GET['s_max'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="col-md-4">
                        <label class="form-label mb-1 fw-bold" style="font-size: 0.75rem;">PRICE RANGE ($)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" name="p_min" class="form-control" placeholder="Min" value="<?= htmlspecialchars($_GET['p_min'] ?? '') ?>">
                            <span class="input-group-text border-start-0 border-end-0 bg-transparent">-</span>
                            <input type="number" step="0.01" name="p_max" class="form-control" placeholder="Max" value="<?= htmlspecialchars($_GET['p_max'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Reset Link -->
                    <div class="col-md-1 text-center">
                        <a href="products.php" class="btn btn-sm btn-link text-decoration-none text-danger p-0 mb-1">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
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